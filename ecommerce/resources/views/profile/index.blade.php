@extends('layouts.app')

@section('title', 'My Account')

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
        
        <!-- Profile Content -->
        <div class="col-lg-9">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">My Profile</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        @if(session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-4 mb-3 text-center">
                                <div class="profile-photo-container mb-3">
                                    @if($user->profile_photo_path)
                                        <img src="{{ asset('storage/' . $user->profile_photo_path) }}" alt="{{ $user->name }}" class="img-thumbnail rounded-circle" id="profilePhotoPreview">
                                    @else
                                        <div class="avatar-placeholder rounded-circle bg-primary" id="profilePhotoPreview">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="mb-3">
                                    <label for="profile_photo" class="form-label">Profile Photo</label>
                                    <input type="file" class="form-control @error('profile_photo') is-invalid @enderror" id="profile_photo" name="profile_photo" accept="image/*">
                                    @error('profile_photo')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name', $user->userDetail->first_name ?? '') }}">
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name', $user->userDetail->last_name ?? '') }}">
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->userDetail->phone ?? '') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="birth_date" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control @error('birth_date') is-invalid @enderror" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->userDetail->birth_date ?? '') }}">
                                    @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $user->userDetail->gender ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $user->userDetail->gender ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ old('gender', $user->userDetail->gender ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                        <option value="prefer_not_to_say" {{ old('gender', $user->userDetail->gender ?? '') == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Account Activity</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4">
                            <div class="activity-box">
                                <div class="activity-icon">
                                    <i class="fas fa-shopping-bag fa-2x text-primary"></i>
                                </div>
                                <div class="activity-details">
                                    <h3>{{ $ordersCount }}</h3>
                                    <p>Orders Placed</p>
                                    <a href="{{ route('profile.orders') }}" class="small">View Details</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="activity-box">
                                <div class="activity-icon">
                                    <i class="fas fa-star fa-2x text-warning"></i>
                                </div>
                                <div class="activity-details">
                                    <h3>{{ $reviewsCount }}</h3>
                                    <p>Reviews Written</p>
                                    <a href="{{ route('profile.reviews') }}" class="small">View Details</a>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-4">
                            <div class="activity-box">
                                <div class="activity-icon">
                                    <i class="fas fa-heart fa-2x text-danger"></i>
                                </div>
                                <div class="activity-details">
                                    <h3>{{ $wishlistCount }}</h3>
                                    <p>Wishlist Items</p>
                                    <a href="{{ route('profile.wishlist') }}" class="small">View Details</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="recent-orders">
                        <h6 class="mb-3">Recent Orders</h6>
                        
                        @if($recentOrders->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Order #</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentOrders as $order)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('profile.orders.show', $order->id) }}">
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="text-end">
                                <a href="{{ route('profile.orders') }}" class="btn btn-outline-primary btn-sm">View All Orders</a>
                            </div>
                        @else
                            <div class="text-center py-3">
                                <p class="text-muted">You haven't placed any orders yet.</p>
                                <a href="{{ route('products.index') }}" class="btn btn-primary">Start Shopping</a>
                            </div>
                        @endif
                    </div>
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
    
    .profile-photo-container {
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    
    .profile-photo-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-placeholder {
        width: 150px;
        height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 3rem;
        font-weight: bold;
        margin: 0 auto;
    }
    
    .activity-box {
        display: flex;
        align-items: center;
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        transition: all 0.3s ease;
    }
    
    .activity-box:hover {
        background-color: #e9ecef;
        transform: translateY(-5px);
    }
    
    .activity-icon {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
    }
    
    .activity-details h3 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0;
    }
    
    .activity-details p {
        margin-bottom: 0;
        color: #6c757d;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const profilePhoto = document.getElementById('profile_photo');
        const photoPreview = document.getElementById('profilePhotoPreview');
        
        profilePhoto.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if (photoPreview.tagName === 'IMG') {
                        photoPreview.src = e.target.result;
                    } else {
                        // Create new image element
                        const img = document.createElement('img');
                        img.id = 'profilePhotoPreview';
                        img.className = 'img-thumbnail rounded-circle';
                        img.src = e.target.result;
                        
                        photoPreview.parentNode.replaceChild(img, photoPreview);
                    }
                }
                
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
</script>
@endpush
                            