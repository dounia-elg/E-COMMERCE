<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PDF;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function show(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $order->load(['items.product', 'user']);
        
        return view('orders.show', compact('order'));
    }
    
    public function invoice(Order $order)
    {
        // Check if order belongs to the authenticated user
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $order->load(['items.product', 'user']);
        
        $pdf = PDF::loadView('orders.invoice', compact('order'));
        
        return $pdf->download('invoice-' . $order->order_number . '.pdf');
    }
}