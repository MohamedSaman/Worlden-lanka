<div>
    <div class="container-fluid py-4">
        <!-- Page Header -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card card-header-modern mb-4">
                    <div class="d-flex align-items-center">
                        <div class="icon-wrapper me-3">
                            <i class="bi bi-card-list"></i>
                        </div>
                        <div>
                            <h3 class="mb-1">Payment Records</h3>
                            <p class="mb-0">View and manage all payment records</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card primary animate-fade-in">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape icon-md rounded-circle bg-danger bg-opacity-10 me-3 text-center">
                                <i class="bi bi-wallet2 text-danger"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Total Pay Amount</p>
                                <div class="d-flex align-items-baseline mt-1">
                                    <h4 class="mb-0 fw-bold text-gray-800">Rs.{{ number_format($totalPayments, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape icon-md rounded-circle bg-danger bg-opacity-10 me-3 text-center">
                                <i class="bi bi-receipt-cutoff text-danger"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Total Pay Count</p>
                                <div class="d-flex align-items-baseline mt-1">
                                    <h4 class="mb-0 fw-bold text-gray-800">{{ number_format($totalPayCount) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape icon-md rounded-circle bg-danger bg-opacity-10 me-3 text-center">
                                <i class="bi bi-hourglass-split text-danger"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Today Pay Amount</p>
                                <div class="d-flex align-items-baseline mt-1">
                                    <h4 class="mb-0 fw-bold text-gray-800">Rs.{{ number_format($todayTotalPayments, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center">
                            <div class="icon-shape icon-md rounded-circle bg-danger bg-opacity-10 me-3 text-center">
                                <i class="bi bi-hourglass-split text-danger"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Today Pay Count</p>
                                <div class="d-flex align-items-baseline mt-1">
                                    <h4 class="mb-0 fw-bold text-gray-800">{{ number_format($todayPayCount) }}</h4>
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
                    <!-- Search & Filters -->
                    <div class="card-body border-bottom" style="background-color: #f8f9fa;">
                        <div class="row g-3 align-items-center">

                            <div class="col-lg-7">
                                <div class="search-box-modern">
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search text-primary-custom"></i>
                                        </span>
                                        <input type="text"
                                            class="form-control"
                                            placeholder="Search by invoice number or customer..."
                                            wire:model.live.debounce.300ms="search"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="d-flex gap-2 justify-content-lg-end">
                                    <div class="dropdown">
                                        <button class="btn btn-modern btn-primary-modern"
                                            type="button" id="filterDropdown" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="bi bi-funnel me-1"></i> Filters
                                            @if ($filters['status'] || $filters['paymentMethod'] || $filters['dateFrom'] || $filters['dateTo'] || $filters['dateRange'])
                                            <span class="badge bg-white text-primary ms-1">!</span>
                                            @endif
                                        </button>
                                        <div class="dropdown-menu p-4 shadow-lg border-0 rounded-4" style="width: 320px;"
                                            aria-labelledby="filterDropdown">
                                            <h6 class="dropdown-header bg-light rounded py-2 mb-3 text-center text-sm fw-semibold" style="color: #9d1c20;">Filter Options</h6>
                                            <div class="mb-3">
                                                <label class="form-label text-sm fw-semibold" style="color: #9d1c20;">Payment Status</label>
                                                <select class="form-select form-select-sm rounded-full shadow-sm"
                                                    wire:model.live="filters.status">
                                                    <option value="">All Statuses</option>
                                                    <option value="paid">Paid</option>
                                                    <option value="current">Current</option>
                                                    <option value="forward">Forward</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-sm fw-semibold" style="color: #9d1c20;">Payment Method</label>
                                                <select class="form-select form-select-sm rounded-full shadow-sm"
                                                    wire:model.live="filters.paymentMethod">
                                                    <option value="">All Methods</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="card">Card</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                    <option value="cheque">Cheque</option>
                                                    <option value="credit">Credit</option>
                                                    <option value="cash+cheque">Cash + Cheque</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label text-sm fw-semibold d-block mb-2" style="color: #9d1c20;">Quick Dates</label>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-full" wire:click="setDatePreset('today')">Today</button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-full" wire:click="setDatePreset('this_week')">This Week</button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-full" wire:click="setDatePreset('this_month')">This Month</button>
                                                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-full" wire:click="setDatePreset('clear')">Clear</button>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-sm fw-semibold" style="color: #9d1c20;">Date From</label>
                                                <input type="date"
                                                    class="form-control form-control-sm rounded-full shadow-sm"
                                                    wire:model.live="filters.dateFrom">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-sm fw-semibold" style="color: #9d1c20;">Date To</label>
                                                <input type="date"
                                                    class="form-control form-control-sm rounded-full shadow-sm"
                                                    wire:model.live="filters.dateTo">
                                            </div>
                                            <div class="d-grid">
                                                <button class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                                    wire:click="resetFilters">
                                                    <i class="bi bi-x-circle me-1"></i> Reset Filters
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-modern">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th class="ps-4 text-uppercase text-xs fw-semibold py-3" style="color: #9d1c20;">Name</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">Amount</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">Method</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">Status</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($payments as $payment)
                                    <tr wire:key="payment-{{ $payment->id }}">
                                        <td class="fw-bold ps-4">
                                            @if($payment->sale && $payment->sale->customer)
                                            {{ $payment->sale->customer->name ?? 'Walk-in Customer' }}
                                            @elseif($payment->customer)
                                            {{ $payment->customer->name ?? 'Walk-in Customer' }}
                                            @else
                                            {{ $payment->sale->customer->name ?? 'Walk-in Customer' }}
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold">Rs.{{ number_format($payment->amount, 2) }}</td>
                                        <td class="text-center">
                                            @php $method = $payment->due_payment_method ?? $payment->payment_method; @endphp
                                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                                {{ ucfirst(str_replace('_', ' ', $method)) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @php
                                            if ($payment->status == 'Paid' || $payment->status == 'paid') {
                                            $displayStatus = 'Paid';
                                            $statusClass = 'success';
                                            } elseif ($payment->status == 'forward') {
                                            $displayStatus = 'Forward';
                                            $statusClass = 'warning';
                                            } else {
                                            $displayStatus = 'Current';
                                            $statusClass = 'info';
                                            }
                                            @endphp
                                            <span class="badge rounded-pill bg-{{ $statusClass }} bg-opacity-10 text-{{ $statusClass }} px-3 py-2">
                                                {{ $displayStatus }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <button class="btn btn-sm rounded-pill px-3" style="background-color:#9d1c20; color:white;" wire:click="viewPaymentDetails({{ $payment->id }})" wire:loading.attr="disabled">
                                                <i class="bi bi-receipt-cutoff"></i> View
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-gray-600">No payment records found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($payments->hasPages())
                        <div class="card-footer bg-white border-top p-4">
                            <div class="pagination-modern">
                                {{ $payments->links('livewire::bootstrap') }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div wire:ignore.self class="modal fade" id="payment-receipt-modal" tabindex="-1" aria-labelledby="payment-receipt-modal-label" aria-hidden="true" wire:key="payment-receipt-{{ $selectedPayment ? $selectedPayment->id : 'none' }}">
            <div class="modal-dialog modal-lg">
                <div class="modal-content rounded-4 shadow-xl"
                    style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #FFFFFF, #F8F9FA);">
                    <div class="modal-header"
                        style="background-color: #9d1c20; color: #FFFFFF; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                        <h5 class="modal-title fw-bold tracking-tight" id="payment-receipt-modal-label">
                            <i class="bi bi-receipt me-2"></i>Payment Receipt
                        </h5>
                        <div class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-sm rounded-full px-3 transition-all hover:shadow"
                                id="printButton" style="background-color: #9d1c20;border-color:#fff; color: #fff;" onclick="printReceiptContent()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100"
                                data-bs-dismiss="modal" aria-label="Close" wire:click="$set('selectedPayment', null)"></button>
                        </div>
                    </div>
                    <div class="modal-body p-4" id="receiptContent">
                        @if ($isLoadingPayment)
                        <div class="text-center p-5">
                            <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                                <i class="bi bi-receipt text-gray-600 fs-3"></i>
                            </div>
                            <h5 class="text-gray-600 fw-normal" style="font-size: 1.25rem;">Loading Receipt Details</h5>
                            <p class="text-sm text-gray-500 mb-0" style="font-size: 0.9rem;">Please wait while data is being loaded...</p>
                        </div>
                        @elseif ($selectedPayment)
                        <div class="receipt-container">
                            <div class="text-center mb-4">
                                <h3 class="mb-1 fw-bold tracking-tight" style="color: #9d1c20;">PLUS</h3>
                                <p class="mb-0 text-muted small" style="color: #6B7280;">NO 20/2/1, 2nd FLOOR,HUNTER
                                    BUILDING,BANKSHALLL STREET,COLOMBO-11</p>
                                <p class="mb-0 text-muted small" style="color: #6B7280;">Phone: 011 - 2332786 |
                                    Email: plusaccessories.lk@gmail.com</p>
                                <h4 class="mt-3 border-bottom border-2 pb-2 fw-bold"
                                    style="color: #9d1c20; border-color: #9d1c20;">PAYMENT RECEIPT</h4>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2 fw-medium" style="color: #6B7280;">INVOICE INFORMATION
                                    </h6>
                                    <div class="card border-1 rounded-3 shadow-sm" style="border-color: #9d1c20;">
                                        <div class="card-body p-3">
                                            @if($selectedPayment->sale)
                                            <p class="mb-1" style="color: #9d1c20;"><strong>Invoice:</strong> {{ $selectedPayment->sale->invoice_number ?? 'N/A' }}</p>
                                            <p class="mb-1" style="color: #9d1c20;"><strong>Sale Date:</strong> {{ $selectedPayment->sale->created_at ? $selectedPayment->sale->created_at->format('d/m/Y h:i A') : 'N/A' }}</p>
                                            <p class="mb-1" style="color: #9d1c20;"><strong>Total:</strong> Rs.{{ number_format($selectedPayment->sale->total_amount ?? 0, 2) }}</p>
                                            @php
                                            $paid = $selectedPayment->sale->payments->where('is_completed', true)->sum('amount');
                                            $remaining = $selectedPayment->sale->total_amount - $paid;
                                            @endphp
                                            <p class="mb-1" style="color: #9d1c20;"><strong>Remaining:</strong> Rs.{{ number_format($remaining, 2) }}</p>
                                            <p class="mb-0" style="color: #9d1c20;"><strong>Payment Status:</strong>
                                                <span class="badge"
                                                    style="background-color: {{ $selectedPayment->sale->payment_status == 'paid' ? '#0F5132' : ($selectedPayment->sale->payment_status == 'partial' ? '#664D03' : '#842029') }}; color: #FFFFFF;">
                                                    {{ $selectedPayment->sale->payment_status ? ucfirst($selectedPayment->sale->payment_status) : 'Unknown' }}
                                                </span>
                                            </p>
                                            @else
                                            <p class="text-muted" style="color: #6B7280;">This is a Brought-forward payment not associated with a specific sale.</p>
                                            @if($selectedPayment->customer)
                                            <p class="mb-1" style="color: #9d1c20;"><strong>Customer:</strong> {{ $selectedPayment->customer->name }}</p>
                                            <p class="mb-1" style="color: #9d1c20;"><strong>Phone:</strong> {{ $selectedPayment->customer->phone ?? 'N/A' }}</p>
                                            @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2 fw-medium" style="color: #6B7280;">CUSTOMER INFORMATION</h6>
                                    <div class="card border-1 rounded-3 shadow-sm" style="border-color: #9d1c20;">
                                        <div class="card-body p-3">
                                            @if($selectedPayment->sale && $selectedPayment->sale->customer)
                                            <p class="mb-1" style="color: #9d1c20;"><strong>Name:</strong> {{ $selectedPayment->sale->customer->name ?? 'Guest Customer' }}</p>
                                            <p class="mb-1" style="color: #9d1c20;"><strong>Phone:</strong> {{ $selectedPayment->sale->customer->phone ?? 'N/A' }}</p>
                                            @if($selectedPayment->sale->customer->address)
                                            <p class="mb-0" style="color: #9d1c20;"><strong>Address:</strong> {{ $selectedPayment->sale->customer->address }}</p>
                                            @endif
                                            @else
                                            <p class="text-muted" style="color: #6B7280;">No customer information available.</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <h6 class="text-muted mb-2 fw-medium" style="color: #6B7280;">PURCHASED ITEMS</h6>
                            <div class="table-responsive mb-4">
                                @if($selectedPayment && $selectedPayment->sale && $selectedPayment->sale->items && $selectedPayment->sale->items->count() > 0)
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
                                        @foreach ($selectedPayment->sale->items as $index => $item)
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
                                @else
                                <div class="text-center py-3 text-muted">
                                    <p>
                                        @if(!$selectedPayment)
                                        No payment selected.
                                        @elseif(!$selectedPayment->sale)
                                        This is a Brought-forward payment - no specific items associated with this payment.
                                        @elseif(!$selectedPayment->sale->items || $selectedPayment->sale->items->count() == 0)
                                        No items found for this sale.
                                        @else
                                        No items found for this payment.
                                        @endif
                                    </p>
                                </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted mb-2 fw-medium" style="color: #6B7280;">PAYMENT
                                        INFORMATION</h6>
                                    <div class="mb-2 p-2 border-start border-3 rounded-2"
                                        style="border-color: {{ $selectedPayment->is_completed ? '#0F5132' : '#664D03' }}; background-color: #F8F9FA;">
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>{{ $selectedPayment->is_completed ? 'Payment' : 'Scheduled Payment'
                                            }}:</strong>
                                            Rs.{{ number_format($selectedPayment->amount, 2) }}
                                        </p>
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>Method:</strong> {{ ucfirst(str_replace('_', ' ',
                                        $selectedPayment->payment_method)) }}
                                        </p>
                                        @if ($selectedPayment->payment_reference)
                                        <p class="mb-1" style="color: #9d1c20;">
                                            <strong>Reference:</strong> {{ $selectedPayment->payment_reference }}
                                        </p>
                                        @endif
                                        @if ($selectedPayment->payment_date)
                                        <p class="mb-0" style="color: #9d1c20;">
                                            <strong>Date:</strong> {{
                                        \Carbon\Carbon::parse($selectedPayment->payment_date)->format('d/m/Y') }}
                                        </p>
                                        @endif
                                        @if ($selectedPayment->due_date)
                                        <p class="mb-0" style="color: #9d1c20;">
                                            <strong>Due Date:</strong> {{
                                        \Carbon\Carbon::parse($selectedPayment->due_date)->format('d/m/Y') }}
                                        </p>
                                        @endif
                                    </div>

                                    @if ($selectedPayment->payment_reference)
                                    <h6 class="text-muted mt-3 mb-2 fw-medium" style="color: #6B7280;">NOTES</h6>
                                    <p class="font-italic" style="color: #6B7280;">{{ $selectedPayment->payment_reference }}</p>
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
                                                <span>Rs.{{ number_format($selectedPayment->sale->subtotal ?? 0, 2) }}</span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2"
                                                style="color: #9d1c20;">
                                                <span>Total Discount:</span>
                                                <span>Rs.{{ number_format($selectedPayment->sale->discount_amount ?? 0, 2) }}</span>
                                            </div>
                                            <hr style="border-color: #9d1c20;">
                                            <div class="d-flex justify-content-between" style="color: #9d1c20;">
                                                <span class="fw-bold">Grand Total:</span>
                                                <span class="fw-bold">Rs.{{ number_format($selectedPayment->sale->total_amount ?? 0, 2)
                                                }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-center mt-4 pt-3 border-top" style="border-color: #9d1c20;">
                                <p class="mb-0 text-muted small" style="color: #6B7280;">Thank you for your
                                    payment!</p>
                            </div>
                        </div>
                        @else
                        <div class="text-center p-5">
                            <p class="text-muted" style="color: #6B7280;">No payment data available</p>
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

        <div wire:ignore.self class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-fullscreen-md-down modal-xl">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header text-white p-4" style="background: linear-gradient(90deg, #9d1c20 0%, #9d1c20 100%);">
                        <h5 class="modal-title fw-bold tracking-tight" id="imageModalLabel">Payment Attachment</h5>
                        <div>
                            <a id="downloadImageLink" href="#" class="btn btn-sm btn-light me-2 rounded-full" download>
                                <i class="bi bi-download"></i> Download
                            </a>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body text-center p-0">
                        <div class="position-relative">
                            <img id="fullSizeImage" src="" class="img-fluid w-100" alt="Payment proof">
                            <div class="position-absolute top-0 end-0 p-3">
                                <button id="zoomInBtn" class="btn btn-sm btn-light rounded-circle me-1"><i class="bi bi-zoom-in"></i></button>
                                <button id="zoomOutBtn" class="btn btn-sm btn-light rounded-circle"><i class="bi bi-zoom-out"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('styles')
        @include('components.admin-styles')
        @endpush

        @push('scripts')
        <script>
            window.printReceiptContent = function() {
                const receiptContent = document.querySelector('#receiptContent')?.innerHTML || '';
                const printWindow = window.open('', '_blank', 'height=600,width=800');

                printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Payment Receipt - Print</title>
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
            };

            function openFullImage(imageUrl) {
                const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
                document.getElementById('fullSizeImage').src = imageUrl;
                const downloadLink = document.getElementById('downloadImageLink');
                downloadLink.href = imageUrl;
                downloadLink.download = 'payment-reference.jpg';
                imageModal.show();
            }

            document.addEventListener('livewire:initialized', () => {
                Livewire.on('openModal', (modalId) => {
                    const modalElement = document.getElementById(modalId);
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    }
                });

                // Handle delayed modal opening to ensure data is loaded
                Livewire.on('openPaymentModal', () => {
                    // Small delay to ensure Livewire has processed the property updates
                    setTimeout(() => {
                        const modalElement = document.getElementById('payment-receipt-modal');
                        if (modalElement) {
                            // Double-check that the payment data is actually loaded
                            if (window.Livewire && window.Livewire.find) {
                                try {
                                    const component = window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id'));
                                    if (component && component.selectedPayment) {
                                        const modal = new bootstrap.Modal(modalElement);
                                        modal.show();
                                    } else {
                                        // If data still not available, try again in 100ms
                                        setTimeout(() => {
                                            const modal = new bootstrap.Modal(modalElement);
                                            modal.show();
                                        }, 100);
                                    }
                                } catch (e) {
                                    // Fallback if component lookup fails
                                    const modal = new bootstrap.Modal(modalElement);
                                    modal.show();
                                }
                            } else {
                                const modal = new bootstrap.Modal(modalElement);
                                modal.show();
                            }
                        }
                    }, 50); // 50ms delay should be sufficient
                });

                Livewire.on('payment-data-loaded', () => {
                    // Force a re-render of the modal content to show the loaded data
                    setTimeout(() => {
                        // This ensures the modal content is updated with the loaded payment data
                        const modalBody = document.querySelector('#payment-receipt-modal .modal-body');
                        if (modalBody) {
                            modalBody.style.opacity = '0.7';
                            setTimeout(() => {
                                modalBody.style.opacity = '1';
                            }, 100);
                        }
                    }, 100);
                });

                Livewire.on('showToast', (event) => {
                    // Implement toast notification (e.g., using Toastr)
                    console.log('Toast:', event.type, event.message);
                    // Example: alert(event.message); // Replace with actual toast implementation
                });
            });
        </script>
        @endpush
    </div>