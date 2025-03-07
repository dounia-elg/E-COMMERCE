<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())->first();
        
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add products before checkout.');
        }
        
        // Check if products are still available
        foreach ($cart->items as $item) {
            if ($item->quantity > $item->product->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "'{$item->product->name}' is no longer available in the requested quantity.");
            }
        }
        
        $user = Auth::user();
        
        return view('checkout.index', compact('cart', 'user'));
    }
    
    public function process(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'country' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:stripe,paypal',
        ]);
        
        $cart = Cart::where('user_id', Auth::id())->first();
        
        if (!$cart || $cart->items->count() === 0) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add products before checkout.');
        }
        
        // Calculate totals
        $subtotal = 0;
        $tax = 0;
        $shipping = 10.00; // Fixed shipping fee
        
        foreach ($cart->items as $item) {
            // Check stock availability
            if ($item->quantity > $item->product->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', "'{$item->product->name}' is no longer available in the requested quantity.");
            }
            
            $price = $item->product->discounted_price;
            $subtotal += $price * $item->quantity;
        }
        
        // Calculate tax (10%)
        $tax = $subtotal * 0.10;
        $total = $subtotal + $tax + $shipping;
        
        try {
            DB::beginTransaction();
            
            // Update user details
            $user = Auth::user();
            if ($user->userDetail) {
                $user->userDetail->update([
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'country' => $request->country,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                ]);
            } else {
                $user->userDetail()->create([
                    'address' => $request->address,
                    'city' => $request->city,
                    'state' => $request->state,
                    'country' => $request->country,
                    'postal_code' => $request->postal_code,
                    'phone' => $request->phone,
                ]);
            }
            
            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'total_amount' => $total,
                'shipping_cost' => $shipping,
                'tax_amount' => $tax,
                'shipping_address' => $request->address,
                'shipping_city' => $request->city,
                'shipping_state' => $request->state,
                'shipping_country' => $request->country,
                'shipping_postal_code' => $request->postal_code,
                'shipping_phone' => $request->phone,
                'notes' => $request->notes,
                'payment_method' => $request->payment_method,
                'is_paid' => false,
            ]);
            
            // Create order items
            foreach ($cart->items as $item) {
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                    'discount' => $item->product->discount_percent 
                        ? ($item->product->price * $item->product->discount_percent / 100) 
                        : 0
                ]);
                
                // Reduce product quantity
                $product = $item->product;
                $product->quantity -= $item->quantity;
                $product->save();
            }
            
            // Clear cart
            $cart->items()->delete();
            
            DB::commit();
            
            // Process payment
            if ($request->payment_method === 'stripe') {
                return $this->processStripePayment($order);
            } else {
                // For PayPal, we'll redirect to a page that will initiate PayPal payment
                return redirect()->route('checkout.paypal', ['order' => $order->id]);
            }
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('checkout.index')
                ->with('error', 'An error occurred while processing your order: ' . $e->getMessage());
        }
    }
    
    public function processStripePayment(Order $order)
    {
        // Set Stripe API key
        Stripe::setApiKey(config('services.stripe.secret'));
        
        $lineItems = [];
        
        foreach ($order->items as $item) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $item->product->name,
                    ],
                    'unit_amount' => round(($item->price - $item->discount) * 100), // In cents
                ],
                'quantity' => $item->quantity,
            ];
        }
        
        // Add shipping and tax as separate line items
        if ($order->shipping_cost > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Shipping',
                    ],
                    'unit_amount' => round($order->shipping_cost * 100), // In cents
                ],
                'quantity' => 1,
            ];
        }
        
        if ($order->tax_amount > 0) {
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Tax',
                    ],
                    'unit_amount' => round($order->tax_amount * 100), // In cents
                ],
                'quantity' => 1,
            ];
        }
        
        // Create Stripe checkout session
        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('checkout.success', ['order' => $order->id]),
            'cancel_url' => route('checkout.cancel', ['order' => $order->id]),
            'metadata' => [
                'order_id' => $order->id
            ],
        ]);
        
        // Update order with payment ID
        $order->update([
            'payment_id' => $session->id
        ]);
        
        // Redirect to Stripe checkout
        return redirect($session->url);
    }
    
    public function success(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Update order status
        $order->update([
            'status' => 'processing',
            'is_paid' => true,
            'paid_at' => now(),
        ]);
        
        return view('checkout.success', compact('order'));
    }
    
    public function cancel(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Return products to stock
        foreach ($order->items as $item) {
            $product = $item->product;
            $product->quantity += $item->quantity;
            $product->save();
        }
        
        // Update order status
        $order->update([
            'status' => 'declined'
        ]);
        
        return redirect()->route('checkout.index')
            ->with('error', 'Your payment was cancelled.');
    }
    
    public function paypal(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Implement PayPal integration here
        // For this example, we'll just simulate a successful payment
        
        return view('checkout.paypal', compact('order'));
    }
    
    public function paypalSuccess(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        // Update order status
        $order->update([
            'status' => 'processing',
            'is_paid' => true,
            'paid_at' => now(),
        ]);
        
        return redirect()->route('checkout.success', ['order' => $order->id]);
    }
    
    public function paypalCancel(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }
        
        return redirect()->route('checkout.cancel', ['order' => $order->id]);
    }
}