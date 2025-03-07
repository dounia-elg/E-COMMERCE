@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Profile Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="avatar-circle mb-3">
                        @if($user->profile_photo_path)
                            <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="rounded-circle">
                        @else
                            <div class="avatar-text rounded-circle bg-primary">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    <p class="small text-muted">Member since {{ $user->created_at->format('M Y') }}</p>
                </div>
            </div>
            
            <div class="list-group shadow-sm mt-4">
                <a href="{{ route('profile.index') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.index') ? 'active' : '' }}">
                    <i class="fas fa-user me-2"></i> My Profile
                </a>
                <a href="{{ route('profile.orders') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.orders') ? 'active' : '' }}">
                    <i class="fas fa-shopping-bag me-2"></i> Orders
                    <span class="badge bg-primary rounded-pill float-end">{{ $ordersCount }}</span>
                </a>
                <a href="{{ route('profile.addresses') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.addresses') ? 'active' : '' }}">
                    <i class="fas fa-map-marker-alt me-2"></i> Addresses
                </a>
                <a href="{{ route('profile.reviews') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.reviews') ? 'active' : '' }}">
                    <i class="fas fa-star me-2"></i> Reviews
                    <span class="badge bg-primary rounded-pill float-end">{{ $reviewsCount }}</span>
                </a>
                <a href="{{ route('profile.wishlist') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.wishlist') ? 'active' : '' }}">
                    <i class="fas fa-heart me-2"></i> Wishlist
                    <span class="badge bg-primary rounded-pill float-end">{{ $wishlistCount }}</span>
                </a>
                <a href="{{ route('profile.settings') }}" class="list-group-item list-group-item-action {{ request()->routeIs('profile.settings') ? 'active' : '' }}">
                    <i class="fas fa-cog me-2"></i> Account Settings
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="list-group-item list-group-item-action text-danger">
                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Order Content -->
        <div class="col-lg-9">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">My Orders</h5>
                </div>
                <div class="card-body">
                    @if($orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Order #</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($orders as $order)
                                        <tr>
                                            <td>
                                                <a href="{{ route('profile.orders.show', $order->id) }}" class="text-decoration-none">
                                                    #{{ $order->order_number }}
                                                </a>
                                            </td>
                                            <td>{{ $order->created_at->format('M d, Y') }}</td>
                                            <td>{{ $order->items->sum('quantity') }}</td>
                                            <td>${{ number_format($order->total_amount, 2) }}</td>
                                            <td>
                                                @if($order->status == 'pending')
                                                    <span class="badge bg-warning text-dark">Pending</span>
                                                @elseif($order->status == 'processing')
                                                    <span class="badge bg-info text-white">Processing</span>
                                                @elseif($order->status == 'completed')
                                                    <span class="badge bg-success">Completed</span>
                                                @elseif($order->status == 'cancelled')
                                                    <span class="badge bg-danger">Cancelled</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('profile.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                                    Details
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $orders->links() }}
                        </div>
                    @else
                        <div class="text-center py-4">
                            <div class="mb-3">
                                <i class="fas fa-shopping-bag fa-4x text-muted"></i>
                            </div>
                            <h5>No Orders Found</h5>
                            <p class="text-muted">You haven't placed any orders yet.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary mt-3">Start Shopping</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        margin: 0 auto;
    }
    
    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border: 2px solid #eee;
    }
    
    .avatar-text {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
        font-weight: bold;
    }
</style>
@endpush