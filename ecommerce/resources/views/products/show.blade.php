@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Products</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="product-image-gallery">
                <!-- Main Image -->
                <div class="product-main-image mb-3">
                    @if($product->images->count() > 0)
                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" id="main-product-image" class="img-fluid" alt="{{ $product->name }}">
                    @else
                        <img src="{{ asset('images/no-image.jpg') }}" id="main-product-image" class="img-fluid" alt="{{ $product->name }}">
                    @endif
                    
                    @if($product->discount_percent)
                        <div class="discount-badge">
                            -{{ $product->discount_percent }}%
                        </div>
                    @endif
                </div>
                
                <!-- Thumbnails -->
                @if($product->images->count() > 1)
                <div class="product-thumbnails">
                    <div class="row g-2">
                        @foreach($product->images as $image)
                        <div class="col-3">
                            <div class="thumbnail-image {{ $image->is_primary ? 'active' : '' }}" 
                                 data-image="{{ asset('storage/' . $image->image_path) }}">
                                <img src="{{ asset('storage/' . $image->image_path) }}" class="img-fluid" alt="{{ $product->name }}">
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="col-md-6">
            <h1 class="product-title mb-2">{{ $product->name }}</h1>
            
            <div class="product-meta mb-3">
                <div class="product-rating">
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= $product->average_rating)
                            <i class="fas fa-star text-warning"></i>
                        @else
                            <i class="far fa-star text-warning"></i>
                        @endif
                    @endfor
                    <span class="ms-1 text-muted">({{ $product->reviews->count() }} reviews)</span>
                </div>
            </div>
            
            <div class="product-price mb-3">
                @if($product->discount_percent)
                    <span class="text-decoration-line-through text-muted fs-4">${{ number_format($product->price, 2) }}</span>
                    <span class="text-primary fw-bold fs-2">${{ number_format($product->discounted_price, 2) }}</span>
                    <span class="badge bg-danger ms-2">Save {{ $product->discount_percent }}%</span>
                @else
                    <span class="text-primary fw-bold fs-2">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            
            <div class="product-status mb-3">
                <span class="badge {{ $product->in_stock ? 'bg-success' : 'bg-danger' }}">
                    {{ $product->in_stock ? 'In Stock' : 'Out of Stock' }}
                </span>
                <span class="ms-2 text-muted">SKU: {{ $product->sku }}</span>
            </div>
            
            <div class="product-short-description mb-4">
                <p>{{ $product->short_description ?? \Illuminate\Support\Str::limit($product->description, 200) }}</p>
            </div>
            
            @if($product->in_stock)
                <form action="{{ route('cart.add') }}" method="POST" class="product-form mb-4">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    
                    <div class="row align-items-center">
                        <div class="col-md-4 col-5">
                            <div class="input-group product-quantity">
                                <button type="button" class="btn btn-outline-secondary quantity-decrease">-</button>
                                <input type="number" class="form-control text-center" name="quantity" min="1" value="1" max="{{ $product->quantity }}">
                                <button type="button" class="btn btn-outline-secondary quantity-increase">+</button>
                            </div>
                        </div>
                        <div class="col-md-8 col-7">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-cart-plus me-2"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </form>
            @else
                <div class="alert alert-warning mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> This product is currently out of stock.
                </div>
            @endif
            
            <div class="product-actions mb-4">
                <button class="btn btn-outline-secondary btn-sm">
                    <i class="far fa-heart"></i> Add to Wishlist
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="fas fa-exchange-alt"></i> Compare
                </button>
                <button class="btn btn-outline-secondary btn-sm ms-2">
                    <i class="fas fa-share-alt"></i> Share
                </button>
            </div>
            
            <div class="product-info mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success me-2"></i> Fast shipping</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Quality guarantee</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li><i class="fas fa-check-circle text-success me-2"></i> 30-day returns</li>
                            <li><i class="fas fa-check-circle text-success me-2"></i> Secure checkout</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="product-meta">
                <p class="mb-1"><strong>Category:</strong> <a href="{{ route('products.category', $product->category->slug) }}">{{ $product->category->name }}</a></p>
            </div>
        </div>
    </div>
    
    <div class="row mt-5">
        <div class="col-12">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab" data-bs-target="#description" 
                            type="button" role="tab" aria-controls="description" aria-selected="true">Description</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" 
                            type="button" role="tab" aria-controls="reviews" aria-selected="false">Reviews ({{ $product->reviews->count() }})</button>
                </li>
            </ul>
            <div class="tab-content border-start border-end border-bottom p-4 bg-white" id="productTabsContent">
                <div class="tab-pane fade show active" id="description" role="tabpanel" aria-labelledby="description-tab">
                    <div class="product-description">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="reviews" role="tabpanel" aria-labelledby="reviews-tab">
                    <div class="product-reviews">
                        @if($product->reviews->where('is_approved', true)->count() > 0)
                            @foreach($product->reviews->where('is_approved', true)->sortByDesc('created_at') as $review)
                                <div class="review-item mb-4 pb-4 border-bottom">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0">
                                            <div class="avatar">
                                                <span class="avatar-text rounded-circle bg-primary">{{ substr($review->user->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <h5 class="mb-0">{{ $review->user->name }}</h5>
                                                <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="review-rating mb-2">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star text-warning"></i>
                                                    @else
                                                        <i class="far fa-star text-warning"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                            <p class="review-text mb-0">{{ $review->comment }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="alert alert-info">
                                There are no reviews yet for this product.
                            </div>
                        @endif
                        
                        @auth
                            <div class="write-review mt-4">
                                <h4>Write a Review</h4>
                                <form action="{{ route('products.review', $product->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="rating" class="form-label">Rating</label>
                                        <div class="star-rating">
                                            <div class="stars">
                                                <i class="far fa-star" data-rating="1"></i>
                                                <i class="far fa-star" data-rating="2"></i>
                                                <i class="far fa-star" data-rating="3"></i>
                                                <i class="far fa-star" data-rating="4"></i>
                                                <i class="far fa-star" data-rating="5"></i>
                                            </div>
                                            <input type="hidden" name="rating" id="rating" value="0">
                                        </div>
                                        @error('rating')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="comment" class="form-label">Your Review</label>
                                        <textarea class="form-control @error('comment') is-invalid @enderror" id="comment" name="comment" rows="4" required>{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-0">
                                        <button type="submit" class="btn btn-primary">Submit Review</button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="alert alert-info mt-4">
                                Please <a href="{{ route('login') }}">login</a> to write a review.
                            </div>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if(count($relatedProducts) > 0)
    <div class="related-products mt-5">
        <h3>Related Products</h3>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-2">
            @foreach($relatedProducts as $relatedProduct)
            <div class="col">
                <div class="card h-100 product-card">
                    <div class="product-image-container">
                        @if($relatedProduct->primaryImage)
                            <img src="{{ asset('storage/' . $relatedProduct->primaryImage->image_path) }}" class="card-img-top" alt="{{ $relatedProduct->name }}">
                        @else
                            <img src="{{ asset('images/no-image.jpg') }}" class="card-img-top" alt="{{ $relatedProduct->name }}">
                        @endif
                        
                        @if($relatedProduct->discount_percent)
                            <div class="discount-badge">
                                -{{ $relatedProduct->discount_percent }}%
                            </div>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                        <p class="card-text text-muted mb-1">{{ $relatedProduct->category->name }}</p>
                        <div class="product-price mt-auto">
                            @if($relatedProduct->discount_percent)
                                <span class="text-decoration-line-through text-muted">${{ number_format($relatedProduct->price, 2) }}</span>
                                <span class="text-primary fw-bold">${{ number_format($relatedProduct->discounted_price, 2) }}</span>
                            @else
                                <span class="text-primary fw-bold">${{ number_format($relatedProduct->price, 2) }}</span>
                            @endif
                        </div>
                        <div class="product-rating">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= $relatedProduct->average_rating)
                                    <i class="fas fa-star text-warning"></i>
                                @else
                                    <i class="far fa-star text-warning"></i>
                                @endif
                            @endfor
                            <span class="ms-1 text-muted">({{ $relatedProduct->reviews->count() }})</span>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-top-0 d-flex justify-content-between">
                        <a href="{{ route('products.show', $relatedProduct->slug) }}" class="btn btn-outline-primary">Details</a>
                        <form action="{{ route('cart.add') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $relatedProduct->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary" {{ !$relatedProduct->in_stock ? 'disabled' : '' }}>
                                <i class="fas fa-cart-plus"></i> Add
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .product-main-image {
        position: relative;
        height: 400px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        background-color: #f8f9fa;
        border-radius: 5px;
    }
    
    .product-main-image img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    
    .discount-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: #dc3545;
        color: white;
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 0.9rem;
        font-weight: bold;
    }
    
    .thumbnail-image {
        cursor: pointer;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 2px solid transparent;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    
    .thumbnail-image.active {
        border-color: var(--bs-primary);
    }
    
    .thumbnail-image img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
    }
    
    .product-quantity .form-control {
        max-width: 60px;
    }
    
    .avatar {
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .avatar-text {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }
    
    .star-rating .stars {
        font-size: 1.5rem;
        cursor: pointer;
    }
    
    .star-rating .stars i {
        margin-right: 5px;
    }
    
    .star-rating .stars i.active {
        color: #ffc107;
    }
    
    @media (max-width: 767px) {
        .product-main-image {
            height: 300px;
        }
        
        .thumbnail-image {
            height: 60px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Thumbnail click handler
        const thumbnails = document.querySelectorAll('.thumbnail-image');
        const mainImage = document.getElementById('main-product-image');
        
        thumbnails.forEach(thumbnail => {
            thumbnail.addEventListener('click', function() {
                // Remove active class from all thumbnails
                thumbnails.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked thumbnail
                this.classList.add('active');
                
                // Update main image
                mainImage.src = this.dataset.image;
            });
        });
        
        // Quantity buttons
        const quantityInput = document.querySelector('.product-quantity input');
        const decreaseBtn = document.querySelector('.quantity-decrease');
        const increaseBtn = document.querySelector('.quantity-increase');
        
        if (quantityInput) {
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value);
                const maxValue = parseInt(quantityInput.getAttribute('max'));
                if (currentValue < maxValue) {
                    quantityInput.value = currentValue + 1;
                }
            });
        }
        
        // Star rating
        const stars = document.querySelectorAll('.star-rating .stars i');
        const ratingInput = document.getElementById('rating');
        
        if (stars.length) {
            stars.forEach(star => {
                star.addEventListener('mouseover', function() {
                    const rating = this.dataset.rating;
                    
                    // Reset all stars
                    stars.forEach(s => s.className = 'far fa-star');
                    
                    // Highlight stars up to hovered star
                    for (let i = 0; i < rating; i++) {
                        stars[i].className = 'fas fa-star active';
                    }
                });
                
                star.addEventListener('mouseout', function() {
                    const currentRating = ratingInput.value;
                    
                    // Reset all stars
                    stars.forEach(s => s.className = 'far fa-star');
                    
                    // Highlight stars up to selected rating
                    for (let i = 0; i < currentRating; i++) {
                        stars[i].className = 'fas fa-star active';
                    }
                });
                
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;
                    
                    // Highlight stars up to clicked star
                    stars.forEach((s, index) => {
                        s.className = index < rating ? 'fas fa-star active' : 'far fa-star';
                    });
                });
            });
        }
    });
</script>
@endpush