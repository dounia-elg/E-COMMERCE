@extends('layouts.app')

@section('title', 'Home')

@section('content')
<!-- Hero Section -->
<section class="hero-section mb-5">
    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-indicators">
            @foreach($sliders as $key => $slider)
            <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $key }}" class="{{ $key == 0 ? 'active' : '' }}" aria-current="{{ $key == 0 ? 'true' : 'false' }}" aria-label="Slide {{ $key + 1 }}"></button>
            @endforeach
        </div>
        <div class="carousel-inner">
            @foreach($sliders as $key => $slider)
            <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                <img src="{{ asset('storage/' . $slider->image_path) }}" class="d-block w-100" alt="{{ $slider->title }}">
                <div class="carousel-caption d-none d-md-block">
                    <h2>{{ $slider->title }}</h2>
                    <p>{{ $slider->subtitle }}</p>
                    @if($slider->button_text && $slider->button_link)
                    <a href="{{ $slider->button_link }}" class="btn btn-primary">{{ $slider->button_text }}</a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
        </button>
    </div>
</section>

<!-- Featured Categories -->
<section class="container mb-5">
    <div class="text-center mb-4">
        <h2 class="section-title">Shop by Category</h2>
        <p class="text-muted">Find the perfect products across our popular categories</p>
    </div>
    
    <div class="row g-4">
        @foreach($featuredCategories as $category)
        <div class="col-6 col-md-4 col-lg-3">
            <a href="{{ route('products.category', $category->slug) }}" class="text-decoration-none">
                <div class="card category-card h-100">
                    <div class="category-img-container">
                        @if($category->image_path)
                            <img src="{{ asset('storage/' . $category->image_path) }}" class="card-img-top" alt="{{ $category->name }}">
                        @else
                            <img src="{{ asset('images/category-placeholder.jpg') }}" class="card-img-top" alt="{{ $category->name }}">
                        @endif
                    </div>
                    <div class="card-body text-center">
                        <h5 class="card-title mb-0">{{ $category->name }}</h5>
                        <p class="card-text text-muted small">{{ $category->products_count }} Products</p>
                    </div>
                </div>
            </a>
        </div>
        @endforeach
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">View All Categories</a>
    </div>
</section>

<!-- Featured Products -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-0">Featured Products</h2>
            <p class="text-muted">Handpicked products for our customers</p>
        </div>
        <a href="{{ route('products.index', ['featured' => 1]) }}" class="btn btn-outline-primary">View All</a>
    </div>
    
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($featuredProducts as $product)
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
</section>

<!-- Best Sellers -->
<section class="bg-light py-5 mb-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="section-title mb-0">Best Sellers</h2>
                <p class="text-muted">Our most popular products based on sales</p>
            </div>
            <a href="{{ route('products.index', ['sort' => 'popularity']) }}" class="btn btn-outline-primary">View All</a>
        </div>
        
        <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
            @foreach($bestSellerProducts as $product)
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
    </div>
</section>

<!-- New Arrivals -->
<section class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="section-title mb-0">New Arrivals</h2>
            <p class="text-muted">Check out our latest products</p>
        </div>
        <a href="{{ route('products.index', ['sort' => 'latest']) }}" class="btn btn-outline-primary">View All</a>
    </div>
    
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-4 g-4">
        @foreach($newArrivals as $product)
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
                    
                    <div class="new-badge">
                        New
                    </div>
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
</section>

<!-- Special Deals -->
<section class="container-fluid px-0 mb-5">
    <div class="special-deals-banner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 text-white">
                    <h2 class="mb-3">Special Deals & Discounts</h2>
                    <p class="lead mb-4">Get up to 50% off on selected items. Limited time offers you don't want to miss!</p>
                    <a href="{{ route('products.index', ['discount' => 1]) }}" class="btn btn-light btn-lg">Shop Now</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="container mb-5">
    <div class="row g-4">
        <div class="col-md-3 col-sm-6">
            <div class="feature-box text-center">
                <div class="feature-icon mb-3">
                    <i class="fas fa-truck fa-3x text-primary"></i>
                </div>
                <h5>Free Shipping</h5>
                <p class="text-muted">On orders over $50</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="feature-box text-center">
                <div class="feature-icon mb-3">
                    <i class="fas fa-undo fa-3x text-primary"></i>
                </div>
                <h5>30 Days Return</h5>
                <p class="text-muted">Hassle-free returns</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="feature-box text-center">
                <div class="feature-icon mb-3">
                    <i class="fas fa-lock fa-3x text-primary"></i>
                </div>
                <h5>Secure Payment</h5>
                <p class="text-muted">100% secure payment</p>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="feature-box text-center">
                <div class="feature-icon mb-3">
                    <i class="fas fa-headset fa-3x text-primary"></i>
                </div>
                <h5>24/7 Support</h5>
                <p class="text-muted">Dedicated customer support</p>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 text-center">
                <h3>Subscribe to Our Newsletter</h3>
                <p class="text-muted mb-4">Get updates about new products, sales and promotions.</p>
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="newsletter-form">
                    @csrf
                    <div class="input-group mb-3">
                        <input type="email" class="form-control" placeholder="Your email address" name="email" required>
                        <button class="btn btn-primary" type="submit">Subscribe</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
    .hero-section {
        margin-top: -20px;
    }
    
    .carousel-item {
        height: 500px;
        overflow: hidden;
    }
    
    .carousel-item img {
        object-fit: cover;
        height: 100%;
        width: 100%;
    }
    
    .carousel-caption {
        background-color: rgba(0, 0, 0, 0.5);
        padding: 20px;
        border-radius: 5px;
    }
    
    .section-title {
        position: relative;
        font-weight: 600;
    }
    
    .category-card {
        transition: all 0.3s ease;
        overflow: hidden;
    }
    
    .category-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
    
    .category-img-container {
        height: 180px;
        overflow: hidden;
    }
    
    .category-img-container img {
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    
    .category-card:hover .category-img-container img {
        transform: scale(1.1);
    }
    
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
        transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-image-container img {
        transform: scale(1.05);
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
    
    .new-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background-color: #28a745;
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
    
    .special-deals-banner {
        background: url('{{ asset('images/special-deals-bg.jpg') }}') no-repeat center center;
        background-size: cover;
        padding: 100px 0;
        position: relative;
    }
    
    .special-deals-banner::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        background-color: rgba(0, 0, 0, 0.5);
    }
    
    .special-deals-banner .container {
        position: relative;
        z-index: 1;
    }
    
    .feature-box {
        padding: 20px;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    
    .feature-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    }
    
    .feature-icon {
        background-color: rgba(var(--bs-primary-rgb), 0.1);
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .newsletter-form .form-control {
        height: 50px;
        padding-left: 20px;
    }
    
    .newsletter-form .btn {
        padding-left: 30px;
        padding-right: 30px;
    }
    
    @media (max-width: 767px) {
        .carousel-item {
            height: 300px;
        }
        
        .carousel-caption {
            display: block !important;
            padding: 10px;
        }
        
        .carousel-caption h2 {
            font-size: 1.5rem;
        }
        
        .special-deals-banner {
            padding: 50px 0;
        }
    }
</style>
@endpush