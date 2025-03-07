@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">Shopping Cart</a></li>
            <li class="breadcrumb-item active" aria-current="page">Checkout</li>
        </ol>
    </nav>

    <h1 class="mb-4">Checkout</h1>
    
    @if(!$cart || $cart->items->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="fas fa-shopping-cart fa-4x text-muted"></i>
            </div>
            <h3>Your cart is empty</h3>
            <p class="text-muted">You need to add products to your cart before proceeding to checkout.</p>
            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Browse Products</a>
        </div>
    @else
        <form action="{{ route('checkout.process') }}" method="POST" id="checkout-form">
            @csrf
            <div class="row">
                <div class="col-lg-8">
                    <!-- Shipping Information -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Shipping Information</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', auth()->user()->userDetail->first_name ?? '') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', auth()->user()->userDetail->last_name ?? '') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', auth()->user()->userDetail->phone ?? '') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', auth()->user()->userDetail->address ?? '') }}" required>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="address2" class="form-label">Address Line 2 <span class="text-muted">(Optional)</span></label>
                                <input type="text" class="form-control @error('address2') is-invalid @enderror" id="address2" name="address2" value="{{ old('address2', auth()->user()->userDetail->address2 ?? '') }}">
                                @error('address2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-5 mb-3">
                                    <label for="country" class="form-label">Country <span class="text-danger">*</span></label>
                                    <select class="form-select @error('country') is-invalid @enderror" id="country" name="country" required>
                                        <option value="">Choose...</option>
                                        <option value="US" {{ old('country', auth()->user()->userDetail->country ?? '') == 'US' ? 'selected' : '' }}>United States</option>
                                        <option value="CA" {{ old('country', auth()->user()->userDetail->country ?? '') == 'CA' ? 'selected' : '' }}>Canada</option>
                                        <option value="UK" {{ old('country', auth()->user()->userDetail->country ?? '') == 'UK' ? 'selected' : '' }}>United Kingdom</option>
                                        <!-- Add more countries -->
                                    </select>
                                    @error('country')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="state" class="form-label">State <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('state') is-invalid @enderror" id="state" name="state" value="{{ old('state', auth()->user()->userDetail->state ?? '') }}" required>
                                    @error('state')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="postal_code" class="form-label">Postal Code <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" name="postal_code" value="{{ old('postal_code', auth()->user()->userDetail->postal_code ?? '') }}" required>
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Payment Method -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Payment Method</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="payment-methods">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_card" value="card" {{ old('payment_method') == 'card' ? 'checked' : 'checked' }}>
                                    <label class="form-check-label" for="payment_method_card">
                                        Credit / Debit Card
                                    </label>
                                </div>
                                
                                <div class="card-payment-form mb-4 {{ old('payment_method') == 'paypal' ? 'd-none' : '' }}">
                                    <div class="mb-3">
                                        <label for="card_number" class="form-label">Card Number <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control @error('card_number') is-invalid @enderror" id="card_number" name="card_number" value="{{ old('card_number') }}" placeholder="1234 5678 9012 3456">
                                            <span class="input-group-text">
                                                <i class="fab fa-cc-visa me-1"></i>
                                                <i class="fab fa-cc-mastercard me-1"></i>
                                                <i class="fab fa-cc-amex"></i>
                                            </span>
                                        </div>
                                        @error('card_number')
                                            <div class="invalid-feedback d-block">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="expiry_date" class="form-label">Expiration Date <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('expiry_date') is-invalid @enderror" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" placeholder="MM/YY">
                                            @error('expiry_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="cvv" class="form-label">CVV <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('cvv') is-invalid @enderror" id="cvv" name="cvv" value="{{ old('cvv') }}" placeholder="123">
                                            @error('cvv')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="card_name" class="form-label">Name on Card <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('card_name') is-invalid @enderror" id="card_name" name="card_name" value="{{ old('card_name') }}">
                                        @error('card_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_paypal" value="paypal" {{ old('payment_method') == 'paypal' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_method_paypal">
                                        <i class="fab fa-paypal text-primary me-2"></i> PayPal
                                    </label>
                                </div>
                                
                                <div class="paypal-info {{ old('payment_method') == 'paypal' ? '' : 'd-none' }}">
                                    <div class="alert alert-info">
                                        <p class="mb-0">You will be redirected to PayPal to complete your purchase securely.</p>
                                    </div>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_cash" value="cash" {{ old('payment_method') == 'cash' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="payment_method_cash">
                                        <i class="fas fa-money-bill-wave text-success me-2"></i> Cash on Delivery
                                    </label>
                                </div>
                                
                                <div class="cash-info {{ old('payment_method') == 'cash' ? '' : 'd-none' }}">
                                    <div class="alert alert-warning">
                                        <p class="mb-0">Please have the exact amount ready when the delivery arrives. Our delivery personnel cannot provide change.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Notes -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Notes</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-0">
                                <label for="notes" class="form-label">Notes about your order <span class="text-muted">(Optional)</span></label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                                <div class="form-text">If you have specific delivery instructions or information about your order, please add them here.</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Order Summary -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Order Summary</h5>
                        </div>
                        <div class="card-body">
                            <div class="order-items mb-3">
                                @foreach($cart->items as $item)
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <span class="fw-medium">{{ $item->quantity }}x</span> {{ $item->product->name }}
                                    </div>
                                    <div>
                                        ${{ number_format($item->product->discounted_price * $item->quantity, 2) }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            
                            <hr>
                            
                            @php
                                $subtotal = $cart->subtotal;
                                $tax = $subtotal * 0.1; // 10% tax
                                $shipping = $subtotal > 50 ? 0 : 5.99; // Free shipping over $50
                                $total = $subtotal + $tax + $shipping;
                            @endphp
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>${{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Tax (10%):</span>
                                <span>${{ number_format($tax, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>{{ $shipping > 0 ? '$'.number_format($shipping, 2) : 'Free' }}</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold fs-5">${{ number_format($total, 2) }}</span>
                            </div>
                            
                            <div class="mt-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input @error('terms') is-invalid @enderror" type="checkbox" id="terms" name="terms" {{ old('terms') ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>
                                    </label>
                                    @error('terms')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        Place Order
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Secure Checkout Info -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <i class="fas fa-shield-alt text-success fa-2x"></i>
                                <h5 class="mt-2">Secure Checkout</h5>
                            </div>
                            <p class="text-center text-muted small mb-3">Your payment information is processed securely. We do not store your credit card details.</p>
                            <div class="d-flex justify-content-between mb-2">
                                <img src="{{ asset('images/payment/visa.png') }}" alt="Visa" height="25">
                                <img src="{{ asset('images/payment/mastercard.png') }}" alt="Mastercard" height="25">
                                <img src="{{ asset('images/payment/amex.png') }}" alt="Amex" height="25">
                                <img src="{{ asset('images/payment/paypal.png') }}" alt="PayPal" height="25">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif
    
    <!-- Terms & Conditions Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                    <h5>1. Introduction</h5>
                    <p>These terms and conditions govern your use of our website and the purchase of products from our online store.</p>
                    
                    <h5>2. Order Acceptance</h5>
                    <p>When you place an order with us, you are making an offer to purchase products. We reserve the right to accept or decline your order for any reason.</p>
                    
                    <h5>3. Pricing and Payment</h5>
                    <p>All prices are in USD and inclusive of VAT where applicable. Payment must be made in full before the order is processed.</p>
                    
                    <h5>4. Delivery</h5>
                    <p>We aim to deliver products within the estimated delivery time specified during checkout. However, delays may occasionally occur.</p>
                    
                    <h5>5. Returns and Refunds</h5>
                    <p>You have 14 days from receipt of your order to return unwanted items. Products must be in original condition.</p>
                    
                    <h5>6. Privacy</h5>
                    <p>We respect your privacy and will handle your personal information in accordance with our Privacy Policy.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Payment method toggle
        const paymentMethodInputs = document.querySelectorAll('input[name="payment_method"]');
        const cardPaymentForm = document.querySelector('.card-payment-form');
        const paypalInfo = document.querySelector('.paypal-info');
        const cashInfo = document.querySelector('.cash-info');
        
        paymentMethodInputs.forEach(input => {
            input.addEventListener('change', function() {
                if (this.value === 'card') {
                    cardPaymentForm.classList.remove('d-none');
                    paypalInfo.classList.add('d-none');
                    cashInfo.classList.add('d-none');
                } else if (this.value === 'paypal') {
                    cardPaymentForm.classList.add('d-none');
                    paypalInfo.classList.remove('d-none');
                    cashInfo.classList.add('d-none');
                } else if (this.value === 'cash') {
                    cardPaymentForm.classList.add('d-none');
                    paypalInfo.classList.add('d-none');
                    cashInfo.classList.remove('d-none');
                }
            });
        });
        
        // Card input formatting
        const cardNumberInput = document.getElementById('card_number');
        const expiryDateInput = document.getElementById('expiry_date');
        const cvvInput = document.getElementById('cvv');
        
        if (cardNumberInput) {
            cardNumberInput.addEventListener('input', function(e) {
                // Remove any non-digit characters
                let value = this.value.replace(/\D/g, '');
                
                // Add a space after every 4 digits
                value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                
                // Limit to 19 characters (16 digits + 3 spaces)
                value = value.substr(0, 19);
                
                this.value = value;
            });
        }
        
        if (expiryDateInput) {
            expiryDateInput.addEventListener('input', function(e) {
                // Remove any non-digit characters
                let value = this.value.replace(/\D/g, '');
                
                // Add a slash after the first 2 digits
                if (value.length > 2) {
                    value = value.substr(0, 2) + '/' + value.substr(2);
                }
                
                // Limit to 5 characters (MM/YY)
                value = value.substr(0, 5);
                
                this.value = value;
            });
        }
        
        if (cvvInput) {
            cvvInput.addEventListener('input', function(e) {
                // Remove any non-digit characters
                let value = this.value.replace(/\D/g, '');
                
                // Limit to 3-4 digits
                value = value.substr(0, 4);
                
                this.value = value;
            });
        }
    });
</script>
@endpush