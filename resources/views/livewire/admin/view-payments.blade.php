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
                <div class="stat-card primary animate-fade-in">
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
                <div class="stat-card primary animate-fade-in">
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
                <div class="stat-card primary animate-fade-in">
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
                                        <th class="text-uppercase text-xs fw-semibold py-3 " style="color: #9d1c20;">Status</th>
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
                                            <span class="badge rounded-pill bg-secondary bg-opacity-10 text-black px-3 py-2">
                                                {{ ucfirst(str_replace('_', ' ', $method)) }}
                                            </span>
                                        </td>
                                        <td class="text-left">
                                            @php
                                            if ($payment->status == 'Paid' || $payment->status == 'paid') {
                                            $displayStatus = 'Paid';
                                            $statusClass = 'success';
                                            } elseif ($payment->status == 'forward') {
                                            $displayStatus = 'Brought Forward';
                                            $statusClass = 'info';
                                            } elseif ($payment->status == 'current'){
                                            $displayStatus = 'Current Due';
                                            $statusClass = 'info';
                                            } else{
                                                $displayStatus = 'Current and Forward';
                                                $statusClass = 'warning';
                                            }
                                            @endphp
                                            <span class="badge rounded-pill bg-{{ $statusClass }} bg-opacity-20 text-black px-3 py-2">
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
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content rounded-4 shadow-xl border-0">
                    <!-- Header -->
                    <div class="modal-header border-0 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #9d1c20 0%, #c92228 100%); padding: 2rem;">

                        <div class="position-relative z-1">
                            <h4 class="modal-title fw-bold mb-1" id="payment-receipt-modal-label">
                                <i class="bi bi-receipt me-2"></i>Payment Receipt
                            </h4>
                            <p class="mb-0 opacity-90 small">Transaction Details & Summary</p>
                        </div>
                        <div class="ms-auto d-flex gap-2 position-relative z-1">
                            <button type="button" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm" id="printButton" onclick="printReceiptContent()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" wire:click="$set('selectedPayment', null)"></button>
                        </div>
                    </div>

                    <!-- Body -->
                    <!-- Modal Body Content -->
                    <div class="modal-body p-0" id="receiptContent">
                        @if ($isLoadingPayment)
                        <!-- Loading State -->
                        <div class="text-center py-5">
                            <div class="spinner-border text-danger mb-3" role="status" style="width: 3rem; height: 3rem;">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <h5 class="text-muted fw-normal">Loading Receipt Details</h5>
                            <p class="text-sm text-muted mb-0">Please wait...</p>
                        </div>
                        @elseif ($selectedPayment)

                        <!-- Screen View - Modern Design -->
                        <div class="receipt-screen-view">
                            <div class="px-5">
                                
                                <!-- Payment Receipt Heading -->
                                <div class="text-center my-4">
                                    <h3 class="fw-bold mb-0" style="color: #9d1c20; letter-spacing: 2px;">PAYMENT RECEIPT</h3>
                                </div>

                                <!-- Customer Details (Left) and Receipt Details (Right) -->
                                <div class="row mb-4">
                                    <!-- Left: Customer Details -->
                                    <div class="col-md-6">
                                        <div class="card border-0 h-100" style="background-color: #f8f9fa;">
                                            <div class="card-body p-4">
                                                <h6 class="fw-bold mb-3 text-uppercase" style="color: #9d1c20;">
                                                    <i class="bi bi-person-circle me-2"></i>Customer Details
                                                </h6>
                                                @if($selectedPayment->sale && $selectedPayment->sale->customer)
                                                <div class="mb-3">
                                                    <p class="text-muted small mb-1">Customer Name</p>
                                                    <p class="fw-bold mb-0">{{ $selectedPayment->sale->customer->name ?? 'Guest Customer' }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <p class="text-muted small mb-1">Phone Number</p>
                                                    <p class="fw-bold mb-0">{{ $selectedPayment->sale->customer->phone ?? 'N/A' }}</p>
                                                </div>
                                                @if($selectedPayment->sale->customer->address)
                                                <div>
                                                    <p class="text-muted small mb-1">Address</p>
                                                    <p class="fw-bold mb-0">{{ $selectedPayment->sale->customer->address }}</p>
                                                </div>
                                                @endif
                                                @elseif($selectedPayment->customer)
                                                <div class="mb-3">
                                                    <p class="text-muted small mb-1">Customer Name</p>
                                                    <p class="fw-bold mb-0">{{ $selectedPayment->customer->name }}</p>
                                                </div>
                                                <div>
                                                    <p class="text-muted small mb-1">Phone Number</p>
                                                    <p class="fw-bold mb-0">{{ $selectedPayment->customer->phone ?? 'N/A' }}</p>
                                                </div>
                                                @else
                                                <p class="text-muted mb-0">No customer information available</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right: Receipt Details -->
                                    <div class="col-md-6">
                                        <div class="card border-0 h-100" style="background-color: #f8f9fa;">
                                            <div class="card-body p-4">
                                                <h6 class="fw-bold mb-3 text-uppercase" style="color: #9d1c20;">
                                                    <i class="bi bi-receipt me-2"></i>Receipt Details
                                                </h6>
                                                <div class="mb-3">
                                                    <p class="text-muted small mb-1">Receipt Number</p>
                                                    <p class="fw-bold mb-0" style="color: #9d1c20;">
                                                        @if($selectedPayment->sale)
                                                        {{ $selectedPayment->sale->invoice_number ?? 'N/A' }}
                                                        @else
                                                        BF-{{ str_pad($selectedPayment->id, 6, '0', STR_PAD_LEFT) }}
                                                        @endif
                                                    </p>
                                                </div>
                                                <div>
                                                    <p class="text-muted small mb-1">Payment Date</p>
                                                    <p class="fw-bold mb-0" style="color: #9d1c20;">
                                                        {{ $selectedPayment->payment_date ? \Carbon\Carbon::parse($selectedPayment->payment_date)->format('d M Y') : ($selectedPayment->created_at ? $selectedPayment->created_at->format('d M Y') : 'N/A') }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Amount - Center Highlighted -->
                                <div class="text-center mb-4 p-4 rounded-3" style="background: linear-gradient(135deg, #9d1c20 0%, #c92228 100%); box-shadow: 0 4px 12px rgba(157, 28, 32, 0.3);">
                                    <p class="text-white mb-2 text-uppercase small fw-semibold" style="opacity: 0.9;">Total Amount Paid</p>
                                    <h1 class="text-white fw-bold mb-0" style="font-size: 3.5rem; letter-spacing: 2px;">
                                        Rs. {{ number_format($selectedPayment->amount, 2) }}
                                    </h1>
                                </div>

                                <!-- Payment Method and Status -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body p-4 text-center">
                                                <i class="bi bi-credit-card text-danger fs-2 mb-3"></i>
                                                <p class="text-muted small mb-2 text-uppercase">Payment Method</p>
                                                <h5 class="mb-0 fw-bold" style="color: #9d1c20;">
                                                    {{ ucfirst(str_replace('_', ' ', $selectedPayment->payment_method)) }}
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card border-0 shadow-sm h-100">
                                            <div class="card-body p-4 text-center">
                                                @php
                                                if ($selectedPayment->status == 'Paid' || $selectedPayment->status == 'paid') {
                                                $displayStatus = 'Paid';
                                                $statusClass = 'success';
                                                $statusIcon = 'check-circle-fill';
                                                } elseif ($selectedPayment->status == 'forward') {
                                                $displayStatus = 'Brought Forward';
                                                $statusClass = 'success';
                                                $statusIcon = 'arrow-right-circle-fill';
                                                } elseif ($selectedPayment->status == 'current'){
                                                $displayStatus = 'Current Due Paid';
                                                $statusClass = 'success';
                                                $statusIcon = 'clock-fill';
                                                }else{
                                                    $displayStatus = 'Current And Forward Paid';
                                                    $statusClass = 'success';
                                                    $statusIcon = 'clock-fill';
                                                }
                                                @endphp
                                                <i class="bi bi-{{ $statusIcon }} text-{{ $statusClass }} fs-2 mb-3"></i>
                                                <p class="text-muted small mb-2 text-uppercase">Payment Status</p>
                                                <h5 class="mb-0">
                                                    <span class="badge bg-{{ $statusClass }} px-4 py-2 fs-6">{{ $displayStatus }}</span>
                                                </h5>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            

                                <!-- Footer -->
                                <div class="text-center pt-4 mt-4 border-top border-2" style="border-color: #9d1c20 !important;">
                                    <div class="mb-3">
                                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2" style="color: #9d1c20;">Thank You For Your Payment!</h4>
                                    <p class="text-muted mb-0">We appreciate your business and trust in us.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Print View - A4 Size Professional Receipt -->
                        <div class="receipt-print-view" style="display: none;">
                            <div style="font-family: 'Courier New', monospace; padding: 30px; max-width: 800px; margin: 0 auto;">

                                <!-- Company Header -->
                                <div style="text-align: center; margin-bottom: 15px; padding-bottom: 20px; border-bottom: 3px solid #000;">
                                    <h1 style="font-size: 32px; font-weight: bold; letter-spacing: 5px; margin-bottom: 15px; color: #000;">PLUS</h1>
                                    <div style="font-size: 13px; line-height: 1.5; color: #000; font-weight: bold;">
                                        <div>NO 20/2/1, 2nd FLOOR, HUNTER BUILDING</div>
                                        <div>BANKSHALLL STREET, COLOMBO-11</div>
                                        <div style="margin-top: 8px;">Tel: 011-2332786 | Email: plusaccessories.lk@gmail.com</div>
                                    </div>
                                </div>

                                <!-- Payment Receipt Heading -->
                                <div style="text-align: center; margin: 10px 0;">
                                    <h2 style="font-size: 20px; font-weight: bold; letter-spacing: 3px; color: #000;">PAYMENT RECEIPT</h2>
                                </div>

                                <!-- Customer and Receipt Details Side by Side -->
                                <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 50%; vertical-align: top; padding-right: 20px;">
                                            <div style="font-size: 14px; font-weight: bold; margin-bottom: 15px; text-decoration: underline; color: #000;">CUSTOMER DETAILS:</div>
                                            @if($selectedPayment->sale && $selectedPayment->sale->customer)
                                            <div style="font-size: 13px; line-height: 2; color: #000;">
                                                <div><strong>Name:</strong> {{ $selectedPayment->sale->customer->name ?? 'Guest Customer' }}</div>
                                                <div><strong>Phone:</strong> {{ $selectedPayment->sale->customer->phone ?? 'N/A' }}</div>
                                                @if($selectedPayment->sale->customer->address)
                                                <div><strong>Address:</strong> {{ $selectedPayment->sale->customer->address }}</div>
                                                @endif
                                            </div>
                                            @elseif($selectedPayment->customer)
                                            <div style="font-size: 13px; line-height: 2; color: #000;">
                                                <div><strong>Name:</strong> {{ $selectedPayment->customer->name }}</div>
                                                <div><strong>Phone:</strong> {{ $selectedPayment->customer->phone ?? 'N/A' }}</div>
                                            </div>
                                            @else
                                            <div style="font-size: 13px; color: #000;">No customer information</div>
                                            @endif
                                        </td>
                                        <td style="width: 50%; vertical-align: top; padding-left: 20px;">
                                            <div style="font-size: 14px; font-weight: bold; margin-bottom: 15px; text-decoration: underline; color: #000;">RECEIPT DETAILS:</div>
                                            <div style="font-size: 13px; line-height: 2; color: #000;">
                                                <div><strong>Receipt No:</strong>
                                                    @if($selectedPayment->sale)
                                                    {{ $selectedPayment->sale->invoice_number ?? 'N/A' }}
                                                    @else
                                                    BF-{{ str_pad($selectedPayment->id, 6, '0', STR_PAD_LEFT) }}
                                                    @endif
                                                </div>
                                                <div><strong>Date:</strong> {{ $selectedPayment->payment_date ? \Carbon\Carbon::parse($selectedPayment->payment_date)->format('d M Y') : ($selectedPayment->created_at ? $selectedPayment->created_at->format('d M Y') : 'N/A') }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Payment Amount - Highlighted Box -->
                                <div style="text-align: center; margin: 30px 0; padding: 25px; border: 3px double #000; background-color: #f5f5f5;">
                                    <div style="font-size: 14px; font-weight: bold; margin-bottom: 10px; letter-spacing: 2px; color: #000;">TOTAL AMOUNT PAID</div>
                                    <div style="font-size: 32px; font-weight: bold; letter-spacing: 3px; color: #000;">Rs. {{ number_format($selectedPayment->amount, 2) }}</div>
                                </div>

                                <!-- Payment Method and Status -->
                                <table style="width: 100%; margin-bottom: 10px; border-collapse: collapse;">
                                    <tr>
                                        <td style="width: 50%; padding: 15px; text-align: center; border: 2px solid #000; border-right: 1px solid #000;">
                                            <div style="font-size: 12px; font-weight: bold; margin-bottom: 8px; color: #000;">PAYMENT METHOD</div>
                                            <div style="font-size: 16px; font-weight: bold; color: #000;">{{ ucfirst(str_replace('_', ' ', $selectedPayment->payment_method)) }}</div>
                                        </td>
                                        <td style="width: 50%; padding: 15px; text-align: center; border: 2px solid #000; border-left: 1px solid #000;">
                                            <div style="font-size: 12px; font-weight: bold; margin-bottom: 8px; color: #000;">PAYMENT STATUS</div>
                                            <div style="font-size: 16px; font-weight: bold; color: #000;">{{ $displayStatus ?? 'N/A' }}</div>
                                        </td>
                                    </tr>
                                </table>


                                

                                <!-- Signature Section -->
                                <div style="margin-top: 50px;">
                                    <table style="width: 100%; font-size: 12px;">
                                        <tr>
                                            <td style="width: 50%; text-align: center;  padding-top: 10px;">
                                                <p>..............................</p>
                                                <strong>Customer Signature</strong>
                                            </td>
                                            <td style="width: 50%; text-align: center;  padding-top: 10px;">
                                                <p>..............................</p>
                                                <strong>Authorized Signature</strong>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <!-- Footer -->
                                <div style="text-align: center; margin-top: 40px; padding-top: 20px; border-top: 2px solid #000;">
                                    <div style="font-size: 18px; font-weight: bold; margin-bottom: 10px; color: #000;">THANK YOU FOR YOUR PAYMENT!</div>
                                    <div style="font-size: 12px; color: #000; line-height: 1.8;">
                                        <div>We appreciate your business and trust in us.</div>
                                        <div style="margin-top: 8px;">Please keep this receipt for your records.</div>
                                    </div>
                                </div>

                                
                            </div>
                        </div>

                        @else
                        <!-- No Data State -->
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-circle text-muted" style="font-size: 4rem;"></i>
                            <h5 class="text-muted mt-3">No payment data available</h5>
                        </div>
                        @endif
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer border-0 bg-light">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Close
                        </button>
                        <button type="button" class="btn rounded-pill px-4" style="background-color: #9d1c20; color: white;" onclick="printReceiptContent()">
                            <i class="bi bi-printer me-1"></i>Print Receipt
                        </button>
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
        <style>
            @media print {
                body * {
                    visibility: hidden;
                }

                .receipt-print-view,
                .receipt-print-view * {
                    visibility: visible;
                }

                .receipt-print-view {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    font-family: "Courier New", monospace !important;
                    font-size: 14px !important;
                    color: #000 !important;
                    font-weight: bold !important;
                }

                .modal-header,
                .modal-footer,
                .receipt-screen-view,
                .btn,
                .no-print {
                    display: none !important;
                }

                @page {
                    size: A4;
                    margin: 15mm;
                }
            }
        </style>
        @endpush

        @push('scripts')
        <script>
            window.printReceiptContent = function() {
                const printView = document.querySelector('.receipt-print-view')?.innerHTML || '';
                const printWindow = window.open('', '_blank', 'height=800,width=900');

                printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Payment Receipt - PLUS</title>
            <style>
                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }
                body {
                    font-family: "Courier New", monospace;
                    font-size: 14px;
                    color: #000 !important;
                    font-weight: bold !important;
                    padding: 20mm;
                }
                @page {
                    size: A4;
                    margin: 15mm;
                }
                @media print {
                    body {
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body>
            ${printView}
        </body>
        </html>
    `);

                printWindow.document.close();
                printWindow.focus();

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