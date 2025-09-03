<div>
    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6>Product Billing System</h6>
                    </div>
                    <div class="card-body">
                        <!-- Search Section -->
                        <div class="row mb-4">
                            <div class="col-md-6 mx-auto">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                        placeholder="Search by code, name, or brand..."
                                        wire:model.live="search" autocomplete="off">
                                </div>

                                <!-- Search Results Dropdown -->
                                @if ($search && count($searchResults) > 0)
                                <div class="search-results-container position-relative mt-1 w-100 bg-white shadow-lg rounded z-index-1000"
                                    style="max-height: 350px; overflow-y: auto;">
                                    @foreach ($searchResults as $result)
                                    <div class="search-result-item p-2 border-bottom"
                                        wire:key="result-{{ $result->id }}">
                                        <div class="d-flex align-items-stretch position-relative">
                                            <!-- Product Image -->
                                            <div class="product-image me-3" style="min-width: 60px;">
                                                @if ($result->image)
                                                <img src="{{ asset('storage/' . $result->image) }}"
                                                    alt="{{ $result->name }}" class="img-fluid rounded"
                                                    style="width: 60px; height: 60px; object-fit: cover;">
                                                @else
                                                <div class="no-image bg-light d-flex align-items-center justify-content-center rounded"
                                                    style="width: 60px; height: 60px;">
                                                    <i class="fas fa-box text-muted"></i>
                                                </div>
                                                @endif
                                            </div>

                                            <!-- Vertical divider -->
                                            <div class="vr mx-2 h-100"></div>

                                            <!-- Product Info -->
                                            <div class="product-info flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="mb-0 fw-bold">
                                                        {{ $result->product_name ?? 'Unnamed Product' }}
                                                    </h6>
                                                    <div>
                                                        <span
                                                            class="badge bg-success">Rs.{{ $result->selling_price ?? '-' }}</span>
                                                        <span class="badge bg-info">Stock:
                                                            {{ $result->stock ?? '-' }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-muted small mt-1">
                                                    <span class="me-2">Code: {{ $result->code ?? 'Null' }}</span> |
                                                    <span class="mx-2">Brand: {{ $result->brand ?? 'Null' }}</span> |
                                                    <span class="ms-2">Category: {{ $result->category_name ?? 'Null' }}</span>
                                                </div>
                                            </div>

                                            <!-- Vertical divider -->
                                            <div class="vr mx-2 h-100"></div>

                                            <!-- Product Action -->
                                            <div class="product-action d-flex align-items-center">
                                                <button class="btn btn-sm btn-primary"
                                                    wire:click="addToCart({{ $result->id }})"
                                                    <i class="fas fa-plus"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @elseif($search && count($searchResults) == 0)
                                <div
                                    class="search-results-container position-absolute mt-1 w-100 bg-white shadow-lg rounded">
                                    <div class="p-3 text-center text-muted">
                                        No products found matching "{{ $search }}"
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Cart Table -->
                        <div class="table-responsive shadow-sm rounded-lg bg-white p-3">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bold">Product</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bold text-center">Price</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bold text-center">Qty</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bold text-center">Discount</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bold text-center">Total</th>
                                        <th class="text-uppercase text-secondary text-xs font-weight-bold text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cart as $id => $item)
                                    <tr wire:key="cart-{{ $id }}" class="border-bottom">
                                        <!-- Product Details -->
                                        <td>
                                            <h6 class="mb-1 text-sm fw-semibold">{{ $item['name'] }}</h6>
                                            <small class="text-muted">
                                                {{ $item['code'] ?? 'N/A' }} | {{ $item['brand'] ?? 'Unbranded' }}
                                            </small>
                                        </td>

                                        <!-- Price -->
                                        <td class="text-center">
                                            Rs.{{ number_format($item['discountPrice'] ?: $item['price'], 2) }}
                                        </td>

                                        <!-- Quantity -->
                                        <td class="text-center">
                                            <div class="input-group input-group-sm mx-auto" style="max-width: 120px;">
                                                <button class="btn btn-outline-primary btn-sm"
                                                    wire:click="updateQuantity({{ $id }}, {{ $quantities[$id] - 1 }})"
                                                    {{ $quantities[$id] <= 1 ? 'disabled' : '' }}>-</button>
                                                <input type="number"
                                                    class="form-control form-control-sm text-center"
                                                    value="{{ $quantities[$id] }}" min="1"
                                                    max="{{ $item['inStock'] }}"
                                                    wire:change="updateQuantity({{ $id }}, $event.target.value)">
                                                <button class="btn btn-outline-primary btn-sm"
                                                    wire:click="updateQuantity({{ $id }}, {{ $quantities[$id] + 1 }})"
                                                    {{ $quantities[$id] >= $item['inStock'] ? 'disabled' : '' }}>+</button>
                                            </div>
                                            @if($quantities[$id] >= $item['inStock'])
                                            <small class="text-danger d-block mt-1">Max: {{ $item['inStock'] }}</small>
                                            @endif
                                        </td>

                                        <!-- Discount -->
                                        <td class="text-center">
                                            <div class="input-group input-group-sm mx-auto" style="max-width: 100px;">
                                                <span class="input-group-text">Rs</span>
                                                <input type="number" class="form-control form-control-sm"
                                                    value="{{ $discounts[$id] ?? 0 }}" min="0"
                                                    max="{{ $item['price'] }}" step="0.01"
                                                    wire:change="updateDiscount({{ $id }}, $event.target.value)">
                                            </div>
                                        </td>

                                        <!-- Total -->
                                        <td class="text-center fw-bold">
                                            Rs.{{ number_format((($item['discountPrice'] ?: $item['price']) - ($discounts[$id] ?? 0)) * $quantities[$id], 2) }}
                                        </td>

                                        <!-- Actions -->
                                        <td class="text-center">
                                            <button class="btn btn-sm btn-outline-info me-2" wire:click="showDetail({{ $id }})">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" wire:click="removeFromCart({{ $id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="fas fa-shopping-cart fa-3x mb-2"></i>
                                            <p>Your cart is empty. Search and add products to create a bill.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>


                        @if (!empty($cart))
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="staff" class="form-label">Assign to Staff <span class="text-danger">*</span></label>
                                    <select wire:model="selectedStaffId" id="staff" class="form-select @error('selectedStaffId') is-invalid @enderror">
                                        <option value="">Select a staff member</option>
                                        @foreach($staffs as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('selectedStaffId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Order Summary -->
                        @if (!empty($cart))
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card mt-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0 fw-bold">Order Summary</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Subtotal:</span>
                                            <span>Rs.{{ number_format($subtotal, 2) }}</span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <span>Total Discount:</span>
                                            <span>Rs.{{ number_format($totalDiscount, 2) }}</span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold">Grand Total:</span>
                                            <span class="fw-bold">Rs.{{ number_format($grandTotal, 2) }}</span>
                                        </div>

                                        <div class="d-flex mt-4">
                                            <button class="btn btn-danger me-2" wire:click="clearCart">
                                                <i class="fas fa-times me-2"></i>Clear
                                            </button>
                                            <button class="btn btn-success flex-grow-1" wire:click="completeSale">
                                                <i class="fas fa-check me-2"></i>Complete Sale
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- View Product Modal -->
            <div wire:ignore.self class="modal fade" id="viewDetailModal" tabindex="-1"
                aria-labelledby="viewDetailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h1 class="modal-title fs-5 text-white" id="viewDetailModalLabel">Product Details</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        @if ($productDetails)
                        <div class="modal-body p-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-0">
                                    <div class="row g-0">
                                        <!-- Image Column -->
                                        <div class="col-md-4 border-end">
                                            <div class="position-relative h-100">
                                                @if ($productDetails->image)
                                                <img src="{{ asset('storage/' . $productDetails->image) }}"
                                                    alt="{{ $productDetails->name }}"
                                                    class="img-fluid rounded-start h-100 w-100 object-fit-cover">
                                                @else
                                                <div
                                                    class="bg-light d-flex align-items-center justify-content-center h-100">
                                                    <i class="bi bi-box text-muted"
                                                        style="font-size: 5rem;"></i>
                                                    <p class="text-muted">No image available</p>
                                                </div>
                                                @endif

                                                <!-- Status badges -->
                                                <div
                                                    class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-2">
                                                    <span
                                                        class="badge bg-{{ $productDetails->status == 'active' ? 'success' : 'danger' }}">
                                                        {{ ucfirst($productDetails->status) }}
                                                    </span>

                                                    @if ($stock > 0)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i>
                                                        In Stock
                                                    </span>
                                                    @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle-fill"></i>
                                                        Out of Stock
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Main Details Column -->
                                        <div class="col-md-8">
                                            <div class="p-4">
                                                <h3 class="fw-bold mb-0 text-primary">
                                                    {{ $productDetails->product_name }}
                                                </h3>
                                                <div class="mb-3">
                                                    <span class="badge bg-dark p-2 fs-6">Code:
                                                        {{ $productDetails->code ?? 'N/A' }}</span>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">Brand</p>
                                                        <h5 class="fw-bold text-primary">
                                                            {{ $productDetails->brand ?? 'N/A' }}
                                                        </h5>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <p class="text-muted mb-1">Category</p>
                                                        <h5 class="text-primary">
                                                            {{ $productDetails->category_name ?? 'N/A' }}
                                                        </h5>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <p class="text-muted mb-1">Description</p>
                                                    <p>{{ $productDetails->description ?: 'No description available' }}</p>
                                                </div>

                                                <!-- Pricing -->
                                                <div class="card bg-light p-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h5 class="text-danger fw-bold mb-0">
                                                                Rs.{{ number_format($productDetails->selling_price, 2) }}
                                                            </h5>
                                                            @if ($stock > 0)
                                                            <small class="text-success">
                                                                <i class="bi bi-check-circle-fill"></i>
                                                                {{ $stock }} units available
                                                            </small>
                                                            @else
                                                            <small class="text-danger fw-bold">
                                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                                OUT OF STOCK
                                                            </small>
                                                            @endif
                                                        </div>
                                                        @if ($productDetails->discount_price > 0)
                                                        <div class="position-relative">
                                                            <div
                                                                class="position-absolute top-0 start-50 translate-middle">
                                                                <span
                                                                    class="badge bg-danger p-2 rounded-pill">SPECIAL OFFER</span>
                                                            </div>
                                                            <div class="border border-success rounded-3 p-2 text-center mt-3"
                                                                style="background-color: rgba(25, 135, 84, 0.1);">
                                                                <span class="text-success fw-bold fs-5">
                                                                    SAVE Rs.{{ number_format($productDetails->discount_price, 2) }}
                                                                </span>
                                                            </div>
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
                        @endif

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- View Product Modal End -->
        </div>
    </div>

    @push('styles')
    <style>
        .search-results-container {
            z-index: 1050;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }
    </style>
    @endpush
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            function setupQuantityValidation() {
                document.querySelectorAll('.quantity-input').forEach(input => {
                    input.addEventListener('input', function() {
                        const max = parseInt(this.getAttribute('max'));
                        const value = parseInt(this.value) || 0;

                        if (value > max) {
                            this.classList.add('is-invalid');
                            this.nextElementSibling?.classList.add('d-block');
                        } else {
                            this.classList.remove('is-invalid');
                            this.nextElementSibling?.classList.remove('d-block');
                        }
                    });

                    input.addEventListener('blur', function() {
                        const max = parseInt(this.getAttribute('max'));
                        const min = parseInt(this.getAttribute('min') || 1);
                        let value = parseInt(this.value) || 0;

                        if (value > max) {
                            this.value = max;
                            Livewire.dispatch('quantity-corrected', {
                                watchId: this.dataset.watchId,
                                quantity: max
                            });
                        } else if (value < min) {
                            this.value = min;
                            Livewire.dispatch('quantity-corrected', {
                                watchId: this.dataset.watchId,
                                quantity: min
                            });
                        }
                    });
                });
            }

            setupQuantityValidation();
            document.addEventListener('livewire:update', setupQuantityValidation);
        });
    </script>
    @endpush
</div>