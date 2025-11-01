<div class="container-fluid py-4">
    <div class="card border-0 shadow">
        <!-- Card Header -->
        <div class="card-header text-white p-2 rounded-t-4 d-flex align-items-center"
            style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%); border-radius: 20px 20px 0 0;">
            <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center me-3">
                <i class="bi bi-shield-lock text-white fs-4" aria-hidden="true"></i>
            </div>
            <div>
                <h3 class="mb-1 fw-bold tracking-tight text-white">Product Return</h3>
                <p class="text-white opacity-80 mb-0 text-sm">Monitor and manage your product Returns</p>
            </div>
        </div>
        <!-- Header Subtext -->
        <div class="card-header bg-transparent py-3 border-bottom" style="border-color: #233D7F;">
            <p class="mb-0 text-muted">Use the left panel to search by customer or invoice. Select one, then choose a product to process return and damage on the right.</p>
        </div>

        <!-- Card Body -->
        <div class="card-body p-3 bg-transparent">
            <div class="row">
                <!-- Left Column: Customer & Invoice Search -->
                <div class="col-lg-5 mb-4 mb-lg-0">
                    <div class="mb-4">
                        <h5 class="mb-3 fw-semibold">Search Customer</h5>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-gray-100 border-0 px-3"><i class="bi bi-person"></i></span>
                            <input type="text" class="form-control border-0" placeholder="Type name/phone/email..." wire:model.live.debounce.300ms="searchCustomer" autocomplete="off">
                        </div>
                        <div class="list-group scrollable-container">
                            @forelse($customerResults as $c)
                            <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" wire:click="selectCustomer({{ $c['id'] }})">
                                <div>
                                    <div class="fw-semibold">{{ $c['name'] }}</div>
                                    <div class="small text-muted">{{ $c['phone'] ?? '—' }} · {{ $c['email'] ?? '—' }}</div>
                                </div>
                                @if($selectedCustomerId === $c['id'])<i class="bi bi-check-circle text-success"></i>@endif
                            </button>
                            @empty
                            <div class="text-muted small p-3">No customers</div>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h5 class="mb-3 fw-semibold">Search Invoice</h5>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-gray-100 border-0 px-3"><i class="bi bi-receipt"></i></span>
                            <input type="text" class="form-control border-0" placeholder="Type invoice number..." wire:model.live.debounce.300ms="searchInvoice" autocomplete="off">
                        </div>
                        <div class="list-group scrollable-container">
                            @forelse($invoiceResults as $inv)
                            <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" wire:click="selectInvoice({{ $inv['id'] }})">
                                <div>
                                    <div class="fw-semibold">#{{ $inv['invoice_number'] }}</div>
                                    <div class="small text-muted">{{ \Carbon\Carbon::parse($inv['created_at'])->format('Y-m-d') }} · {{ $inv['customer']['name'] ?? 'N/A' }}</div>
                                </div>
                                @if($selectedInvoiceId === $inv['id'])<i class="bi bi-check-circle text-success"></i>@endif
                            </button>
                            @empty
                            <div class="text-muted small p-3">No invoices</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Right Column: Details, Product Search, Re-Entry Form -->
                <div class="col-lg-7">
                    <div class="sticky-top" style="top: 20px;">
                        <div class="card border-0 shadow">
                            <div class="card-header bg-light py-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Return Re-Entry</h5>
                                <div class="text-muted small">@if($selectedCustomer) Customer: <strong>{{ $selectedCustomer->name }}</strong>@endif @if($selectedInvoiceId) · Invoice: <strong>#{{ $selectedInvoice['invoice_number'] ?? '' }}</strong>@endif</div>
                            </div>
                            <div class="card-body">
                                @if($selectedInvoiceId && !empty($selectedInvoiceItems))
                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label fw-semibold mb-0">Invoice Items</label>
                                        <span class="text-muted small">#{{ $selectedInvoice['invoice_number'] ?? '' }}</span>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Product</th>
                                                    <th class="text-center">Sold</th>
                                                    <th class="text-center">Returned</th>
                                                    <th class="text-center">Available</th>
                                                    <th class="text-end">Amount</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($selectedInvoiceItems as $it)
                                                <tr>
                                                    <td>{{ $it['product_name'] }}</td>
                                                    <td class="text-center">{{ $it['quantity'] }}</td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning">{{ $it['returned_quantity'] }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success">{{ $it['available_quantity'] }}</span>
                                                    </td>
                                                    <td class="text-end">{{ number_format($it['amount'], 2) }}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                @if(!empty($returnedProducts))
                                <div class="mb-4">
                                    <div class="alert alert-info">
                                        <h6 class="alert-heading mb-2"><i class="bi bi-info-circle me-2"></i>Previously Returned Products</h6>
                                        <div class="table-responsive">
                                            <table class="table table-sm table-borderless mb-0">
                                                <thead>
                                                    <tr>
                                                        <th>Product</th>
                                                        <th class="text-center">Qty</th>
                                                        <th class="text-end">Amount</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($returnedProducts as $ret)
                                                    <tr>
                                                        <td class="small">{{ $ret['product_name'] }}</td>
                                                        <td class="text-center small">{{ $ret['return_quantity'] }}</td>
                                                        <td class="text-end small">{{ number_format($ret['total_amount'], 2) }}</td>
                                                        <td class="small">{{ $ret['created_at'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endif
                                <!-- Product Search and Result List -->
                                <div class="mb-4">
                                    <label class="form-label fw-semibold">Search Product</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-gray-100 border-0 px-3"><i class="bi bi-search"></i></span>
                                        <input type="text" class="form-control border-0" placeholder="Type product name or code..." wire:model.live.debounce.300ms="productSearch" autocomplete="off">
                                    </div>
                                    <div class="list-group mt-2" style="max-height: 220px; overflow-y:auto;">
                                        @foreach($productResults as $p)
                                        <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" wire:click="selectProduct({{ $p['id'] }})">
                                            <div>
                                                <div class="fw-semibold">{{ $p['product_name'] }}</div>
                                                <div class="small text-muted">Code: {{ $p['product_code'] }} · {{ $p['category']['name'] ?? 'N/A' }}</div>
                                            </div>
                                            @if($selectedProductId === $p['id'])<i class="bi bi-check-circle text-success"></i>@endif
                                        </button>
                                        @endforeach
                                        @if(empty($productResults) && strlen($productSearch) > 0)
                                        <div class="text-muted small p-3">No matching products</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Selected Product Panel + Re-Entry Form -->
                                @if($selectedProduct)
                                <div class="alert alert-secondary d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <div class="fw-semibold">{{ $selectedProduct->product_name }}</div>
                                        <div class="small text-muted">Code: {{ $selectedProduct->product_code }} · {{ $selectedProduct->category?->name ?? 'N/A' }}</div>
                                    </div>
                                    <div class="d-flex gap-3 small">
                                        <span class="badge bg-success">Available: {{ $selectedProduct->stock_quantity }}</span>
                                        <span class="badge bg-danger">Damage: {{ $selectedProduct->damage_quantity }}</span>
                                    </div>
                                </div>

                                <form wire:submit.prevent="updateStock">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Re-entry Quantity (add to stock)</label>
                                            <input type="number" class="form-control" wire:model="addStock" min="0" placeholder="0">
                                            @error('addStock') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label">Damage Quantity (add to damage)</label>
                                            <input type="number" class="form-control" wire:model="addDamage" min="0" placeholder="0">
                                            @error('addDamage') <span class="text-danger small">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Return Notes (Optional)</label>
                                        <textarea class="form-control" wire:model="returnNotes" rows="2" placeholder="Add any notes about this return..."></textarea>
                                        @error('returnNotes') <span class="text-danger small">{{ $message }}</span> @enderror
                                    </div>

                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary btn-md mx-auto" style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">
                                            <i class="bi bi-check-circle me-2"></i>Process Return
                                        </button>
                                    </div>
                                    @error('submit') <div class="text-danger small mt-2">{{ $message }}</div> @enderror
                                </form>
                                @else
                                <div class="text-center py-4 text-muted">Search and select a product to continue.</div>
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