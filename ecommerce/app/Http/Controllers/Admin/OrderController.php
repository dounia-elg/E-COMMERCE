<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }
    
    public function index(Request $request)
    {
        $query = Order::with('user');
        
        // Filter by status
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
        }
        
        // Search by order number or customer name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('user', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }
        
        $orders = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.orders.index', compact('orders'));
    }
    
    public function show(Order $order)
    {
        $order->load(['items.product', 'user']);
        
        return view('admin.orders.show', compact('order'));
    }
    
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,declined'
        ]);
        
        $order->update([
            'status' => $request->status
        ]);
        
        return redirect()->back()->with('success', 'Order status updated successfully');
    }
}