<div>
    @push('styles')
    <style>
        .search-results-container {
            z-index: 1050;
            border: 1px solid #dee2e6;
            border-top: none;
        }

        .search-result-item:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .search-results-container {
                font-size: 0.875rem;
            }
            
            .search-result-item .product-info h6 {
                font-size: 0.875rem;
            }
            
            .search-result-item .badge {
                font-size: 0.75rem;
            }
        }

        input[type="number"].is-invalid {
            border-color: #9d1c20;
        }

        .tracking-tight {
            letter-spacing: -0.025em;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .hover\:shadow:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .hover\:bg-gray-50:hover {
            background-color: #F8F9FA;
        }

        .table-bordered {
            border-collapse: collapse;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #9d1c20;
        }

        @media (max-width: 767.98px) {
            .table {
                font-size: 0.875rem;
            }

            .table td:nth-child(3),
            .table th:nth-child(3),
            /* Code */
            .table td:nth-child(6),
            .table th:nth-child(6) {
                /* Discount */
                display: none;
            }

            .modal-body {
                padding: 1rem;
            }

            .modal-footer {
                justify-content: center;
            }
        }
    </style>
    @endpush

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header pb-0">
                        <h6 style="color: #9d1c20;">Billing System</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6 mx-auto position-relative">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search text-denger"></i>
                                    </span>
                                    <input type="text" class="form-control"
                                        placeholder="Search by code, model, barcode, brand or name..."
                                        wire:model.live="search" autocomplete="off">
                                </div>

                                @if ($search && count($searchResults) > 0)
                                <div class="search-results-container position-absolute mt-1 w-100 bg-white shadow-lg rounded"
                                    style="max-height: 350px; overflow-y: auto; z-index: 1000; top: 100%; left: 0;">
                                    @foreach ($searchResults as $result)
                                    @if($result->status== 'Active')
                                    <div class="search-result-item p-2 border-bottom d-flex align-items-center"
                                        wire:key="result-{{ $result->id }}">

                                        <div class="product-image me-3" style="min-width: 60px;">
                                            @if ($result->image)
                                            <img src="{{ asset('public/storage/' . $result->image) }}"
                                                alt="{{ $result->name }}" class="img-fluid rounded"
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                            <div
                                                style="width:30px;height:30px;background-color:#f3f4f6;border-radius:0.5rem;display:flex;align-items:center;justify-content:center; margin:0 auto;">
                                                <i class="bi bi-box-seam text-gray-600"></i>
                                            </div>
                                            @endif
                                        </div>

                                        <div class="product-info flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="mb-1 fw-bold">{{ $result->product_name ?? 'Unnamed Product'
                                                    }}</h6>
                                                <div class="text-end">
                                                    <span class="badge bg-success">
                                                        Rs.{{ $result->discount_price ?: $result->selling_price ?? '-'
                                                        }}
                                                    </span>
                                                    <span
                                                        class="badge {{ $result->stock_quantity <= 5 ? 'bg-warning text-dark' : 'bg-info' }}">
                                                        Available: {{ $result->stock_quantity ?? 0 }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="text-muted small mt-1">
                                                <span class="me-2">Code: {{ $result->product_code ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <div class="ps-2">
                                            <button class="btn btn-sm btn-primary"
                                                wire:click="addToCart({{ $result->id }})" {{ $result->stock_quantity <=
                                                    0 ? 'disabled' : '' }}>
                                                <i class="fas fa-plus"></i> Add
                                            </button>
                                        </div>

                                    </div>
                                    @endif
                                    @endforeach
                                </div>
                                @elseif($search && count($searchResults) == 0)
                                <div
                                    class="search-results-container position-absolute mt-1 w-100 bg-white shadow-lg rounded p-3"
                                    style="top: 100%; left: 0; z-index: 1000;">
                                    <div class="text-center text-muted">
                                        <i class="fas fa-search fa-2x mb-2"></i>
                                        <p>No products found matching "{{ $search }}"</p>
                                    </div>
                                </div>
                                @endif

                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Product</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Unit Price</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Quantity</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Quantity Type</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Discount</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Total</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cart as $id => $item)
                                    <tr wire:key="cart-{{ $id }}">
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div>
                                                    @if ($item['image'])
                                                    <img src="{{ asset('public/storage/' . $item['image']) }}"
                                                        class="avatar avatar-sm me-3 rounded" alt="{{ $item['name'] }}"
                                                        style="width: 50px; height: 50px; object-fit: cover;">
                                                    @else
                                                    <div
                                                        style="width:40px;height:40px;background-color:#f3f4f6;border-radius:0.5rem;display:flex;align-items:center;justify-content:center; margin-right: auto;">
                                                        <i class="bi bi-box-seam text-gray-600"></i>
                                                    </div>
                                                    @endif

                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $item['name'] }}</h6>
                                                    <small class="text-xs text-secondary mb-0">{{ $item['code'] ?? 'N/A'
                                                        }} |
                                                        {{ $item['brand'] ?? 'N/A' }}
                                                    </small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" class="form-control form-control-sm"
                                                    value="{{ $prices[$id] }}" min="0" step="0.01"
                                                    wire:model.blur="prices.{{ $id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div style="width: 100px;">
                                                {{-- FIXED: Removed wire:change and other attributes to simplify and
                                                prevent conflicts --}}
                                                <input type="number"
                                                    class="form-control form-control-sm text-center quantity-input"
                                                    value="{{ $quantities[$id] }}" min="1"
                                                    max="{{ $item['stock_quantity'] }}"
                                                    wire:model.blur="quantities.{{ $id }}">
                                                <small class="text-muted">Max: {{ $item['stock_quantity'] }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm"
                                                wire:model="quantityTypes.{{ $id }}">
                                                <option value="">Select</option>
                                                @foreach($availableQuantityTypes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                        </td>

                                        <td>
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" class="form-control form-control-sm"
                                                    value="{{ $discounts[$id] ?? 0 }}" min="0" max="{{ $prices[$id] }}"
                                                    step="0.01" wire:model.blur="discounts.{{ $id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                Rs.{{ number_format(($prices[$id]) *
                                                $quantities[$id] - ($discounts[$id] ?? 0) * $quantities[$id], 2) }}
                                            </p>
                                        </td>
                                        <td>
                                            <!-- <button class="btn btn-link btn-sm text-info rounded-circle "
                                                wire:click="showDetail({{ $id }})">
                                                <i class="bi bi-eye"></i>
                                            </button> -->
                                            <button class="btn btn-link btn-sm text-danger rounded-circle "
                                                wire:click="removeFromCart({{ $id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                                <p>Your cart is empty. Search and add products to create a bill.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header pb-0 " style="background-color: #9d1c20;">
                                        <h6 class="text-white">Customer & Payment Information</h6>
                                    </div>
                                    <div class="card-body" style="height: 500px; overflow-y: auto;">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Select Customer</label>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="input-group flex-grow-1">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-person"></i>
                                                    </span>
                                                    <select class="form-select" wire:model="customerId">
                                                        <option value="">-- Select a customer --</option>
                                                        @foreach ($customers as $customer)
                                                        <option value="{{ $customer->id }}">
                                                            {{ $customer->name }} ({{$customer->phone}})
                                                        </option>
                                                        @endforeach
                                                    </select>

                                                </div>
                                                <button class="btn  d-flex align-items-center"
                                                    style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';"
                                                    data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                                    <i class="bi bi-plus-circle me-1"></i>ADD
                                                </button>
                                            </div>
                                            @error('customerId')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Payment Type</label>
                                            <div class="d-flex">
                                                <div class="form-check me-4">
                                                    <input class="form-check-input" type="radio" name="paymentType"
                                                        id="fullPayment" value="full" wire:model.live="paymentType"
                                                        checked>
                                                    <label class="form-check-label" for="fullPayment">
                                                        <span class="badge bg-success me-1">
                                                            <i class="fas fa-money-bill me-1"></i>
                                                        </span>
                                                        Payment
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="paymentType"
                                                        id="partialPayment" value="partial"
                                                        wire:model.live="paymentType">
                                                    <label class="form-check-label" for="partialPayment">
                                                        <span class="badge bg-warning me-1">
                                                            <i class="fas fa-percentage me-1"></i>
                                                        </span>
                                                        Credit
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        @if ($paymentType == 'full')
                                        <div class="card mb-3 border">
                                            <div class="card-body p-3">
                                                <h6 class="card-title fw-bold mb-3">
                                                    <i class="fas fa-money-bill-wave me-2"></i>Cash Payment
                                                </h6>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold">Cash Amount</label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">Rs.</span>
                                                        <input type="number" class="form-control"
                                                            placeholder="Enter cash amount" wire:model="cashAmount">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="card mb-3 border">
                                            <div class="card-body p-3">
                                                <h6 class="card-title fw-bold mb-3">
                                                    <i class="fas fa-money-check-alt me-2"></i>Cheque Payments
                                                </h6>
                                                <form wire:submit.prevent="addCheque">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Cheque
                                                                Number</label>
                                                            <input type="text" class="form-control"
                                                                placeholder="Enter cheque number"
                                                                wire:model="newCheque.number">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Bank Name</label>
                                                            <select class="form-select" wire:model="newCheque.bank">
                                                                <option value="">-- Select a bank --</option>
                                                                @foreach($banks as $bank)
                                                                <option value="{{ $bank }}">{{ $bank }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('newCheque.bank') <span class="text-danger small">{{
                                                                $message }}</span> @enderror
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Cheque Date</label>
                                                            <input type="date" class="form-control"
                                                                wire:model="newCheque.date">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">Cheque
                                                                Amount</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text">Rs.</span>
                                                                <input type="number" class="form-control"
                                                                    placeholder="Enter cheque amount"
                                                                    wire:model="newCheque.amount">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-sm "
                                                            style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">
                                                            <i class="fas fa-plus me-1"></i> Add Cheque
                                                        </button>
                                                    </div>
                                                </form>

                                                @if(!empty($cheques))
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-sm table-bordered">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Number</th>
                                                                <th>Bank</th>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($cheques as $index => $cheque)
                                                            <tr wire:key="cheque-{{ $index }}">
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ $cheque['number'] }}</td>
                                                                <td>{{ $cheque['bank'] }}</td>
                                                                <td>{{ $cheque['date'] }}</td>
                                                                <td>Rs.{{ number_format($cheque['amount'], 2) }}</td>
                                                                <td>
                                                                    <button class="btn btn-link btn-sm text-danger p-0"
                                                                        wire:click.prevent="removeCheque({{ $index }})">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        @else
                                        <div class="card mb-3 border border-warning bg-light">
                                            <div class="card-body p-3">
                                                <h6 class="card-title fw-bold mb-3">
                                                    <i class="fas fa-calendar-alt me-2"></i>Credit Sale Details
                                                </h6>
                                                <p class="text-info">The total amount of <strong>Rs.{{
                                                        number_format($grandTotal, 2) }}</strong> will be recorded as a
                                                    due payment.</p>

                                            </div>
                                        </div>
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Notes</label>
                                            <textarea class="form-control" rows="3"
                                                placeholder="Add any notes about this sale"
                                                wire:model="saleNotes"></textarea>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="card">
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
                                            <button class="btn btn-danger me-2" wire:click="clearCart"
                                                style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">
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

                    </div>
                </div>
            </div>

            <!-- View model -->
            <div wire:ignore.self class="modal fade" id="viewDetailModal" tabindex="-1"
                aria-labelledby="viewDetailModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header bg-primary">
                            <h1 class="modal-title fs-5 text-white" id="viewDetailModalLabel">Watch Details</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        @if ($productDetails)
                        <div class="modal-body p-4">
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-0">
                                    <div class="row g-0">
                                        <div class="col-md-4 border-end">
                                            <div class="position-relative h-100">
                                                @if ($productDetails->image)
                                                <img src="{{ asset('public/storage/' . $productDetails->image) }}"
                                                    alt="{{ $productDetails->name }}"
                                                    class="img-fluid rounded-start h-100 w-100 object-fit-cover">
                                                @else
                                                <div
                                                    class="bg-light d-flex flex-column align-items-center justify-content-center h-100">
                                                    <i class="bi bi-box-seam text-muted" style="font-size: 5rem;"></i>
                                                    <p class="text-muted mt-2">No image available</p>
                                                </div>
                                                @endif
                                                <div class="position-absolute top-0 end-0 p-2 d-flex flex-column gap-2">
                                                    @if ($productDetails->available_stock > 0)
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle-fill"></i> In Stock
                                                    </span>
                                                    @else
                                                    <span class="badge bg-danger">
                                                        <i class="bi bi-x-circle-fill"></i> Out of Stock
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-8">
                                            <div class="p-4">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <h3 class="fw-bold mb-0 text-primary">{{
                                                        $productDetails->product_name }}</h3>
                                                </div>

                                                <div class="mb-3">
                                                    <span class="badge bg-dark p-2 fs-6">Code: {{
                                                        $productDetails->product_code ?? 'N/A' }}</span>
                                                </div>

                                                <div class="mb-4">
                                                    <p class="text-muted mb-1">Description</p>
                                                    <p>{{ $productDetails->description ?? 'N/A' }}</p>
                                                </div>

                                                <div class="card bg-light p-3 mb-3">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <h4 class="text-primary fw-bold">
                                                                Rs.{{ number_format($productDetails->selling_price, 2)
                                                                }}
                                                            </h4>
                                                            @if ($productDetails->available_stock > 0)
                                                            <small class="text-success">
                                                                <i class="bi bi-check-circle-fill"></i> {{
                                                                $productDetails->available_stock }} units available
                                                            </small>
                                                            @else
                                                            <small class="text-danger fw-bold">
                                                                <i class="bi bi-exclamation-triangle-fill"></i> OUT OF
                                                                STOCK
                                                            </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion mt-4" id="productDetailsAccordion">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                                data-bs-target="#inventory-collapse" aria-expanded="true"
                                                aria-controls="inventory-collapse">
                                                <i class="bi bi-box-seam me-2"></i> Inventory
                                            </button>
                                        </h2>
                                        <div id="inventory-collapse" class="accordion-collapse collapse show"
                                            data-bs-parent="#productDetailsAccordion">
                                            <div class="accordion-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="card mb-3 border-danger">
                                                            <div class="card-body d-flex justify-content-between">
                                                                <p class="card-text fw-bold">Damage Stock</p>
                                                                <h4 class="card-title text-danger">{{
                                                                    $productDetails->damage_quantity }}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div
                                                            class="card mb-3 {{ $productDetails->available_stock > 0 ? 'border-success' : 'border-danger' }}">
                                                            <div class="card-body d-flex justify-content-between">
                                                                <p class="card-text fw-bold">Available Stock</p>
                                                                <h4
                                                                    class="card-title {{ $productDetails->available_stock > 0 ? 'text-success' : 'text-danger' }}">
                                                                    {{ $productDetails->available_stock }}
                                                                </h4>
                                                            </div>
                                                        </div>
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

            <!-- Add customer -->
            <div wire:ignore.self class="modal fade" id="addCustomerModal" tabindex="-1"
                aria-labelledby="addCustomerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title" id="addCustomerModalLabel">
                                <i class="bi bi-user-plus me-2"></i>Add New Customer
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form wire:submit.prevent="saveCustomer">
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label">Customer Type</label>
                                        <div class="d-flex">
                                            <div class="form-check me-4">
                                                <input class="form-check-input" type="radio" name="newCustomerType"
                                                    id="newWholesale" value="wholesale" wire:model="newCustomerType"
                                                    checked>
                                                <label class="form-check-label" for="newWholesale">Wholesale</label>
                                            </div>
                                            <div class="form-check ">
                                                <input class="form-check-input" type="radio" name="newCustomerType"
                                                    id="newRetail" value="retail" wire:model="newCustomerType">
                                                <label class="form-check-label" for="newRetail">Retail</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                                            <input type="text" class="form-control" placeholder="Enter customer name"
                                                wire:model="newCustomerName" required>
                                        </div>
                                        @error('newCustomerName')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                            <input type="text" class="form-control" placeholder="Enter phone number"
                                                wire:model="newCustomerPhone">
                                        </div>
                                        @error('newCustomerPhone')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
                                            <input type="email" class="form-control" placeholder="Enter email address"
                                                wire:model="newCustomerEmail">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                            <input type="text" class="form-control" placeholder="Enter address"
                                                wire:model="newCustomerAddress">
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label">Additional Information</label>
                                        <textarea class="form-control" rows="3"
                                            placeholder="Add any additional information about this customer"
                                            wire:model="newCustomerNotes"></textarea>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>Cancel
                            </button>
                            <button type="button" class="btn btn-primary" wire:click="saveCustomer">
                                <i class="fas fa-save me-1"></i>Save Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- reciepte model -->
            <div wire:ignore.self class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content rounded-4 shadow-xl"
                        style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #FFFFFF, #F8F9FA);">
                        <div class="modal-header"
                            style="background-color: #9d1c20; color: #FFFFFF; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                            <h5 class="modal-title fw-bold tracking-tight" id="receiptModalLabel">
                                <i class="bi bi-receipt me-2"></i>Sales Receipt
                            </h5>
                            <div class="ms-auto d-flex gap-2">
                                <button type="button" class="btn btn-sm rounded-full px-3 transition-all hover:shadow"
                                    id="printButton" style="background-color: #9d1c20;border-color:#fff; color: #fff;">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
                                <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100"
                                    data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                        </div>
                        <div class="modal-body p-4" id="receiptContent">
                            @if ($receipt)
                            <div class="receipt-container">
                                <div class="text-center mb-4">
                                    <h3 class="mb-1 fw-bold tracking-tight" style="color: #9d1c20;">PLUS</h3>
                                    <p class="mb-0 text-muted small" style="color: #6B7280;">NO 20/2/1, 2nd FLOOR,HUNTER
                                        BUILDING,BANKSHALLL STREET,COLOMBO-11</p>
                                    <p class="mb-0 text-muted small" style="color: #6B7280;">Phone: 011 - 2332786 |
                                        Email: plusaccessories.lk@gmail.com</p>
                                    <h4 class="mt-3 border-bottom border-2 pb-2 fw-bold"
                                        style="color: #9d1c20; border-color: #9d1c20;">SALES RECEIPT</h4>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2 fw-medium" style="color: #6B7280;">INVOICE DETAILS
                                        </h6>
                                        <p class="mb-1" style="color: #9d1c20;"><strong>Invoice Number:</strong> {{
                                            $receipt->invoice_number }}</p>
                                        <p class="mb-1" style="color: #9d1c20;"><strong>Date:</strong> {{
                                            $receipt->created_at->setTimezone('Asia/Colombo')->format('d/m/Y h:i A') }}
                                        </p>
                                        <p class="mb-1"><strong>Payment Status:</strong>
                                            @if(ucfirst($receipt->payment_status) == 'Paid')
                                            <span class="badge"
                                                style="background-color: {{ $receipt->payment_status == 'paid' ? '#0F5132' : ($receipt->payment_status == 'partial' ? '#664D03' : '#842029') }}; color: #FFFFFF;">
                                                Paid
                                            </span>
                                            @else
                                            <span class="badge"
                                                style="background-color: {{ $receipt->payment_status == 'paid' ? '#0F5132' : ($receipt->payment_status == 'partial' ? '#664D03' : '#842029') }}; color: #FFFFFF;">
                                                Credit
                                            </span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2 fw-medium" style="color: #6B7280;">CUSTOMER DETAILS
                                        </h6>
                                        @if ($receipt->customer)
                                        <p class="mb-1" style="color: #9d1c20;"><strong>Name:</strong> {{
                                            $receipt->customer->name }}</p>
                                        <p class="mb-1" style="color: #9d1c20;"><strong>Phone:</strong> {{
                                            $receipt->customer->phone ?? 'N/A' }}</p>
                                        <p class="mb-1" style="color: #9d1c20;"><strong>Type:</strong> {{
                                            ucfirst($receipt->customer_type) ?? 'N/A' }}</p>
                                        @else
                                        <p class="text-muted" style="color: #6B7280;">Walk-in Customer</p>
                                        @endif
                                    </div>
                                </div>

                                <h6 class="text-muted mb-2 fw-medium" style="color: #6B7280;">PURCHASED ITEMS</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-sm border-1"
                                        style="border-color: #9d1c20;">
                                        <thead style="background-color: #9d1c20; color: #FFFFFF;">
                                            <tr>
                                                <th scope="col" class="text-center py-2">No</th>
                                                <th scope="col" class="text-center py-2">Item</th>
                                                <th scope="col" class="text-center py-2">Price</th>
                                                <th scope="col" class="text-center py-2">Qty</th>
                                                <th scope="col" class="text-center py-2">Discount</th>
                                                <th scope="col" class="text-center py-2">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody style="color: #9d1c20;">
                                            @foreach ($receipt->items as $index => $item)
                                            <tr class="transition-all hover:bg-gray-50">
                                                <td class="text-center py-2">{{ $index + 1 }}</td>
                                                <td class="text-center py-2">{{ $item->product->product_name ?? 'N/A' }}
                                                </td>
                                                
                                                <td class="text-center py-2">Rs.{{ number_format($item->price, 2) }}
                                                </td>
                                                <td class="text-center py-2">{{ $item->quantity }}</td>
                                                <td class="text-center py-2">Rs.{{ number_format($item->discount *
                                                    $item->quantity, 2) }}</td>
                                                <td class="text-center py-2">Rs.{{ number_format(($item->price *
                                                    $item->quantity) - ($item->discount * $item->quantity), 2) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-muted mb-2 fw-medium" style="color: #6B7280;">PAYMENT
                                            INFORMATION</h6>
                                        @if ($receipt->payments->count() > 0)
                                        @foreach ($receipt->payments as $payment)
                                        <div class="mb-2 p-2 border-start border-3 rounded-2"
                                            style="border-color: {{ $payment->is_completed ? '#0F5132' : '#664D03' }}; background-color: #F8F9FA;">
                                            <p class="mb-1" style="color: #9d1c20;">
                                                <strong>{{ $payment->is_completed ? 'Payment' : 'Scheduled Payment'
                                                    }}:</strong>
                                                Rs.{{ number_format($payment->amount, 2) }}
                                            </p>
                                            <p class="mb-1" style="color: #9d1c20;">
                                                <strong>Method:</strong> {{ ucfirst(str_replace('_', ' ',
                                                $payment->payment_method)) }}
                                            </p>
                                            @if ($payment->payment_reference)
                                            <p class="mb-1" style="color: #9d1c20;">
                                                <strong>Reference:</strong> {{ $payment->payment_reference }}
                                            </p>
                                            @endif
                                            @if ($payment->payment_date)
                                            <p class="mb-0" style="color: #9d1c20;">
                                                <strong>Date:</strong> {{
                                                \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}
                                            </p>
                                            @endif
                                            @if ($payment->due_date)
                                            <p class="mb-0" style="color: #9d1c20;">
                                                <strong>Due Date:</strong> {{
                                                \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}
                                            </p>
                                            @endif
                                        </div>
                                        @endforeach
                                        @else
                                        <p class="text-muted" style="color: #6B7280;">No payment information available
                                        </p>
                                        @endif

                                        @if ($receipt->notes)
                                        <h6 class="text-muted mt-3 mb-2 fw-medium" style="color: #6B7280;">NOTES</h6>
                                        <p class="font-italic" style="color: #6B7280;">{{ $receipt->notes }}</p>
                                        @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-1 rounded-3 shadow-sm" style="border-color: #9d1c20;">
                                            <div class="card-body p-3">
                                                <h6 class="card-title fw-bold tracking-tight" style="color: #9d1c20;">
                                                    ORDER SUMMARY</h6>
                                                <div class="d-flex justify-content-between mb-2"
                                                    style="color: #9d1c20;">
                                                    <span>Subtotal:</span>
                                                    <span>Rs.{{ number_format($receipt->subtotal, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2"
                                                    style="color: #9d1c20;">
                                                    <span>Total Discount:</span>
                                                    <span>Rs.{{ number_format($receipt->discount_amount, 2) }}</span>
                                                </div>
                                                <hr style="border-color: #9d1c20;">
                                                <div class="d-flex justify-content-between" style="color: #9d1c20;">
                                                    <span class="fw-bold">Grand Total:</span>
                                                    <span class="fw-bold">Rs.{{ number_format($receipt->total_amount, 2)
                                                        }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center mt-4 pt-3 border-top" style="border-color: #9d1c20;">
                                    <p class="mb-0 text-muted small" style="color: #6B7280;">Thank you for your
                                        purchase!</p>
                                </div>
                            </div>
                            @else
                            <div class="text-center p-5">
                                <p class="text-muted" style="color: #6B7280;">No receipt data available</p>
                            </div>
                            @endif
                        </div>
                        <div class="modal-footer border-top py-3" style="border-color: #9d1c20; background: #F8F9FA;">
                            <button type="button"
                                class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow"
                                data-bs-dismiss="modal"
                                style="background-color: #6B7280; border-color: #6B7280; color: #FFFFFF;"
                                onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';"
                                onmouseout="this.style.backgroundColor='#6B7280'; this.style.borderColor='#6B7280';">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    {{-- FIXED: Simplified script section --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const printButton = document.getElementById('printButton');
            if (printButton) {
                printButton.addEventListener('click', function() {
                    printSalesReceipt();
                });
            }
        });

        function printSalesReceipt() {
            const receiptContent = document.querySelector('#receiptContent')?.innerHTML || '';
            const printWindow = window.open('', '_blank', 'height=600,width=800');

            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Sales Receipt - Print</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                    body { font-family: sans-serif; padding: 20px; font-size: 14px; }
                    .table-bordered th, .table-bordered td { border: 1px solid #9d1c20 !important; padding: 2px 6px !important; font-size: 12px !important; }
                    .receipt-container { max-width: 700px; margin: 0 auto; }
                    .d-flex.flex-row { display: flex; flex-direction: row; gap: 2rem; }
                    .d-flex.flex-row > .flex-fill { width: 50%; min-width: 0; }
                    .row > .col-md-6 { width: 50%; float: left; min-width: 0; }
                    @media print {
                        .no-print, .btn, .modal-footer { display: none !important; }
                        body { padding: 0; }
                        .receipt-container { box-shadow: none; border: none; }
                        .d-flex.flex-row { display: flex !important; flex-direction: row !important; gap: 2rem !important; }
                        .d-flex.flex-row > .flex-fill { width: 50% !important; min-width: 0 !important; }
                        .row { display: flex !important; flex-wrap: wrap !important; }
                        .row > .col-md-6 { width: 50% !important; float: none !important; min-width: 0 !important; }
                    }
                </style>
                </head>
                <body>
                    ${receiptContent}
                </body>
                </html>
            `);

            printWindow.document.close();
            printWindow.focus();

            // Use a timeout to ensure content is loaded before printing
            setTimeout(() => {
                printWindow.print();
                printWindow.close();
            }, 250);
        }

        document.addEventListener('livewire:initialized', () => {

            // Listener for showing modals from the backend
            window.addEventListener('showModal', event => {
                const modal = new bootstrap.Modal(document.getElementById(event.detail[0].modalId));
                modal.show();
            });

            // Listener for closing modals from the backend
            window.addEventListener('closeModal', event => {
                const modal = bootstrap.Modal.getInstance(document.getElementById(event.detail[0].modalId));
                if (modal) {
                    modal.hide();
                }
            });

            // This is the single, working toast notification listener using SweetAlert2
            window.addEventListener('show-toast', event => {
                const data = event.detail[0];
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: data.type,
                    title: data.message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            });

        });
    </script>
    @endpush
</div>