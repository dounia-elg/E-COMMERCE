
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('My Profile') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-4">
                            <h5>{{ __('Account Information') }}</h5>
                            <p><strong>Name:</strong> {{ $user->name }}</p>
                            <p><strong>Email:</strong> {{ $user->email }}</p>
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary">{{ __('Edit Profile') }}</a>
                            <a href="{{ route('profile.change-password') }}" class="btn btn-secondary mt-2">{{ __('Change Password') }}</a>
                        </div>
                        <div class="col-md-8">
                            <h5>{{ __('Contact Information') }}</h5>
                            <p><strong>Address:</strong> {{ $user->userDetail->address ?? 'Not specified' }}</p>
                            <p><strong>City:</strong> {{ $user->userDetail->city ?? 'Not specified' }}</p>
                            <p><strong>State:</strong> {{ $user->userDetail->state ?? 'Not specified' }}</p>
                            <p><strong>Country:</strong> {{ $user->userDetail->country ?? 'Not specified' }}</p>
                            <p><strong>Postal Code:</strong> {{ $user->userDetail->postal_code ?? 'Not specified' }}</p>
                            <p><strong>Phone:</strong> {{ $user->userDetail->phone ?? 'Not specified' }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="mt-3">
                        <h5>{{ __('Order History') }}</h5>
                        <a href="{{ route('profile.orders') }}" class="btn btn-info">{{ __('View My Orders') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection