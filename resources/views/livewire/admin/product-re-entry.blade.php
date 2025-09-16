<div class="container-fluid py-4">
    <div class="card border-0 shadow">
        <!-- Card Header -->
        <div class="card-header text-white p-2 rounded-t-4 d-flex align-items-center"
            style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%); border-radius: 20px 20px 0 0;">
            <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center me-3">
                <i class="bi bi-shield-lock text-white fs-4" aria-hidden="true"></i>
            </div>
            <div>
                <h3 class="mb-1 fw-bold tracking-tight text-white">Product Re-Entry</h3>
                <p class="text-white opacity-80 mb-0 text-sm">Monitor and manage your product Re-enteries</p>
            </div>
        </div>
        <!-- Search Bar -->
        <div class="card-header bg-transparent py-3 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 border-bottom" style="border-color: #233D7F;">
            <div class="flex-grow-1 d-flex justify-content-lg">
                <div class="input-group" style="box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                    <span class="input-group-text bg-gray-100 border-0 px-3">
                        <i class="bi bi-search text-danger"></i>
                    </span>
                    <input type="text"
                        class="form-control border-0"
                        placeholder="Search products..."
                        wire:model.live.debounce.300ms="search"
                        autocomplete="off">
                </div>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-3 bg-transparent">
            <div class="row">
                <!-- Left Column: Product List -->
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <h5 class="mb-3 fw-semibold">Products</h5>
                    <div class="scrollable-container pe-2">
                        <div class="row g-3">
                            @forelse($products as $index => $product)
                            <div class="col-md-6">
                                <div class="product-card card h-100 border shadow-sm {{ $selectedProductId == $product->id ? 'selected' : '' }}"
                                    wire:click="selectProduct({{ $product->id }})">
                                    <div class="card-body p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <span class="badge bg-success">{{ $product->product_code }}</span>
                                            <span class="badge"
                                                style="background-color: {{ $product->stock_quantity > 0 ? '#22c55e' : '#ef4444' }};
                                                             color: #ffffff; padding: 4px 8px; border-radius: 9999px; font-weight: 500;">
                                                {{ $product->stock_quantity }} available
                                            </span>
                                        </div>
                                        <h6 class="card-title mb-1">{{ $product->product_name }}</h6>
                                        <p class="text-muted small mb-2">{{ $product->category?->name ?? 'N/A' }}</p>
                                        <div class="d-flex justify-content-between text-muted small">
                                            <span>Damage: {{ $product->damage_quantity }}</span>
                                            <span>Total: {{ $product->sold + $product->stock_quantity + $product->damage_quantity }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="text-center py-5">
                                    <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                                        <i class="bi bi-box-seam text-gray-600 fs-3"></i>
                                    </div>
                                    <h5 class="text-gray-600 fw-normal">No Product Stock Found</h5>
                                    <p class="text-sm text-gray-500 mb-0">No matching results found for the current search.</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $products->links('pagination::bootstrap-5') }}
                    </div>
                </div>

                <!-- Right Column: Re-Entry Form -->
                <div class="col-lg-6">
                    <div class="sticky-top" style="top: 20px;">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-light py-3">
                                <h5 class="mb-0">Stock Re-Entry Form</h5>
                            </div>
                            <div class="card-body">
                                @if($selectedProduct)
                                <div class="alert alert-danger d-flex align-items-center mb-4">
                                    <span>Editing stock for: <strong>{{ $selectedProduct->product_name }}</strong></span>
                                </div>

                                <form wire:submit.prevent="updateStock">
                                    <div class="row">
                                        <div class=" col-md-6 mb-3">
                                            <label class="form-label">Current Available Stock</label>
                                            <input type="text" class="form-control" value="{{ $selectedProduct->stock_quantity }}" disabled>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Current Damage Quantity</label>
                                            <input type="text" class="form-control" value="{{ $selectedProduct->damage_quantity }}" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Add Re-entry Stock</label>
                                            <input type="number" class="form-control" wire:model="addStock" min="0" placeholder="Enter quantity">
                                            @error('addStock') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Add to Damage Stock</label>
                                            <input type="number" class="form-control" wire:model="addDamage" min="0" placeholder="Enter quantity">
                                            @error('addDamage') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    </div>


                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-md mx-auto" style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">
                                            <i class="bi bi-check-circle me-2"></i>Update Stock
                                        </button>
                                    </div>
                                </form>
                                @else
                                <div class="text-center py-5">
                                    <div class="mb-3">
                                        <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="text-muted">Select a Product</h5>
                                    <p class="text-muted">Choose a product from the list to manage its stock</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('style')
<style>
    .tracking-tight {
        letter-spacing: -0.025em;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .icon-shape {
        width: 2rem;
        height: 2rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-shape.icon-lg {
        width: 3rem;
        height: 3rem;
    }

    .product-card {
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .product-card:hover,
    .product-card.selected {
        box-shadow: 0 0 0 2px #d34d51ff;
        transform: translateY(-2px);
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #d34d51ff;
        box-shadow: 0 0 0 0.25rem rgba(211, 77, 81, 0.25);
    }

    .btn-primary {
        background-color: #d34d51ff;
        border-color: #d34d51ff;
    }

    .btn-primary:hover {
        background-color: #9d1c20;
        border-color: #9d1c20;
    }

    .gradient-bg {
        background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%);
    }

    .scrollable-container {
        max-height: calc(100vh - 200px);
        overflow-y: auto;
    }
</style>
@endpush