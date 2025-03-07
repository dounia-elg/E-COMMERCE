<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cart = $this->getOrCreateCart();
        $cartItems = $cart->items()->with('product.primaryImage')->get();
        
        return view('cart.index', compact('cart', 'cartItems'));
    }
    
    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10'
        ]);
        
        $product = Product::findOrFail($request->product_id);
        
        // Check if product is available
        if (!$product->is_active || $product->quantity < $request->quantity) {
            return redirect()->back()->with('error', 'This product is not available in the requested quantity');
        }
        
        $cart = $this->getOrCreateCart();
        
        // Check if product already in cart
        $cartItem = CartItem::where('cart_id', $cart->id)
            ->where('product_id', $product->id)
            ->first();
            
        if ($cartItem) {
            // Update quantity
            $newQuantity = $cartItem->quantity + $request->quantity;
            
            // Check if new quantity exceeds available stock
            if ($newQuantity > $product->quantity) {
                return redirect()->back()->with('error', 'Cannot add more of this product (stock limit reached)');
            }
            
            $cartItem->update(['quantity' => $newQuantity]);
        } else {
            // Add new item to cart
            CartItem::create([
                'cart_id' => $cart->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity
            ]);
        }
        
        return redirect()->route('cart.index')->with('success', 'Product added to cart successfully');
    }
    
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:10'
        ]);
        
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getOrCreateCart();
        
        // Check if item belongs to the user's cart
        if ($cartItem->cart_id !== $cart->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $product = $cartItem->product;
        
        // Check if requested quantity is available
        if ($request->quantity > $product->quantity) {
            return redirect()->back()->with('error', 'The requested quantity exceeds available stock');
        }
        
        $cartItem->update(['quantity' => $request->quantity]);
        
        return redirect()->route('cart.index')->with('success', 'Cart updated successfully');
    }
    
    public function remove($id)
    {
        $cartItem = CartItem::findOrFail($id);
        $cart = $this->getOrCreateCart();
        
        // Check if item belongs to the user's cart
        if ($cartItem->cart_id !== $cart->id) {
            abort(403, 'Unauthorized action.');
        }
        
        $cartItem->delete();
        
        return redirect()->route('cart.index')->with('success', 'Item removed from cart');
    }
    
    public function clear()
    {
        $cart = $this->getOrCreateCart();
        $cart->items()->delete();
        
        return redirect()->route('cart.index')->with('success', 'Cart cleared successfully');
    }
    
    // Helper method to get current user's cart or create one
    protected function getOrCreateCart()
    {
        if (Auth::check()) {
            // Get cart for logged-in user
            $cart = Cart::firstOrCreate([
                'user_id' => Auth::id()
            ]);
            
            // If there was a session cart, merge it with the user's cart
            $this->mergeSessionCartWithUserCart($cart);
            
            return $cart;
        } else {
            // Get cart for guest user
            $sessionId = session()->get('cart_session_id');
            
            if (!$sessionId) {
                $sessionId = Str::uuid();
                session()->put('cart_session_id', $sessionId);
            }
            
            return Cart::firstOrCreate([
                'session_id' => $sessionId
            ]);
        }
    }
    
    // Helper method to merge session cart with user cart after login
    protected function mergeSessionCartWithUserCart($userCart)
    {
        $sessionId = session()->get('cart_session_id');
        
        if (!$sessionId) {
            return;
        }
        
        $sessionCart = Cart::where('session_id', $sessionId)->first();
        
        if (!$sessionCart) {
            return;
        }
        
        // Loop through session cart items and add to user cart
        foreach ($sessionCart->items as $item) {
            $userCartItem = CartItem::where('cart_id', $userCart->id)
                ->where('product_id', $item->product_id)
                ->first();
                
            if ($userCartItem) {
                // Update quantity if product already in user's cart
                $userCartItem->update([
                    'quantity' => $userCartItem->quantity + $item->quantity
                ]);
            } else {
                // Move item to user's cart
                $item->update([
                    'cart_id' => $userCart->id
                ]);
            }
        }
        
        // Delete the session cart
        $sessionCart->delete();
        session()->forget('cart_session_id');
    }
}