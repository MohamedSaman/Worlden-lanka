<div>
    @push('styles')
    @include('components.admin-styles')
    <style>
        .search-results-container {
            z-index: 1050;
            border: 2px solid #dee2e6;
            border-top: none;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .search-result-item:hover {
            background: linear-gradient(to right, #fff5f5, #ffffff);
            cursor: pointer;
            border-left: 3px solid #9d1c20;
        }

        .search-result-item {
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .professional-header-card {
            background: linear-gradient(135deg, #9d1c20 0%, #d34d51 100%);
            border-radius: 12px;
        }

        .invoice-info-card {
            transition: all 0.3s ease;
        }

        .invoice-info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(157, 28, 32, 0.15) !important;
        }

        .input-group-text {
            background-color: #f8f9fa;
        }

        .form-control:focus {
            border-color: #9d1c20;
            box-shadow: 0 0 0 0.2rem rgba(157, 28, 32, 0.15);
        }

        .search-enhanced {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: all 0.3s ease;
        }

        .search-enhanced:focus {
            border-color: #9d1c20;
            box-shadow: 0 0 0 0.2rem rgba(157, 28, 32, 0.1);
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

        @media (max-width: 767.98px) {

            .table td:nth-child(3),
            .table th:nth-child(3),
            .table td:nth-child(6),
            .table th:nth-child(6) {
                display: none;
            }
        }

        /* Edit Mode Styles */
        .edit-mode-banner {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.9;
            }
        }
    </style>
    @endpush

    <div class="container-fluid py-4">
        <!-- Edit Mode Banner -->
        @if($isEditMode)
        <div class="alert alert-warning edit-mode-banner d-flex justify-content-between align-items-center mb-4" role="alert">
            <div>
                <i class="bi bi-pencil-square me-2"></i>
                <strong>EDIT MODE:</strong> You are editing Invoice #{{ $invoiceNumber }}. Make your changes and click "Update Sale" or "Cancel Edit".
            </div>
            <button class="btn btn-sm btn-dark" wire:click="cancelEdit">
                <i class="bi bi-x-circle me-1"></i>Cancel Edit
            </button>
        </div>
        @endif

        <!-- Professional Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #9d1c20 0%, #d34d51 100%);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="text-white mb-1 fw-bold">
                                    <i class="bi bi-receipt-cutoff me-2"></i>Store Billing System
                                </h2>
                                <p class="text-white-50 mb-0">
                                    @if($isEditMode)
                                    Update invoice and manage sale changes
                                    @else
                                    Create invoices and manage sales transactions
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm">
                                    <div class="card-body p-3">
                                        <div class="row g-2">
                                            <div class="col-md-7">
                                                <label class="form-label small text mb-1">Invoice Number</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light border-end-0">
                                                        <i class="bi bi-hash text-danger"></i>
                                                    </span>
                                                    <input type="text"
                                                        wire:model.live="invoiceNumber"
                                                        class="form-control border-start-0 fw-bold"
                                                        placeholder="Invoice Number"
                                                        style="color: #9d1c20; font-size: 0.95rem;">
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small text mb-1">Invoice Date</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-light border-end-0">
                                                        <i class="bi bi-calendar3 text-danger"></i>
                                                    </span>
                                                    <input type="date"
                                                        wire:model.live="invoiceDate"
                                                        class="form-control border-start-0 fw-semibold"
                                                        max="{{ date('Y-m-d') }}"
                                                        style="color: #9d1c20; font-size: 0.9rem;">
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
        </div>


        <!-- Main Content -->
        <div class="row">
            <div class="col-12">
                <div class="card animate-slide-in">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-6 mx-auto">
                                <div class="position-relative">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search text-danger"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control border-start-0 search-enhanced"
                                            style="border-left: none !important;"
                                            placeholder="🔍 Search by product code, name, model, brand or barcode..."
                                            wire:model.live="search"
                                            autocomplete="off">
                                    </div>

                                    @if ($search && count($searchResults) > 0)
                                    <div class="search-results-container position-absolute mt-1 w-100 bg-white shadow-lg rounded-3 border"
                                        style="max-height: 400px; overflow-y: auto; z-index: 1000; top: 100%; left: 0;">
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
                                                    @if($result->customer_field && isset($result->customer_field['Size']))
                                                    <span class="me-2">| Size: <strong>{{ $result->customer_field['Size'] }}</strong></span>
                                                    @endif
                                                    @if($result->customer_field && isset($result->customer_field['Color']))
                                                    <span>| Color: <strong>{{ $result->customer_field['Color'] }}</strong></span>
                                                    @endif
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
                                    <div class="search-results-container position-absolute mt-1 w-100 bg-white shadow-lg rounded-3 border p-4"
                                        style="top: 100%; left: 0; z-index: 1000;">
                                        <div class="text-center text">
                                            <i class="bi bi-search fa-2x mb-3 d-block" style="font-size: 2rem; color: #9d1c20;"></i>
                                            <p class="mb-0">No products found matching "<strong>{{ $search }}</strong>"</p>
                                        </div>
                                    </div>
                                    @endif

                                </div>
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
                                        <!-- <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Discount</th> -->
                                        @if(collect($cart)->some(fn($item) => isset($item['customer_field']['Size']) && $item['customer_field']['Size'] && isset($item['customer_field']['Color']) && $item['customer_field']['Color']))
                                        <th class="text-uppercase text-center text-secondary text-xxs font-weight-bolder opacity-7">
                                            Size | Color</th>
                                        @endif
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
                                                <input type="number"
                                                    class="form-control form-control-sm text-center quantity-input"
                                                    value="{{ $quantities[$id] }}" min="1"
                                                    max="{{ $item['stock_quantity'] }}"
                                                    wire:model.blur="quantities.{{ $id }}">
                                                <small class="text">Max: {{ $item['stock_quantity'] }}</small>
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

                                        <!-- <td>
                                            <div class="input-group input-group-sm" style="width: 150px;">
                                                <span class="input-group-text">Rs.</span>
                                                <input type="number" class="form-control form-control-sm"
                                                    value="{{ $discounts[$id] ?? 0 }}" min="0" max="{{ $prices[$id] }}"
                                                    step="0.01" wire:model.blur="discounts.{{ $id }}">
                                            </div>
                                        </td> -->
                                        @if(collect($cart)->some(fn($item) => isset($item['customer_field']['Size']) && $item['customer_field']['Size'] && isset($item['customer_field']['Color']) && $item['customer_field']['Color']))
                                        <td>
                                            <p class="text-xs text-center font-weight-bold mb-0">
                                                @if(isset($item['customer_field']['Size']) && $item['customer_field']['Size'] && isset($item['customer_field']['Color']) && $item['customer_field']['Color'])
                                                {{ $item['customer_field']['Size'] }} | {{ $item['customer_field']['Color'] }}
                                                @else
                                                N/A
                                                @endif
                                            </p>
                                        </td>
                                        @endif
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                Rs.{{ number_format(($prices[$id]) *
                                                $quantities[$id] - ($discounts[$id] ?? 0) * $quantities[$id], 2) }}
                                            </p>
                                        </td>
                                        <td>
                                            <button class="btn btn-link btn-sm text-danger rounded-circle "
                                                wire:click="removeFromCart({{ $id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text">
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

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Notes</label>
                                                <input text="text" class="form-control"
                                                    placeholder="Add any notes about this sale"
                                                    wire:model="saleNotes"></input>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Delivery Notes</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Add any delivery notes about this sale"
                                                    wire:model="deliveryNote"></input>
                                            </div>
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
                                            @if($isEditMode)
                                            <button class="btn btn-warning me-2" wire:click="cancelEdit">
                                                <i class="bi bi-x-circle me-2"></i>Cancel
                                            </button>
                                            <button class="btn btn-primary flex-grow-1" wire:click="completeSale">
                                                <i class="fas fa-save me-2"></i>Update Sale
                                            </button>
                                            @else
                                            <button class="btn btn-danger me-2" wire:click="clearCart"
                                                style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">
                                                <i class="fas fa-times me-2"></i>Clear
                                            </button>
                                            <button class="btn btn-success flex-grow-1" wire:click="completeSale">
                                                <i class="fas fa-check me-2"></i>Complete Sale
                                            </button>
                                            @endif
                                        </div>
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
                                                <i class="bi bi-box-seam text" style="font-size: 5rem;"></i>
                                                <p class="text mt-2">No image available</p>
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
                                                <p class="text mb-1">Description</p>
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

        <!-- Receipt modal -->
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
                            <button type="button" class="btn btn-sm btn-warning rounded-full px-3 transition-all hover:shadow"
                                wire:click="editSale">
                                <i class="bi bi-pencil-square me-1"></i>Edit
                            </button>
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
                                <p class="mb-0  small" style="color: #9d1c20;">NO 20/2/1, 2nd FLOOR,HUNTER
                                    BUILDING,BANKSHALLL STREET,COLOMBO-11</p>
                                <p class="mb-0  small" style="color: #9d1c20;">Phone: 011 - 2332786 |
                                    Email: plusaccessories.lk@gmail.com</p>
                                <h4 class="mt-3 border-bottom border-2 pb-2 fw-bold"
                                    style="color: #9d1c20; border-color: #9d1c20;">SALES RECEIPT</h4>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class=" mb-2 fw-medium" style="color: #9d1c20;">INVOICE DETAILS
                                    </h6>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Invoice Number: {{
                                            $receipt->invoice_number }}</strong></p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Date: {{
                                            \Carbon\Carbon::parse($receipt->sales_date)->setTimezone('Asia/Colombo')->format('d/m/Y h:i A') }}</strong>
                                    </p>
                                    <p class="mb-1" style="color: #9d1c20;">
                                        <strong>Payment Status:{{ ucfirst($receipt->payment_status) }}
                                        </strong>
                                    </p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Delivery Note: {{ $receipt->delivery_note ?? 'N/A' }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class=" mb-2 fw-medium" style="color: #9d1c20;">CUSTOMER DETAILS
                                    </h6>
                                    @if ($receipt->customer)
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Name: {{
                                            $receipt->customer->name }}</strong></p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Phone: {{
                                            $receipt->customer->phone ?? 'N/A' }}</strong></p>
                                    <strong class="mb-1" style="color: #9d1c20;"><strong>Type:</strong> {{
                                            ucfirst($receipt->customer_type) ?? 'N/A' }}</strong></p>
                                    @else
                                    <p class="text" style="color: #9d1c20;"><strong>Walk-in Customer</strong></p>
                                    @endif
                                </div>
                            </div>

                            <h6 class="text mb-2 fw-medium" style="color: #9d1c20;">PURCHASED ITEMS</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-sm border-1"
                                    style="border-color: #9d1c20;">
                                    <thead style="background-color: #9d1c20; color: #FFFFFF;">
                                        <tr>
                                            <th scope="col" class="text-center py-2">No</th>
                                            <th scope="col" class="text-center py-2">Item</th>
                                            @if($receipt->items->some(fn($item) => $item->product && isset($item->product->customer_field['Size']) && $item->product->customer_field['Size'] && isset($item->product->customer_field['Color']) && $item->product->customer_field['Color']))
                                            <th scope="col" class="text-center py-2">Size | Color</th>
                                            @endif
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
                                            @if($receipt->items->some(fn($i) => $i->product && isset($i->product->customer_field['Size']) && $i->product->customer_field['Size'] && isset($i->product->customer_field['Color']) && $i->product->customer_field['Color']))
                                            <td class="text-center py-2">
                                                @if($item->product && isset($item->product->customer_field['Size']) && $item->product->customer_field['Size'] && isset($item->product->customer_field['Color']) && $item->product->customer_field['Color'])
                                                {{ $item->product->customer_field['Size'] }} | {{ $item->product->customer_field['Color'] }}
                                                @else
                                                -
                                                @endif
                                            </td>
                                            @endif
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
                                    <h6 class="text mb-2 fw-medium" style="color: #9d1c20;">PAYMENT
                                        INFORMATION</h6>
                                    @if ($receipt->payments->count() > 0)
                                    @foreach ($receipt->payments as $payment)
                                    <div class="mb-2 p-2 border-start border-3 rounded-2"
                                        style="border-color: {{ $payment->is_completed ? '#0F5132' : '#664D03' }}; background-color: #F8F9FA;">
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>{{ $payment->is_completed ? 'Payment' : 'Scheduled Payment'
                                                    }}:
                                                Rs.{{ number_format($payment->amount, 2) }}</strong>
                                        </p>
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>Method: {{ ucfirst(str_replace('_', ' ',
                                                $payment->payment_method)) }}</strong>
                                        </p>
                                        @if ($payment->payment_reference)
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>Reference: {{ $payment->payment_reference }}</strong>
                                        </p>
                                        @endif
                                        @if ($payment->payment_date)
                                        <p class="mb-0" style="color: #9d1c20;">
                                            <strong>Date: {{
                                                \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</strong>
                                        </p>
                                        @endif
                                        @if ($payment->due_date)
                                        <p class="mb-0" style="color: #9d1c20;">
                                            <strong>Due Date: {{
                                                \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}</strong>
                                        </p>
                                        @endif
                                    </div>
                                    @endforeach
                                    @else
                                    <p class="text" style="color: #9d1c20;">No payment information available
                                    </p>
                                    @endif

                                    @if ($receipt->notes)
                                    <h6 class="text mt-3 mb-2 fw-medium" style="color: #9d1c20;">NOTES</h6>
                                    <p class="font-italic" style="color: #9d1c20;">{{ $receipt->notes }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-1 rounded-3 shadow-sm" style="border-color: #9d1c20;">
                                        <div class="card-body p-3">
                                            <h6 class="card-title fw-bold tracking-tight" style="color: #9d1c20;">
                                                ORDER SUMMARY</h6>
                                            <div class="d-flex justify-content-between mb-2"
                                                style="color: #9d1c20;">
                                                <span><strong>Subtotal:</strong></span>
                                                <span><strong>Rs.{{ number_format($receipt->subtotal, 2) }}</strong></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2"
                                                style="color: #9d1c20;">
                                                <span><strong>Total Discount:</strong></span>
                                                <span><strong>Rs.{{ number_format($receipt->discount_amount, 2) }}</strong></span>
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
                                <p class="mb-0 text small" style="color: #9d1c20;">Thank you for your
                                    purchase!</p>
                            </div>
                        </div>
                        @else
                        <div class="text-center p-5">
                            <p class="text" style="color: #9d1c20;">No receipt data available</p>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer border-top py-3" style="border-color: #9d1c20; background: #F8F9FA;">
                        <button type="button"
                            class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow"
                            data-bs-dismiss="modal"
                            style="background-color: #9d1c20; border-color: #9d1c20; color: #FFFFFF;"
                            onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';"
                            onmouseout="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
                    body {
                        font-family: "Courier New", monospace;
                        font-size: 14px;
                        color: #000 !important;
                        font-weight: bold !important;
                    }
                    * { color: #000 !important; }
                    .table-bordered th, .table-bordered td { border: 1px solid #000 !important; padding: 2px 6px !important; font-size: 12px !important; }
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
                        * { color: #000 !important;
                        font:bold !important;  }
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

        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 250);
    }

    document.addEventListener('livewire:initialized', () => {

        window.addEventListener('showModal', event => {
            const modal = new bootstrap.Modal(document.getElementById(event.detail[0].modalId));
            modal.show();
        });

        window.addEventListener('closeModal', event => {
            const modal = bootstrap.Modal.getInstance(document.getElementById(event.detail[0].modalId));
            if (modal) {
                modal.hide();
            }
        });

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