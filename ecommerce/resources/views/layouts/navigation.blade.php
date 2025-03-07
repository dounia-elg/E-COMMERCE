<header>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Laravel') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('products.index') }}">{{ __('Products') }}</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ __('Categories') }}
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            @php
                                $categories = \App\Models\Category::where('is_active', true)->take(8)->get();
                            @endphp
                            
                            @foreach($categories as $category)
                                <li><a class="dropdown-item" href="{{ route('products.category', $category->slug) }}">{{ $category->name }}</a></li>
                            @endforeach
                            
                            @if(\App\Models\Category::count() > 8)
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('products.index') }}">{{ __('All Categories') }}</a></li>
                            @endif
                        </ul>
                    </li>
                </ul>

                <!-- Search Form -->
                <form class="d-flex mx-auto" action="{{ route('products.search') }}" method="GET">
                    <div class="input-group">
                        <input class="form-control" type="search" name="search" placeholder="{{ __('Search products...') }}" aria-label="Search" required>
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </form>

                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ms-auto">
                    <!-- Cart -->
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.index') }}">
                            <i class="fa fa-shopping-cart"></i> 
                            <span class="badge bg-primary cart-count">
                                @php
                                    if (Auth::check()) {
                                        $cart = \App\Models\Cart::where('user_id', Auth::id())->first();
                                    } else {
                                        $sessionId = session()->get('cart_session_id');
                                        $cart = $sessionId ? \App\Models\Cart::where('session_id', $sessionId)->first() : null;
                                    }
                                    
                                    $cartCount = $cart ? $cart->items->sum('quantity') : 0;
                                @endphp
                                {{ $cartCount }}
                            </span>
                        </a>
                    </li>
                    
                    <!-- Authentication Links -->
                    @guest
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @else
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ Auth::user()->name }}
                            </a>

                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                @if(Auth::user()->isAdmin())
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fa fa-tachometer-alt fa-fw"></i> {{ __('Admin Dashboard') }}
                                    </a>
                                    <div class="dropdown-divider"></div>
                                @endif
                                
                                <a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fa fa-user fa-fw"></i> {{ __('My Profile') }}
                                </a>
                                
                                <a class="dropdown-item" href="{{ route('profile.orders') }}">
                                    <i class="fa fa-shopping-bag fa-fw"></i> {{ __('My Orders') }}
                                </a>
                                
                                <div class="dropdown-divider"></div>
                                
                                <a class="dropdown-item" href="{{ route('logout') }}"
                                   onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                    <i class="fa fa-sign-out-alt fa-fw"></i> {{ __('Logout') }}
                                </a>

                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        </li>
                    @endguest
                </ul>
            </div>
        </div>
    </nav>
</header>