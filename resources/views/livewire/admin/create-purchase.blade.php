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
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="text-white mb-1 fw-bold">
                            <i class="bi bi-cart-plus me-2"></i>Create Purchase Order
                        </h2>
                        <p class="text-white-50 mb-0">Add products and create purchase orders</p>
                    </div>
                    <div class="mt-3 text-end">
                        <button wire:click="showPurchaseHistory" class="btn btn-light">
                            <i class="bi bi-clock-history me-1"></i> Purchase History
                        </button>
                    </div>
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


    <!-- Purchase History Modal -->
    <div wire:ignore.self class="modal fade" id="purchaseHistoryModal" tabindex="-1" aria-labelledby="purchaseHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg border-0">
                <!-- Header with gradient -->
                <div class="modal-header border-0 p-4" style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51 100%);">
                    <div>
                        <h5 class="modal-title text-white fw-bold mb-1" id="purchaseHistoryModalLabel">
                            <i class="bi bi-clock-history me-2"></i>Purchase History
                        </h5>
                        <small class="text-white-50">View and manage your purchase orders</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    <!-- Search Section -->
                    <div class="row mb-4 g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold mb-2">Search Purchases</label>
                            <div class="input-group search-enhanced">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-search" style="color:#9d1c20;"></i>
                                </span>
                                <input type="text"
                                    class="form-control border-start-0"
                                    placeholder="Search by ID, notes or amount..."
                                    wire:model.debounce.300ms="purchaseSearch">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button wire:click="searchPurchases" class="btn w-100" style="background-color:#9d1c20; color:white; border:none;">
                                <i class="bi bi-search me-2"></i>Search
                            </button>
                        </div>
                    </div>

                    <!-- Purchases Table Section -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header p-3" style="background-color:#f0f4f8; border-bottom:2px solid #9d1c20;">
                            <h6 class="mb-0 fw-bold" style="color:#9d1c20;">
                                <i class="bi bi-list-check me-2"></i>Purchase Orders
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height:300px; overflow-y:auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background-color:#f8f9fa; position:sticky; top:0;">
                                        <tr>
                                            <th class="ps-3" style="color:#9d1c20;">#</th>
                                            <th style="color:#9d1c20;">Customer</th>
                                            <th style="color:#9d1c20;">Notes</th>
                                            <th class="text-end pe-3" style="color:#9d1c20;">Grand Total</th>
                                            <th style="color:#9d1c20;">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                        $purchaseResults = $this->getPurchaseResults();
                                        @endphp
                                        @forelse($purchaseResults as $purchase)
                                        <tr wire:click="selectPurchase({{ $purchase->id }})"
                                            style="cursor:pointer; transition:all 0.2s ease;"
                                            class="{{ isset($selectedPurchase) && $selectedPurchase->id == $purchase->id ? '' : 'hover-highlight' }}"
                                            @class(['border-start ps-3', 'fw-semibold'=> isset($selectedPurchase) && $selectedPurchase->id == $purchase->id])
                                            @style(['border-left:4px solid #9d1c20 !important; background-color:#f0f4f8 !important;' => isset($selectedPurchase) && $selectedPurchase->id == $purchase->id])>
                                            <td class="ps-3">{{ $purchase->id }}</td>
                                            <td>{{ optional($purchase->customer)->name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="text-muted small">{{ Str::limit($purchase->notes ?? 'No notes', 50) }}</span>
                                            </td>
                                            <td class="text-end pe-3">
                                                <span class="fw-bold" style="color:#9d1c20;">Rs.{{ number_format($purchase->grand_total ?? 0, 2) }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ optional($purchase->created_at)->format('d M Y') }}</small>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5 text-muted">
                                                <i class="bi bi-inbox" style="font-size:2rem; margin-bottom:10px; display:block; opacity:0.5;"></i>
                                                No purchases found.
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <!-- Pagination -->
                            <div class="p-3 border-top">
                                {{ $purchaseResults->links('pagination::bootstrap-4', ['pageName' => 'purchasePage']) }}
                            </div>
                        </div>
                    </div>

                    <!-- Selected Purchase Items Section -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header p-3" style="background-color:#f0f4f8; border-bottom:2px solid #9d1c20;">
                            <h6 class="mb-0 fw-bold" style="color:#9d1c20;">
                                <i class="bi bi-box-seam me-2"></i>Purchase Items
                            </h6>
                        </div>
                        <div class="card-body p-4">
                            @if($selectedPurchase)
                            <!-- Purchase Summary Card -->
                            <div class="alert mb-4" style="background: linear-gradient(135deg, #f0f4f8 0%, #e8ecf1 100%); border-left:4px solid #9d1c20; border:none;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <small class="text-muted d-block">Purchase Order #</small>
                                        <h5 class="mb-0 fw-bold" style="color:#9d1c20;">{{ $selectedPurchase->id }}</h5>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <small class="text-muted d-block">Grand Total</small>
                                        <h5 class="mb-0 fw-bold" style="color:#9d1c20;">Rs.{{ number_format($selectedPurchase->grand_total ?? 0, 2) }}</h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Items Table -->
                            <div class="table-responsive">
                                <table class="table table-sm align-middle mb-0">
                                    <thead style="background-color:#f8f9fa;">
                                        <tr>
                                            <th style="color:#9d1c20;">Product</th>
                                            <th class="text-center" style="color:#9d1c20;">Qty</th>
                                            <th class="text-end" style="color:#9d1c20;">Unit Price</th>
                                            <th class="text-end" style="color:#9d1c20;">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($selectedPurchaseItems as $it)
                                        <tr class="border-bottom">
                                            <td class="fw-semibold">{{ $it['product_name'] }}</td>
                                            <td class="text-center">
                                                <span class="badge" style="background-color:#e8ecf1; color:#9d1c20;">{{ $it['quantity'] }}</span>
                                            </td>
                                            <td class="text-end">Rs.{{ number_format($it['unit_price'], 2) }}</td>
                                            <td class="text-end fw-bold" style="color:#9d1c20;">Rs.{{ number_format($it['subtotal'], 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-hand-index" style="font-size:2rem; margin-bottom:10px; display:block; opacity:0.5;"></i>
                                <p class="mb-0">Click a purchase row to view its items.</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer p-3 border-top">
                    <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>Close
                    </button>
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