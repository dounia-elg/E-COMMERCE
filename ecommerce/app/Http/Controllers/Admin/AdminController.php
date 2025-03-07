<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }
    
    public function dashboard()
    {
        // Get stats for the dashboard
        $totalSales = Order::where('is_paid', true)->sum('total_amount');
        $totalOrders = Order::count();
        $totalCustomers = User::whereHas('roles', function($q) {
            $q->where('name', 'customer');
        })->count();
        $totalProducts = Product::count();
        
        // Get sales data for the chart (last 7 days)
        $salesChart = $this->getSalesChartData();
        
        // Get recent orders
        $recentOrders = Order::with('user')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Get top selling products
        $topProducts = $this->getTopProducts();
        
        return view('admin.dashboard', compact(
            'totalSales', 
            'totalOrders', 
            'totalCustomers', 
            'totalProducts', 
            'salesChart', 
            'recentOrders',
            'topProducts'
        ));
    }
    
    private function getSalesChartData()
    {
        $startDate = Carbon::now()->subDays(6);
        $endDate = Carbon::now();
        
        // Get sales data for the last 7 days
        $salesData = Order::where('is_paid', true)
            ->where('created_at', '>=', $startDate)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total_amount) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // Format the data for the chart
        $chartData = [];
        $labels = [];
        $values = [];
        
        for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
            $currentDate = $date->format('Y-m-d');
            $labels[] = $date->format('D');
            
            $total = 0;
            foreach ($salesData as $data) {
                if ($data->date == $currentDate) {
                    $total = $data->total;
                    break;
                }
            }
            
            $values[] = $total;
        }
        
        return [
            'labels' => $labels,
            'values' => $values
        ];
    }
    
    private function getTopProducts($limit = 5)
    {
        return DB::table('order_items')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->select(
                'products.id',
                'products.name',
                DB::raw('SUM(order_items.quantity) as total_quantity'),
                DB::raw('SUM(order_items.quantity * order_items.price) as total_sales')
            )
            ->groupBy('products.id', 'products.name')
            ->orderBy('total_quantity', 'desc')
            ->limit($limit)
            ->get();
    }
    
    public function pendingReviews()
    {
        $reviews = Review::where('is_approved', false)
            ->with(['product', 'user'])
            ->paginate(20);
            
        return view('admin.reviews.pending', compact('reviews'));
    }
    
    public function approveReview($id)
    {
        $review = Review::findOrFail($id);
        $review->update(['is_approved' => true]);
        
        return redirect()->back()->with('success', 'Review approved successfully');
    }
    
    public function rejectReview($id)
    {
        $review = Review::findOrFail($id);
        $review->delete();
        
        return redirect()->back()->with('success', 'Review rejected and deleted successfully');
    }
}