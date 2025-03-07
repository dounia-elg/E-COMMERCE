@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Shopping Cart</li>
        </ol>
    </nav>

    <h1 class="mb-4">Shopping Cart</h1>
    
    @if(!$cart || $cart->items->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart fa-4x text-muted"></i>
            </div>
            <h3>Your cart is empty</h3>
            <p class="text-muted">Looks like you haven't added any products to your cart yet.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Continue Shopping</a>
        </div>
    @else
        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Product</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Quantity</th>
                                        <th scope="col">Total</th>
                                        <th scope="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    @if($item->product->primaryImage)
                                                        <img src="{{ asset('storage/' . $item->product->primaryImage->image_path) }}" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item->product->name }}">
                                                    @else
                                                        <img src="{{ asset('images/no-image.jpg') }}" class="img-fluid rounded" style="width: 60px; height: 60px; object-fit: cover;" alt="{{ $item->product->name }}">
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="mb-0"><a href="{{ route('products.show', $item->product->slug) }}" class="text-dark text-decoration-none">{{ $item->product->name }}</a></h6>
                                                    <small class="text-muted">{{ $item->product->category->name }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($item->product->discount_percent)
                                                <span class="text-decoration-line-through text-muted">${{ number_format($item->product->price, 2) }}</span><br>
                                                <span class="text-primary">${{ number_format($item->product->discounted_price, 2) }}</span>
                                            @else
                                                <span class="text-primary">${{ number_format($item->product->price, 2) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <form action="{{ route('cart.update', $item->id) }}" method="POST" class="cart-quantity-form">
                                                @csrf
                                                @method('PATCH')
                                                <div class="input-group input-group-sm" style="width: 100px;">
                                                    <button type="button" class="btn btn-outline-secondary quantity-decrease">-</button>
                                                    <input type="number" name="quantity" class="form-control text-center" value="{{ $item->quantity }}" min="1" max="{{ $item->product->quantity }}">
                                                    <button type="button" class="btn btn-outline-secondary quantity-increase">+</button>
                                                </div>
                                            </form>
                                        </td>
                                        <td>
                                            <span class="fw-bold">${{ number_format($item->product->discounted_price * $item->quantity, 2) }}</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-link text-danger p-0">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i> Continue Shopping
                    </a>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fas fa-trash-alt me-2"></i> Clear Cart
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>${{ number_format($cart->subtotal, 2) }}</span>
                        </div>
                        
                        @php
                            $tax = $cart->subtotal * 0.1; // 10% tax rate
                            $shipping = $cart->subtotal > 50 ? 0 : 5.99; // Free shipping over $50
                            $total = $cart->subtotal + $tax + $shipping;
                        @endphp
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax (10%):</span>
                            <span>${{ number_format($tax, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span>{{ $shipping > 0 ? '$'.number_format($shipping, 2) : 'Free' }}</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Total:</span>
                            <span class="fw-bold fs-5">${{ number_format($total, 2) }}</span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="coupon" class="form-label">Coupon Code</label>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="coupon" placeholder="Enter coupon code">
                                <button class="btn btn-outline-secondary" type="button">Apply</button>
                            </div>
                        </div>
                        
                        <div class="d-grid">
                            <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-lg">
                                Proceed to Checkout
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5>We Accept</h5>
                        <div class="d-flex justify-content-between mt-3">
                            <img src="{{ asset('images/payment/visa.png') }}" alt="Visa" height="30">
                            <img src="{{ asset('images/payment/mastercard.png') }}" alt="MasterCard" height="30">
                            <img src="{{ asset('images/payment/amex.png') }}" alt="American Express" height="30">
                            <img src="{{ asset('images/payment/paypal.png') }}" alt="PayPal" height="30">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Quantity update handlers
        const quantityForms = document.querySelectorAll('.cart-quantity-form');
        
        quantityForms.forEach(form => {
            const input = form.querySelector('input[name="quantity"]');
            const decreaseBtn = form.querySelector('.quantity-decrease');
            const increaseBtn = form.querySelector('.quantity-increase');
            
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(input.value);
                if (currentValue > 1) {
                    input.value = currentValue - 1;
                    form.submit();
                }
            });
            
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(input.value);
                const maxValue = parseInt(input.getAttribute('max'));
                if (currentValue < maxValue) {
                    input.value = currentValue + 1;
                    form.submit();
                }
            });
            
            input.addEventListener('change', function() {
                form.submit();
            });
        });
    });
</script>
@endpush