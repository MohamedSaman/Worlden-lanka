<div>
    @push('styles')
    @include('components.admin-styles')
    <style>
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

        .manual-entry-card {
            border-left: 4px solid #9d1c20 !important;
        }

        input[type="number"].is-invalid {
            border-color: #17a2b8;
        }

        @media print {
            .modal-header {
                display: none !important;
            }
            body * {
                visibility: hidden;
            }
            #receiptContent, #receiptContent * {
                visibility: visible;
            }
            #receiptContent {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }

        @media (max-width: 767.98px) {
            .table td:nth-child(3),
            .table th:nth-child(3),
            .table td:nth-child(6),
            .table th:nth-child(6) {
                display: none;
            }
        }
    </style>
    @endpush

    <div class="container-fluid py-4">
        <!-- Edit Mode Banner -->
        @if($isEditMode)
        <div class="alert alert-warning d-flex justify-content-between align-items-center mb-4" 
            style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); border: 2px solid #ff9800;" role="alert">
            <div>
                <i class="bi bi-pencil-square me-2"></i>
                <strong>EDIT MODE:</strong> You are editing Invoice #{{ $invoiceNumber }}. Make your changes and click "Complete Sale" to update or "Cancel Edit".
            </div>
            <button class="btn btn-sm btn-dark" wire:click="cancelEdit">
                <i class="bi bi-x-circle me-1"></i>Cancel Edit
            </button>
        </div>
        @endif

        <!-- Professional Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm professional-header-card">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h2 class="text-white mb-1 fw-bold">
                                    <i class="bi bi-pencil-square me-2"></i>Manual Billing System
                                </h2>
                                <p class="text-white-50 mb-0">
                                    Create invoices manually without stock management
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
                                                        style="color: #17a2b8; font-size: 0.9rem;">
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
                        <!-- Manual Product Entry Section -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-0 shadow-sm manual-entry-card" style="overflow: visible;">
                                    <div class="card-body p-3" style="overflow: visible;">
                                        <div class="d-flex align-items-center mb-3">
                                            <i class="bi bi-plus-circle-fill text-danger me-2" style="font-size: 1.2rem;"></i>
                                            <h6 class="mb-0 text-danger fw-bold">Add Product Manually</h6>
                                        </div>
                                        <div class="row g-2" style="overflow: visible;">
                                            <div class="col-md-3" style="overflow: visible;">
                                                <label class="form-label small mb-1">Product Name *</label>
                                                <div class="position-relative" style="z-index: 1000;">
                                                    <input type="text" 
                                                        class="form-control form-control-sm" 
                                                        wire:model.live.debounce.300ms="manualProductName"
                                                        placeholder="Type to search products..."
                                                        autocomplete="off">
                                                    
                                                    @if($showProductDropdown && $productSearchResults && $productSearchResults->count() > 0)
                                                        <div class="position-absolute w-100 bg-white border border-2 rounded shadow-lg" 
                                                             style="z-index: 9999; max-height: 300px; overflow-y: auto; top: 100%; left: 0; margin-top: 4px; border-color: #dee2e6 !important;">
                                                            @foreach($productSearchResults as $product)
                                                                <div class="p-3 border-bottom"
                                                                     wire:click="selectProduct({{ $product->id }})"
                                                                     style="cursor: pointer; transition: all 0.2s;"
                                                                     onmouseover="this.style.backgroundColor='#e9ecef'; this.style.transform='scale(1.01)'"
                                                                     onmouseout="this.style.backgroundColor='white'; this.style.transform='scale(1)'">
                                                                    <div class="fw-bold text-dark mb-1" style="font-size: 14px;">{{ $product->product_name }}</div>
                                                                    <div class="small text-muted" style="font-size: 12px;">
                                                                        <span class="badge bg-secondary me-1">{{ $product->product_code ?? 'N/A' }}</span>
                                                                        <span class="badge bg-info me-1">{{ $product->category->name ?? 'N/A' }}</span>
                                                                        <span class="badge bg-success">Rs. {{ number_format($product->selling_price, 2) }}</span>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Product Code</label>
                                                <input type="text" 
                                                    class="form-control form-control-sm" 
                                                    wire:model="manualProductCode"
                                                    placeholder="Optional">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Category</label>
                                                <input type="text" 
                                                    class="form-control form-control-sm" 
                                                    wire:model="manualProductCategory"
                                                    list="categoryList"
                                                    placeholder="Select or type">
                                                <datalist id="categoryList">
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->name }}">
                                                    @endforeach
                                                </datalist>
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small mb-1">Price *</label>
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text">Rs.</span>
                                                    <input type="number" 
                                                        class="form-control" 
                                                        wire:model="manualProductPrice"
                                                        placeholder="0.00"
                                                        step="0.01"
                                                        min="0">
                                                </div>
                                            </div>
                                            <div class="col-md-1">
                                                <label class="form-label small mb-1">Qty *</label>
                                                <input type="number" 
                                                    class="form-control form-control-sm" 
                                                    wire:model="manualProductQuantity"
                                                    placeholder="1"
                                                    min="1">
                                            </div>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" 
                                                    class="btn btn-danger w-100 btn-sm" 
                                                    wire:click="addManualProduct">
                                                    <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                                </button>
                                            </div>
                                        </div>
                                        <small class="text-muted d-block mt-2">
                                            <i class="bi bi-info-circle me-1"></i>Manually add products without database or stock tracking.
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Cart Table -->
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Product</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Category</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Unit Price</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Quantity</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Quantity Type</th>
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
                                                    <div
                                                        style="width:40px;height:40px;background-color:#e0f7fa;border-radius:0.5rem;display:flex;align-items:center;justify-content:center; margin-right: 15px;">
                                                        <i class="bi bi-box-seam text-info"></i>
                                                    </div>
                                                </div>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $item['name'] }}</h6>
                                                    <small class="text-xs text-secondary mb-0">{{ $item['code'] ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="text-xs font-weight-bold mb-0">
                                                {{ $item['category'] ?? 'N/A' }}
                                            </p>
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
                                            <div style="width: 80px;">
                                                <input type="number"
                                                    class="form-control form-control-sm text-center quantity-input"
                                                    value="{{ $quantities[$id] }}" min="1"
                                                    wire:model.blur="quantities.{{ $id }}">
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
                                            <p class="text-xs font-weight-bold mb-0">
                                                Rs.{{ number_format(($prices[$id]) *
                                                $quantities[$id] - ($discounts[$id] ?? 0) * $quantities[$id], 2) }}
                                            </p>
                                        </td>
                                        <td>
                                            <button class="btn btn-link btn-sm text-danger rounded-circle"
                                                wire:click="removeFromCart('{{ $id }}')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text">
                                                <i class="fas fa-shopping-cart fa-3x mb-3 text-danger"></i>
                                                <p>Your cart is empty. Add products manually to create a bill.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Payment and Summary Section -->
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header pb-0" style="background-color: #9d1c20;">
                                        <h6 class="text-white">Customer & Payment Information</h6>
                                    </div>
                                    <div class="card-body" style="height: 500px; overflow-y: auto;">
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Customer Name <small class="text-muted">(Select or Type)</small></label>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="input-group flex-grow-1">
                                                    <span class="input-group-text">
                                                        <i class="bi bi-person"></i>
                                                    </span>
                                                    <input type="text" class="form-control" wire:model="customerName"
                                                        list="customerList" placeholder="Select or type customer name">
                                                    <datalist id="customerList">
                                                        @foreach ($customers as $customer)
                                                        <option value="{{ $customer->name }}">{{ $customer->phone }}</option>
                                                        @endforeach
                                                    </datalist>
                                                </div>
                                                <button class="btn d-flex align-items-center"
                                                    style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';"
                                                    data-bs-toggle="modal" data-bs-target="#addCustomerModal">
                                                    <i class="bi bi-plus-circle me-1"></i>ADD
                                                </button>
                                            </div>
                                            @error('customerName')
                                            <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Customer Phone <small class="text-muted">(Optional)</small></label>
                                            <div class="input-group">
                                                <span class="input-group-text">
                                                    <i class="bi bi-telephone"></i>
                                                </span>
                                                <input type="text" class="form-control" wire:model="customerPhone"
                                                    placeholder="Enter customer phone">
                                            </div>
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
                                                            <label class="form-label small">Cheque Number</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                wire:model="newCheque.number"
                                                                placeholder="Enter cheque number">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small">Bank Name</label>
                                                            <select class="form-select form-select-sm"
                                                                wire:model="newCheque.bank">
                                                                <option value="">Select Bank</option>
                                                                @foreach($banks as $bank)
                                                                <option value="{{ $bank }}">{{ $bank }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small">Cheque Date</label>
                                                            <input type="date" class="form-control form-control-sm"
                                                                wire:model="newCheque.date">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small">Amount</label>
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text">Rs.</span>
                                                                <input type="number" class="form-control"
                                                                    wire:model="newCheque.amount" step="0.01">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="text-end mt-3">
                                                        <button type="submit" class="btn btn-sm btn-danger">
                                                            <i class="fas fa-plus me-1"></i>Add Cheque
                                                        </button>
                                                    </div>
                                                </form>

                                                @if(!empty($cheques))
                                                <div class="table-responsive mt-3">
                                                    <table class="table table-sm table-bordered">
                                                        <thead>
                                                            <tr>
                                                                <th>Cheque No</th>
                                                                <th>Bank</th>
                                                                <th>Date</th>
                                                                <th>Amount</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($cheques as $index => $cheque)
                                                            <tr>
                                                                <td>{{ $cheque['number'] }}</td>
                                                                <td>{{ $cheque['bank'] }}</td>
                                                                <td>{{ $cheque['date'] }}</td>
                                                                <td>Rs.{{ number_format($cheque['amount'], 2) }}</td>
                                                                <td>
                                                                    <button class="btn btn-sm btn-danger"
                                                                        wire:click="removeCheque({{ $index }})">
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
                                                    <i class="fas fa-clock me-2"></i>Credit Payment
                                                </h6>
                                                <p class="text-info">The total amount of <strong>Rs.{{
                                                        number_format($grandTotal, 2) }}</strong> will be marked as credit/due.</p>
                                            </div>
                                        </div>
                                        @endif

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Notes</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Add notes (optional)" wire:model="saleNotes">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Delivery Note</label>
                                                <input type="text" class="form-control"
                                                    placeholder="Add delivery notes (optional)"
                                                    wire:model="deliveryNote">
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
                                            <button class="btn btn-secondary me-2" wire:click="clearCart">
                                                <i class="bi bi-trash me-1"></i>Clear Cart
                                            </button>
                                            <button class="btn btn-danger flex-grow-1" wire:click="completeSale"
                                                {{ empty($cart) ? 'disabled' : '' }}>
                                                <i class="bi bi-check-circle me-1"></i>Complete Sale
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Customer Modal -->
        <div class="modal fade" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #9d1c20;">
                        <h5 class="modal-title text-white" id="addCustomerModalLabel">Add New Customer</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="saveCustomer">
                            <div class="mb-3">
                                <label class="form-label">Name *</label>
                                <input type="text" class="form-control" wire:model="newCustomerName" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone</label>
                                <input type="text" class="form-control" wire:model="newCustomerPhone">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" wire:model="newCustomerEmail">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type</label>
                                <select class="form-select" wire:model="newCustomerType">
                                    <option value="">Select Type</option>
                                    <option value="Regular">Regular</option>
                                    <option value="Wholesale">Wholesale</option>
                                    <option value="VIP">VIP</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Address</label>
                                <textarea class="form-control" wire:model="newCustomerAddress" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Notes</label>
                                <textarea class="form-control" wire:model="newCustomerNotes" rows="2"></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-danger">Save Customer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receipt Modal -->
        @if($showReceipt && $receipt)
        <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel"
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
                                id="printButton" style="background-color: #9d1c20;border-color:#fff; color: #fff;" onclick="printSalesReceipt()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-4" id="receiptContent">
                        <div class="receipt-container">
                            <div class="text-center mb-4">
                                <h3 class="mb-1 fw-bold tracking-tight" style="color: #9d1c20;">PLUS</h3>
                                <p class="mb-0 small" style="color: #9d1c20;">NO 20/2/1, 2nd FLOOR,HUNTER
                                    BUILDING,BANKSHALLL STREET,COLOMBO-11</p>
                                <p class="mb-0 small" style="color: #9d1c20;">Phone: 011 - 2332786 |
                                    Email: plusaccessories.lk@gmail.com</p>
                                <h4 class="mt-3 border-bottom border-2 pb-2 fw-bold"
                                    style="color: #9d1c20; border-color: #9d1c20;">SALES RECEIPT</h4>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="mb-2 fw-medium" style="color: #9d1c20;">INVOICE DETAILS</h6>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Invoice Number: {{
                                            $receipt->invoice_number }}</strong></p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Date: {{
                                            $receipt->created_at->setTimezone('Asia/Colombo')->format('d/m/Y h:i A') }}</strong>
                                    </p>
                                    <p class="mb-1" style="color: #9d1c20;">
                                        <strong>Payment Status: {{ ucfirst($receipt->payment_status) }}
                                        </strong>
                                    </p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Delivery Note: {{ $receipt->delivery_note ?? 'N/A' }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2 fw-medium" style="color: #9d1c20;">CUSTOMER DETAILS</h6>
                                    @if ($receipt->customer)
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Name: {{
                                            $receipt->customer->name }}</strong></p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Phone: {{
                                            $receipt->customer->phone ?? 'N/A' }}</strong></p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Type: {{
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
                                            <td class="text-center py-2">{{ $item->product_name }}</td>
                                    
                                            <td class="text-center py-2">Rs.{{ number_format($item->price, 2) }}</td>
                                            <td class="text-center py-2">{{ $item->quantity }} {{ $item->quantity_type }}</td>
                                            <td class="text-center py-2">Rs.{{ number_format($item->discount * $item->quantity, 2) }}</td>
                                            <td class="text-center py-2">Rs.{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text mb-2 fw-medium" style="color: #9d1c20;">PAYMENT INFORMATION</h6>
                                    @if ($receipt->payments->count() > 0)
                                    @foreach ($receipt->payments as $payment)
                                    <div class="mb-2 p-2 border-start border-3 rounded-2"
                                        style="border-color: {{ $payment->is_completed ? '#0F5132' : '#664D03' }}; background-color: #F8F9FA;">
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>{{ $payment->is_completed ? 'Payment' : 'Scheduled Payment' }}: Rs.{{ number_format($payment->amount, 2) }}</strong>
                                        </p>
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>Method: {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</strong>
                                        </p>
                                        @if ($payment->payment_reference)
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>Reference: {{ $payment->payment_reference }}</strong>
                                        </p>
                                        @endif
                                        @if ($payment->payment_date)
                                        <p class="mb-0" style="color: #9d1c20;">
                                            <strong>Date: {{ \Carbon\Carbon::parse($payment->payment_date)->format('d/m/Y') }}</strong>
                                        </p>
                                        @endif
                                        @if ($payment->due_date)
                                        <p class="mb-0" style="color: #9d1c20;">
                                            <strong>Due Date: {{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}</strong>
                                        </p>
                                        @endif
                                    </div>
                                    @endforeach
                                    @else
                                    <p class="text" style="color: #9d1c20;">No payment information available</p>
                                    @endif

                                    @if ($receipt->notes)
                                    <h6 class="text mt-3 mb-2 fw-medium" style="color: #9d1c20;">NOTES</h6>
                                    <p class="font-italic" style="color: #9d1c20;">{{ $receipt->notes }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-1 rounded-3 shadow-sm" style="border-color: #9d1c20;">
                                        <div class="card-body p-3">
                                            <h6 class="card-title fw-bold tracking-tight" style="color: #9d1c20;">ORDER SUMMARY</h6>
                                            <div class="d-flex justify-content-between mb-2" style="color: #9d1c20;">
                                                <span><strong>Subtotal:</strong></span>
                                                <span><strong>Rs.{{ number_format($receipt->subtotal, 2) }}</strong></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2" style="color: #9d1c20;">
                                                <span><strong>Discount:</strong></span>
                                                <span><strong>Rs.{{ number_format($receipt->discount_amount, 2) }}</strong></span>
                                            </div>
                                            <hr class="my-2" style="border-color: #9d1c20;">
                                            <div class="d-flex justify-content-between mb-2" style="color: #9d1c20;">
                                                <span class="fw-bold fs-5"><strong>Grand Total:</strong></span>
                                                <span class="fw-bold fs-5"><strong>Rs.{{ number_format($receipt->total_amount, 2) }}</strong></span>
                                            </div>
                                            @if ($receipt->due_amount > 0)
                                            <div class="d-flex justify-content-between mb-2 text-danger">
                                                <span><strong>Due Amount:</strong></span>
                                                <span><strong>Rs.{{ number_format($receipt->due_amount, 2) }}</strong></span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4">
                                <p class="mb-0 fw-bold" style="color: #9d1c20;">Thank you for your purchase!</p>
                                <p class="mb-0 small" style="color: #9d1c20;">For any inquiries, please contact us</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Print Sales Receipt Function
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
                        * { color: #000 !important; font-weight: bold !important; }
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

        // Toast notifications
        window.addEventListener('show-toast', event => {
            const data = event.detail[0];
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
            Toast.fire({
                icon: data.type,
                title: data.message
            });
        });

        // Sale completed event - show success message and modal
        window.addEventListener('sale-completed', event => {
            const data = event.detail;
            const message = data.message || `Sale completed successfully! Invoice #${data.invoiceNumber}`;
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: message,
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                // Open receipt modal after success message
                const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
                receiptModal.show();
            });
        });
    </script>
    @endpush
</div>