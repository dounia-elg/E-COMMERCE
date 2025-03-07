<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Slider;

class HomeController extends Controller
{
    /**
     * Show the application homepage.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get sliders for the carousel
        $sliders = Slider::where('is_active', true)->orderBy('order')->get();

        // Get featured categories
        $featuredCategories = Category::withCount('products')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        // Get featured products
        $featuredProducts = Product::with('category', 'primaryImage', 'reviews')
            ->where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        // Calculate average rating
        $featuredProducts->each(function ($product) {
            $product->average_rating = $product->reviews->avg('rating') ?: 0;
        });

        // Get best seller products
        $bestSellerProducts = Product::with('category', 'primaryImage', 'reviews')
            ->where('is_active', true)
            ->orderBy('sales_count', 'desc')
            ->take(8)
            ->get();

        $bestSellerProducts->each(function ($product) {
            $product->average_rating = $product->reviews->avg('rating') ?: 0;
        });

        // Get new arrivals
        $newArrivals = Product::with('category', 'primaryImage', 'reviews')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get();

        $newArrivals->each(function ($product) {
            $product->average_rating = $product->reviews->avg('rating') ?: 0;
        });

        // Define chart colors for the dashboard
        $chartColors = [
            '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
            '#6f42c1', '#5a5c69', '#6610f2', '#fd7e14', '#20c997'
        ];

        return view('home', compact(
            'sliders', 
            'featuredCategories', 
            'featuredProducts', 
            'bestSellerProducts', 
            'newArrivals',
            'chartColors'
        ));
    }

    /**
     * Store a newsletter subscription
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function subscribeNewsletter(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:newsletter_subscribers,email'
        ]);

        // Here you would typically store the email in your newsletter_subscribers table
        // For now, we'll just redirect with a success message
        
        return back()->with('success', 'Thank you for subscribing to our newsletter!');
    }
}