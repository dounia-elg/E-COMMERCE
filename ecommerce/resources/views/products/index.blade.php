@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Products</li>
        </ol>
    </nav>
    
    <div class="row mb-4">
        <div class="col-md-3">
            <!-- Filter Sidebar -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('products.index') }}" method="GET">
                        <!-- Categories Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Categories</h6>
                            <div class="categories-filter">
                                @foreach($categories as $category)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="categories[]" 
                                           value="{{ $category->id }}" id="category{{ $category->id }}"
                                           {{ in_array($category->id, request()->get('categories', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="category{{ $category->id }}">
                                        {{ $category->name }} ({{ $category->products_count }})
                                    </label>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Price Range Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Price Range</h6>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="min_price" class="form-label">Min</label>
                                        <input type="number" class="form-control" id="min_price" name="min_price" 
                                               min="0" value="{{ request('min_price', '') }}" placeholder="Min">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="max_price" class="form-label">Max</label>
                                        <input type="number" class="form-control" id="max_price" name="max_price" 
                                               min="0" value="{{ request('max_price', '') }}" placeholder="Max">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ratings Filter -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Customer Rating</h6>
                            <div class="ratings-filter">
                                @for($i = 5; $i >= 1; $i--)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="ratings[]" 
                                           value="{{ $i }}" id="rating{{ $i }}"
                                           {{ in_array($i, request()->get('ratings', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="rating{{ $i }}">
                                        @for($j = 1; $j <= 5; $j++)
                                            @if($j <= $i)
                                                <i class="fas fa-star text-warning"></i>
                                            @else
                                                <i class="far fa-star text-warning"></i>
                                            @endif
                                        @endfor
                                        & Up
                                    </label>
                                </div>
                                @endfor
                            </div>
                        </div>
                        
                                                <!-- Sort Order -->
                                                <div class="mb-4">
                                                    <h6 class="fw-bold">Sort By</h6>
                                                    <select class="form-select" name="sort">
                                                        <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                                                        <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                                                        <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                                                        <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
                                                        <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Rating</option>
                                                    </select>
                                                </div>
                                                
                                                <!-- Apply Button -->
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-primary" type="submit">Apply Filters</button>
                                                    <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Reset</a>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    <!-- Featured Products Sidebar -->
                                    <div class="card shadow-sm">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Featured Products</h5>
                                        </div>
                                        <div class="card-body p-0">
                                            <div class="list-group list-group-flush">
                                                @foreach($featuredProducts as $featuredProduct)
                                                <a href="{{ route('products.show', $featuredProduct->slug) }}" class="list-group-item list-group-item-action">
                                                    <div class="d-flex">
                                                        <div class="flex-shrink-0">
                                                            @if($featuredProduct->primaryImage)
                                                                <img src="{{ asset('storage/' . $featuredProduct->primaryImage->image_path) }}" 
                                                                     alt="{{ $featuredProduct->name }}" class="img-fluid" style="width: 60px; height: 60px; object-fit: cover;">
                                                            @else
                                                                <img src="{{ asset('images/no-image.jpg') }}" 
                                                                     alt="{{ $featuredProduct->name }}" class="img-fluid" style="width: 60px; height: 60px; object-fit: cover;">
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h6 class="mb-0">{{ \Illuminate\Support\Str::limit($featuredProduct->name, 30) }}</h6>
                                                            <div class="product-price">
                                                                @if($featuredProduct->discount_percent)
                                                                    <span class="text-muted text-decoration-line-through">${{ number_format($featuredProduct->price, 2) }}</span>
                                                                    <span class="text-primary">${{ number_format($featuredProduct->discounted_price, 2) }}</span>
                                                                @else
                                                                    <span class="text-primary">${{ number_format($featuredProduct->price, 2) }}</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-9">
                                    <!-- Products Heading and Controls -->
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h4 class="mb-0">{{ $title ?? 'All Products' }}</h4>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">Show:</span>
                                            <form id="per-page-form" action="{{ route('products.index') }}" method="GET">
                                                @if(request()->has('categories'))
                                                    @foreach(request('categories') as $category)
                                                        <input type="hidden" name="categories[]" value="{{ $category }}">
                                                    @endforeach
                                                @endif
                                                
                                                @if(request()->has('ratings'))
                                                    @foreach(request('ratings') as $rating)
                                                        <input type="hidden" name="ratings[]" value="{{ $rating }}">
                                                    @endforeach
                                                @endif
                                                
                                                @if(request('min_price'))
                                                    <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                                                @endif
                                                
                                                @if(request('max_price'))
                                                    <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                                                @endif
                                                
                                                @if(request('sort'))
                                                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                                                @endif
                                                
                                                <select class="form-select form-select-sm" name="per_page" onchange="this.form.submit()">
                                                    <option value="12" {{ request('per_page', 12) == 12 ? 'selected' : '' }}>12</option>
                                                    <option value="24" {{ request('per_page') == 24 ? 'selected' : '' }}>24</option>
                                                    <option value="48" {{ request('per_page') == 48 ? 'selected' : '' }}>48</option>
                                                </select>
                                            </form>
                                            <div class="btn-group ms-3" role="group">
                                                <a href="#" class="btn btn-outline-secondary btn-sm active" data-view="grid">
                                                    <i class="fas fa-th-large"></i>
                                                </a>
                                                <a href="#" class="btn btn-outline-secondary btn-sm" data-view="list">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($products->isEmpty())
                                        <div class="alert alert-info">
                                            <h5 class="alert-heading">No products found!</h5>
                                            <p>We couldn't find any products matching your criteria. Please try adjusting your filters or check back later.</p>
                                        </div>
                                    @else
                                        <!-- Products Grid -->
                                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="products-grid">
                                            @foreach($products as $product)
                                            <div class="col">
                                                <div class="card h-100 product-card">
                                                    <div class="product-image-container">
                                                        @if($product->primaryImage)
                                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" class="card-img-top" alt="{{ $product->name }}">
                                                        @else
                                                            <img src="{{ asset('images/no-image.jpg') }}" class="card-img-top" alt="{{ $product->name }}">
                                                        @endif
                                                        
                                                        @if($product->discount_percent)
                                                            <div class="discount-badge">
                                                                -{{ $product->discount_percent }}%
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="card-body d-flex flex-column">
                                                        <h5 class="card-title">{{ $product->name }}</h5>
                                                        <p class="card-text text-muted mb-1">{{ $product->category->name }}</p>
                                                        <div class="product-price mt-auto">
                                                            @if($product->discount_percent)
                                                                <span class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span>
                                                                <span class="text-primary fw-bold">${{ number_format($product->discounted_price, 2) }}</span>
                                                            @else
                                                                <span class="text-primary fw-bold">${{ number_format($product->price, 2) }}</span>
                                                            @endif
                                                        </div>
                                                        <div class="product-rating">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $product->average_rating)
                                                                    <i class="fas fa-star text-warning"></i>
                                                                @else
                                                                    <i class="far fa-star text-warning"></i>
                                                                @endif
                                                            @endfor
                                                            <span class="ms-1 text-muted">({{ $product->reviews->count() }})</span>
                                                        </div>
                                                    </div>
                                                    <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary">Details</a>
                                                        <form action="{{ route('cart.add') }}" method="POST">
                                                            @csrf
                                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                            <input type="hidden" name="quantity" value="1">
                                                            <button type="submit" class="btn btn-primary" {{ !$product->in_stock ? 'disabled' : '' }}>
                                                                <i class="fas fa-cart-plus"></i> Add
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Products List (Hidden by default) -->
                                        <div class="d-none" id="products-list">
                                            @foreach($products as $product)
                                            <div class="card mb-3 product-list-card">
                                                <div class="row g-0">
                                                    <div class="col-md-3 product-list-image-container">
                                                        @if($product->primaryImage)
                                                            <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" class="img-fluid rounded-start" alt="{{ $product->name }}">
                                                        @else
                                                            <img src="{{ asset('images/no-image.jpg') }}" class="img-fluid rounded-start" alt="{{ $product->name }}">
                                                        @endif
                                                        
                                                        @if($product->discount_percent)
                                                            <div class="discount-badge">
                                                                -{{ $product->discount_percent }}%
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-9">
                                                        <div class="card-body">
                                                            <h5 class="card-title">{{ $product->name }}</h5>
                                                            <p class="card-text text-muted">{{ $product->category->name }}</p>
                                                            <div class="product-rating mb-2">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= $product->average_rating)
                                                                        <i class="fas fa-star text-warning"></i>
                                                                    @else
                                                                        <i class="far fa-star text-warning"></i>
                                                                    @endif
                                                                @endfor
                                                                <span class="ms-1 text-muted">({{ $product->reviews->count() }})</span>
                                                            </div>
                                                            <p class="card-text mb-3">{{ \Illuminate\Support\Str::limit($product->short_description ?? $product->description, 150) }}</p>
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="product-price">
                                                                    @if($product->discount_percent)
                                                                        <span class="text-decoration-line-through text-muted">${{ number_format($product->price, 2) }}</span>
                                                                        <span class="text-primary fw-bold fs-5">${{ number_format($product->discounted_price, 2) }}</span>
                                                                    @else
                                                                        <span class="text-primary fw-bold fs-5">${{ number_format($product->price, 2) }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="d-flex">
                                                                    <a href="{{ route('products.show', $product->slug) }}" class="btn btn-outline-primary me-2">Details</a>
                                                                    <form action="{{ route('cart.add') }}" method="POST">
                                                                        @csrf
                                                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                                                        <input type="hidden" name="quantity" value="1">
                                                                        <button type="submit" class="btn btn-primary" {{ !$product->in_stock ? 'disabled' : '' }}>
                                                                            <i class="fas fa-cart-plus"></i> Add
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        
                                        <!-- Pagination -->
                                        <div class="d-flex justify-content-center mt-4">
                                            {{ $products->appends(request()->query())->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endsection
                        
                        @push('scripts')
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const gridViewBtn = document.querySelector('[data-view="grid"]');
                                const listViewBtn = document.querySelector('[data-view="list"]');
                                const productsGrid = document.getElementById('products-grid');
                                const productsList = document.getElementById('products-list');
                                
                                // Set view based on localStorage or default to grid
                                const currentView = localStorage.getItem('productsView') || 'grid';
                                if (currentView === 'list') {
                                    productsGrid.classList.add('d-none');
                                    productsList.classList.remove('d-none');
                                    gridViewBtn.classList.remove('active');
                                    listViewBtn.classList.add('active');
                                }
                                
                                // Grid view button click
                                gridViewBtn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    productsGrid.classList.remove('d-none');
                                    productsList.classList.add('d-none');
                                    gridViewBtn.classList.add('active');
                                    listViewBtn.classList.remove('active');
                                    localStorage.setItem('productsView', 'grid');
                                });
                                
                                // List view button click
                                listViewBtn.addEventListener('click', function(e) {
                                    e.preventDefault();
                                    productsGrid.classList.add('d-none');
                                    productsList.classList.remove('d-none');
                                    gridViewBtn.classList.remove('active');
                                    listViewBtn.classList.add('active');
                                    localStorage.setItem('productsView', 'list');
                                });
                            });
                        </script>
                        @endpush
                        
                        @push('styles')
                        <style>
                            .product-image-container {
                                position: relative;
                                padding-top: 1rem;
                                height: 200px;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                overflow: hidden;
                            }
                            
                            .product-image-container img {
                                max-height: 100%;
                                object-fit: contain;
                            }
                            
                            .discount-badge {
                                position: absolute;
                                top: 10px;
                                right: 10px;
                                background-color: #dc3545;
                                color: white;
                                padding: 5px 8px;
                                border-radius: 3px;
                                font-size: 0.8rem;
                                font-weight: bold;
    }
    
    .product-card {
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.125);
    }
    
    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .product-list-image-container {
        position: relative;
        height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    
    .product-list-image-container img {
        height: 100%;
        object-fit: cover;
        width: 100%;
    }
    
    .product-list-card {
        transition: all 0.3s ease;
    }
    
    .product-list-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    @media (max-width: 767px) {
        .product-list-image-container {
            height: 180px;
        }
    }
</style>
@endpush