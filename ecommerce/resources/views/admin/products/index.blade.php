@extends('layouts.admin')

@section('title', 'Manage Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Products</h1>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <form action="{{ route('admin.products.index') }}" method="GET" class="d-flex">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search products..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <div class="d-flex justify-content-end">
                    <div class="btn-group me-2">
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary {{ !request('stock') ? 'active' : '' }}">
                            All
                        </a>
                        <a href="{{ route('admin.products.index', ['stock' => 'in']) }}" class="btn btn-outline-secondary {{ request('stock') == 'in' ? 'active' : '' }}">
                            In Stock
                        </a>
                        <a href="{{ route('admin.products.index', ['stock' => 'low']) }}" class="btn btn-outline-secondary {{ request('stock') == 'low' ? 'active' : '' }}">
                            Low Stock
                        </a>
                        <a href="{{ route('admin.products.index', ['stock' => 'out']) }}" class="btn btn-outline-secondary {{ request('stock') == 'out' ? 'active' : '' }}">
                            Out of Stock
                        </a>
                    </div>
                    
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            {{ request('category') ? App\Models\Category::find(request('category'))->name : 'All Categories' }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="categoryDropdown">
                            <li><a class="dropdown-item {{ !request('category') ? 'active' : '' }}" href="{{ route('admin.products.index', array_merge(request()->except('category'), ['page' => 1])) }}">All Categories</a></li>
                            @foreach($categories as $category)
                                <li><a class="dropdown-item {{ request('category') == $category->id ? 'active' : '' }}" href="{{ route('admin.products.index', array_merge(request()->except('category'), ['category' => $category->id, 'page' => 1])) }}">{{ $category->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50">ID</th>
                            <th width="80">Image</th>
                            <th>Product</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Discount</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th width="120">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @if($product->primaryImage)
                                        <img src="{{ asset('storage/' . $product->primaryImage->image_path) }}" alt="{{ $product->name }}" class="img-thumbnail" width="60">
                                    @else
                                        <img src="{{ asset('images/no-image.jpg') }}" alt="{{ $product->name }}" class="img-thumbnail" width="60">
                                    @endif
                                </td>
                                <td>
                                    {{ $product->name }}
                                    <div class="text-muted small">SKU: {{ $product->sku }}</div>
                                </td>
                                <td>{{ $product->category->name }}</td>
                                <td>${{ number_format($product->price, 2) }}</td>
                                <td>
                                    @if($product->discount_percent)
                                        {{ $product->discount_percent }}%
                                        <div class="text-muted small">${{ number_format($product->discounted_price, 2) }}</div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($product->quantity > 10)
                                        <span class="badge bg-success">{{ $product->quantity }}</span>
                                    @elseif($product->quantity > 0)
                                        <span class="badge bg-warning text-dark">{{ $product->quantity }}</span>
                                    @else
                                        <span class="badge bg-danger">Out of Stock</span>
                                    @endif
                                </td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('products.show', $product->slug) }}" class="btn btn-sm btn-outline-primary" target="_blank" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-outline-info" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $product->id }}" title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $product->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $product->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $product->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>Are you sure you want to delete the product <strong>{{ $product->name }}</strong>?</p>
                                                    <p class="text-danger">This action cannot be undone.</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-box-open fa-4x text-muted"></i>
                </div>
                <h4>No products found</h4>
                <p class="text-muted">
                    @if(request('search'))
                        No products matching "{{ request('search') }}" were found.
                    @elseif(request('category'))
                        No products found in this category.
                    @elseif(request('stock') == 'out')
                        There are no out of stock products.
                    @elseif(request('stock') == 'low')
                        There are no low stock products.
                    @else
                        There are no products in the database.
                    @endif
                </p>
                <div class="mt-3">
                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">Add New Product</a>
                    @if(request('search') || request('category') || request('stock'))
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary ms-2">Clear Filters</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
                