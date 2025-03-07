<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::active()->with(['category', 'primaryImage']);

        // Handle search
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Handle category filter
        if ($request->has('category') && $request->input('category') !== 'all') {
            $categoryId = $request->input('category');
            $query->where('category_id', $categoryId);
        }

        // Handle sorting
        if ($request->has('sort')) {
            switch ($request->input('sort')) {
                case 'price_low':
                    $query->orderBy('price', 'asc');
                    break;
                case 'price_high':
                    $query->orderBy('price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'name':
                    $query->orderBy('name', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->paginate(12);
        $categories = Category::where('is_active', true)->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->active()
            ->with(['category', 'images', 'reviews' => function($query) {
                $query->where('is_approved', true)
                    ->orderBy('created_at', 'desc')
                    ->with('user');
            }])
            ->firstOrFail();
            
        // Get related products from same category
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->active()
            ->with('primaryImage')
            ->inRandomOrder()
            ->take(4)
            ->get();
            
        return view('products.show', compact('product', 'relatedProducts'));
    }

    public function addReview(Request $request, $id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to leave a review.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // Check if user already reviewed this product
        $existingReview = Review::where('product_id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingReview) {
            $existingReview->update([
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment'),
                'is_approved' => false, // Requires re-approval
            ]);
        } else {
            Review::create([
                'product_id' => $id,
                'user_id' => Auth::id(),
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment'),
                'is_approved' => false, // Requires admin approval
            ]);
        }

        return redirect()->back()->with('success', 'Your review has been submitted and is awaiting approval.');
    }

    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
            
        $products = Product::where('category_id', $category->id)
            ->active()
            ->with(['category', 'primaryImage'])
            ->paginate(12);
            
        $categories = Category::where('is_active', true)->get();
            
        return view('products.by_category', compact('category', 'products', 'categories'));
    }

    public function search(Request $request)
    {
        $search = $request->input('search');
        
        $products = Product::where(function($query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('description', 'like', "%{$search}%");
        })
        ->active()
        ->with(['category', 'primaryImage'])
        ->paginate(12);
        
        $categories = Category::where('is_active', true)->get();
        
        return view('products.search', compact('products', 'categories', 'search'));
    }
}