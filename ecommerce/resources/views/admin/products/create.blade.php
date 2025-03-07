@extends('layouts.admin')

@section('title', 'Add New Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Add New Product</h1>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row">
                <div class="col-md-8">
                    <!-- Basic Information -->
                    <div class="border-bottom mb-4 pb-4">
                        <h5 class="mb-3">Basic Information</h5>
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="slug" class="form-label">Slug</label>
                            <div class="input-group">
                                <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}">
                                <button class="btn btn-outline-secondary" type="button" id="generateSlug">Generate</button>
                            </div>
                            <div class="form-text">Leave blank to auto-generate from product name.</div>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku') }}" required>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}" step="0.01" min="0" required>
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="discount_percent" class="form-label">Discount (%)</label>
                                    <input type="number" class="form-control @error('discount_percent') is-invalid @enderror" id="discount_percent" name="discount_percent" value="{{ old('discount_percent') }}" min="0" max="100">
                                    @error('discount_percent')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="quantity" class="form-label">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', 0) }}" min="0" required>
                            @error('quantity')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div class="border-bottom mb-4 pb-4">
                        <h5 class="mb-3">Description</h5>
                        
                        <div class="mb-3">
                            <label for="short_description" class="form-label">Short Description</label>
                            <textarea class="form-control @error('short_description') is-invalid @enderror" id="short_description" name="short_description" rows="3">{{ old('short_description') }}</textarea>
                            <div class="form-text">Brief summary of the product (recommended: 150-200 characters).</div>
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Full Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="6" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                                        <!-- Product Images -->
                                        <div class="mb-4">
                                            <h5 class="mb-3">Product Images</h5>
                                            
                                            <div class="mb-3">
                                                <label for="product_images" class="form-label">Upload Images <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control @error('product_images') is-invalid @enderror @error('product_images.*') is-invalid @enderror" id="product_images" name="product_images[]" multiple accept="image/*" required>
                                                <div class="form-text">You can upload multiple images. The first image will be set as the primary image.</div>
                                                @error('product_images')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                @error('product_images.*')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            
                                            <div class="image-preview-container row" id="imagePreviewContainer"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <!-- Settings -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h5 class="mb-0">Settings</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_active">Active</label>
                                                    <div class="form-text">Toggle to show/hide this product on the store.</div>
                                                </div>
                                                
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" {{ old('is_featured') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="is_featured">Featured</label>
                                                    <div class="form-text">Toggle to mark this product as featured.</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- SEO -->
                                        <div class="card mb-4">
                                            <div class="card-header">
                                                <h5 class="mb-0">SEO</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <label for="meta_title" class="form-label">Meta Title</label>
                                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title') }}">
                                                    <div class="form-text">Leave blank to use the product name.</div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="meta_description" class="form-label">Meta Description</label>
                                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                                                    <div class="form-text">Leave blank to use the short description.</div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords') }}">
                                                    <div class="form-text">Comma-separated list of keywords.</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <hr>
                                
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                                    <button type="submit" class="btn btn-primary">Create Product</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endsection
                    
                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            // Generate slug from product name
                            const nameInput = document.getElementById('name');
                            const slugInput = document.getElementById('slug');
                            const generateSlugBtn = document.getElementById('generateSlug');
                            
                            function createSlug(text) {
                                return text.toLowerCase()
                                    .replace(/[^\w\s-]/g, '') // Remove non-word chars
                                    .replace(/\s+/g, '-')     // Replace spaces with -
                                    .replace(/-+/g, '-')      // Replace multiple - with single -
                                    .trim();                 // Trim leading/trailing whitespace
                            }
                            
                            generateSlugBtn.addEventListener('click', function() {
                                if (nameInput.value) {
                                    slugInput.value = createSlug(nameInput.value);
                                }
                            });
                            
                            // Image preview
                            const imageInput = document.getElementById('product_images');
                            const previewContainer = document.getElementById('imagePreviewContainer');
                            
                            imageInput.addEventListener('change', function() {
                                previewContainer.innerHTML = '';
                                
                                if (this.files) {
                                    Array.from(this.files).forEach((file, index) => {
                                        if (!file.type.match('image.*')) {
                                            return;
                                        }
                                        
                                        const reader = new FileReader();
                                        
                                        reader.onload = function(e) {
                                            const col = document.createElement('div');
                                            col.className = 'col-md-4 mb-3';
                                            
                                            const card = document.createElement('div');
                                            card.className = 'card h-100';
                                            
                                            const img = document.createElement('img');
                                            img.src = e.target.result;
                                            img.className = 'card-img-top';
                                            img.style.height = '150px';
                                            img.style.objectFit = 'cover';
                                            
                                            const cardBody = document.createElement('div');
                                            cardBody.className = 'card-body p-2';
                                            
                                            const isPrimary = document.createElement('div');
                                            isPrimary.className = 'form-check';
                                            
                                            const primaryInput = document.createElement('input');
                                            primaryInput.type = 'radio';
                                            primaryInput.className = 'form-check-input';
                                            primaryInput.name = 'primary_image';
                                            primaryInput.value = index;
                                            primaryInput.id = `primary_image_${index}`;
                                            if (index === 0) {
                                                primaryInput.checked = true;
                                            }
                                            
                                            const primaryLabel = document.createElement('label');
                                            primaryLabel.className = 'form-check-label';
                                            primaryLabel.htmlFor = `primary_image_${index}`;
                                            primaryLabel.textContent = 'Set as primary';
                                            
                                            isPrimary.appendChild(primaryInput);
                                            isPrimary.appendChild(primaryLabel);
                                            
                                            cardBody.appendChild(isPrimary);
                                            
                                            card.appendChild(img);
                                            card.appendChild(cardBody);
                                            
                                            col.appendChild(card);
                                            previewContainer.appendChild(col);
                                        };
                                        
                                        reader.readAsDataURL(file);
                                    });
                                }
                            });
                        });
                    </script>
                    @endpush
                