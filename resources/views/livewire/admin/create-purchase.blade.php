@push('styles')
@include('components.admin-styles')
<style>
    .search-results-container {
        z-index: 1050;
        border: 1px solid #e5e7eb;
        border-top: none;
        background: #fff;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        max-height: 400px;
        overflow-y: auto;
    }

    .search-result-item {
        padding: 12px;
        border-left: 3px solid transparent;
        transition: all .15s ease;
        cursor: pointer;
    }

    .search-result-item:hover {
        background: #fff5f5;
        border-left-color: #9d1c20;
    }

    .search-result-meta {
        font-size: 0.85rem;
        color: #6b7280;
    }

    .search-enhanced {
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        transition: all 0.3s ease;
    }

    .search-enhanced:focus-within {
        border-color: #9d1c20;
        box-shadow: 0 0 0 0.2rem rgba(157, 28, 32, 0.1);
    }
</style>
@endpush

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #9d1c20 0%, #d34d51 100%);">
                <div class="card-body p-4">
                    <h2 class="text-white mb-1 fw-bold">
                        <i class="bi bi-cart-plus me-2"></i>Create Purchase Order
                    </h2>
                    <p class="text-white-50 mb-0">Add products and create purchase orders</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <!-- Search Section -->
                    <div class="row mb-4">
                        <!-- Customer Search -->
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">Customer</label>
                            <div class="input-group search-enhanced">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-danger"></i>
                                </span>
                                <input id="searchCustomerInput"
                                    type="text"
                                    class="form-control border-start-0"
                                    placeholder="Search customer by name, phone or email..."
                                    wire:model.live.debounce.300ms="searchCustomer"
                                    autocomplete="off">
                            </div>

                            @if($customerResults && count($customerResults) > 0)
                            <div class="search-results-container position-absolute w-100 mt-1 rounded-3">
                                @foreach($customerResults as $cust)
                                <div class="search-result-item" wire:click="selectCustomer({{ $cust->id }})">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold">{{ $cust->name }}</div>
                                            <div class="search-result-meta">
                                                {{ $cust->phone }}
                                                @if($cust->email)
                                                • {{ $cust->email }}
                                                @endif
                                            </div>
                                        </div>
                                        <div>
                                            <span class="badge bg-primary">Select</span>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <!-- Product Search -->
                        <div class="col-md-6 position-relative">
                            <label class="form-label fw-semibold">Search Product</label>
                            <div class="input-group search-enhanced">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search text-danger"></i>
                                </span>
                                <input id="searchProductInput"
                                    type="text"
                                    class="form-control border-start-0"
                                    placeholder="Type product name or code..."
                                    wire:model.live.debounce.300ms="searchProduct"
                                    autocomplete="off">
                            </div>

                            @if($productResults && count($productResults) > 0)
                            <div class="search-results-container position-absolute w-100 mt-1 rounded-3">
                                @foreach($productResults as $prod)
                                <div class="search-result-item">
                                    <div class="d-flex align-items-center" style="gap:12px;">
                                        <div style="width:48px;height:48px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                            @if($prod->image)
                                            <img src="{{ asset('storage/' . $prod->image) }}"
                                                alt="{{ $prod->product_name }}"
                                                style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                                            @else
                                            <i class="bi bi-box-seam text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1" wire:click="addProductToCart({{ $prod->id }})">
                                            <div class="fw-bold">{{ $prod->product_name }}</div>
                                            <div class="search-result-meta">
                                                Code: {{ $prod->product_code }} • Stock: {{ $prod->stock_quantity ?? 0 }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold">Rs. {{ number_format((float) $prod->selling_price, 2) }}</div>
                                            <div class="search-result-meta">Click to add</div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @elseif(trim($searchProduct) !== '' && count($productResults) === 0)
                            <div class="search-results-container position-absolute w-100 mt-1 rounded-3 p-2">
                                <div class="search-result-item" wire:click="createProductFromSearch">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-bold">Create new product</div>
                                            <div class="search-result-meta">Add "{{ $searchProduct }}" as a new product</div>
                                        </div>
                                        <div>
                                            <span class="badge bg-success">Create</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Cart Table -->
                    <div class="table-responsive mt-4">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cart as $id => $item)
                                <tr wire:key="cart-{{ $id }}">
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($item['image'])
                                            <img src="{{ asset('storage/' . $item['image']) }}"
                                                alt="{{ $item['product_name'] }}"
                                                class="me-3"
                                                style="width:50px;height:50px;object-fit:cover;border-radius:8px;">
                                            @else
                                            <div class="me-3" style="width:50px;height:50px;background:#f3f4f6;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                                <i class="bi bi-box-seam text-muted"></i>
                                            </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold">{{ $item['product_name'] }}</div>
                                                <small class="text-muted">{{ $item['product_code'] }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="input-group input-group-sm" style="width: 150px; margin: 0 auto;">
                                            <span class="input-group-text">Rs.</span>
                                            <input type="number"
                                                class="form-control form-control-sm text-end"
                                                value="{{ $prices[$id] ?? $item['unit_price'] }}"
                                                wire:change="updatePrice({{ $id }}, $event.target.value)"
                                                min="0"
                                                step="0.01">
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            class="form-control form-control-sm text-center"
                                            style="width: 80px; margin: 0 auto;"
                                            value="{{ $quantities[$id] ?? 1 }}"
                                            wire:change="updateQuantity({{ $id }}, $event.target.value)"
                                            min="1">
                                    </td>
                                    <td class="text-end">
                                        Rs. {{ number_format((float) (($prices[$id] ?? $item['unit_price']) * ($quantities[$id] ?? 1)), 2) }}
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-danger" wire:click="removeItem({{ $id }})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                                        <p class="text-muted mt-3">Cart is empty. Search and add products.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary Section -->
                    <div class="row mt-4 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Discount Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">Rs.</span>
                                <input type="number"
                                    class="form-control"
                                    wire:model.live="adjustAmount"
                                    placeholder="Enter discount amount"
                                    min="0"
                                    step="0.01">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">Subtotal:</span>
                                        <span>Rs. {{ number_format((float) $this->getTotalAmount(), 2) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-semibold">Discount:</span>
                                        <span class="text-danger">- Rs. {{ number_format((float) $adjustAmount, 2) }}</span>
                                    </div>
                                    <hr>
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold fs-5">Grand Total:</span>
                                        <span class="fw-bold fs-5 text-primary">Rs. {{ number_format((float) $this->getGrandTotal(), 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Notes</label>
                            <textarea class="form-control"
                                rows="3"
                                wire:model="notes"
                                placeholder="Add any notes about this purchase..."></textarea>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button class="btn btn-secondary" wire:click="$refresh">
                            <i class="bi bi-arrow-clockwise me-1"></i> Reset
                        </button>
                        <button class="btn btn-primary" wire:click="completePurchase">
                            <i class="bi bi-check-circle me-1"></i> Complete Purchase
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        // Handle toast messages
        window.addEventListener('toast', event => {
            const {
                type,
                message
            } = event.detail[0];
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        });

        // Handle search selection
        window.addEventListener('searchSelected', event => {
            const d = event.detail[0];
            if (!d.type) return;

            if (d.type === 'customer') {
                const el = document.getElementById('searchCustomerInput');
                if (el) {
                    el.value = d.name || el.value;
                    el.blur();
                }
            }

            if (d.type === 'product') {
                const el = document.getElementById('searchProductInput');
                if (el) {
                    el.value = '';
                    el.blur();
                }
            }
        });

        // Handle modal events
        Livewire.on('openModal', (modalId) => {
            const modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        });

        Livewire.on('closeModal', (modalId) => {
            const modalElement = document.getElementById(modalId);
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            }
        });
    });
</script>
@endpush