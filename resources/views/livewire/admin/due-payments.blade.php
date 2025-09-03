<div class="container-fluid py-6 bg-gray-50 min-vh-100 transition-colors duration-300">
    <!-- Page Header with Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <!-- Header Content -->
                <div class="card-header text-white p-5 rounded-t-4 d-flex align-items-center"
                    style="background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%);">
                    <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-cash-stack text-white fs-4" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 fw-bold tracking-tight text-white">Customer Due Payments</h3>
                        <p class="text-white opacity-80 mb-0 text-sm">Manage and collect pending payments from customers</p>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="card-body p-5">
                    <div class="row g-4">
                        <!-- Pending Payments Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-info bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-hourglass text-info"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Pending Payment</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h3 class="mb-0 fw-bold text-gray-800">{{ $duePayments->where('status', null)->count() }}</h3>
                                                <span class="badge bg-info bg-opacity-10 text-info ms-2 rounded-full" style="padding: 6px 12px;">To Collect</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Awaiting Approval Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-warning bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-clock-history text-warning"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Awaiting Approval</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h3 class="mb-0 fw-bold text-gray-800">{{ $duePayments->where('status', 'pending')->count() }}</h3>
                                                <span class="badge bg-warning bg-opacity-10 text-warning ms-2 rounded-full" style="padding: 6px 12px;">In Review</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Overdue Payments Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-danger bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-exclamation-circle text-danger"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Overdue</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h3 class="mb-0 fw-bold text-gray-800">
                                                    {{ $duePayments->where('status', null)->filter(function($payment) { return now()->gt($payment->due_date); })->count() }}
                                                </h3>
                                                <span class="badge bg-danger bg-opacity-10 text-danger ms-2 rounded-full" style="padding: 6px 12px;">Attention</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Due Amount Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-success bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-currency-dollar text-success"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Total Due</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h4 class="mb-0 fw-bold text-gray-800">
                                                    Rs.{{ number_format($duePayments->where('status', null)->sum('amount'), 2) }}
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
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <!-- Search & Filter Bar -->
                <div class="card-header p-4" style="background-color: #eff6ff;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="input-group shadow-sm rounded-full overflow-hidden" style="max-width: 400px;">
                            <span class="input-group-text bg-white border-0">
                                <i class="bi bi-search text-blue-600" aria-hidden="true"></i>
                            </span>
                            <input type="text"
                                class="form-control border-0 py-2.5 bg-white text-gray-800"
                                placeholder="Search invoices or customers..."
                                wire:model.live.debounce.300ms="search"
                                autocomplete="off"
                                aria-label="Search invoices or customers">
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="dropdown">
                                <button class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                    type="button" id="filterDropdown" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <i class="bi bi-funnel me-1"></i> Filters
                                    @if ($filters['status'] || $filters['dateRange'])
                                    <span class="badge bg-primary ms-1 rounded-full" style="background-color: #1e40af; color: #ffffff;">!</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu p-4 shadow-lg border-0 rounded-4" style="width: 300px;"
                                    aria-labelledby="filterDropdown">
                                    <h6 class="dropdown-header bg-light rounded py-2 mb-3 text-center text-sm fw-semibold" style="color: #1e3a8a;">Filter Options</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-sm fw-semibold" style="color: #1e3a8a;">Payment Status</label>
                                        <select class="form-select form-select-sm rounded-full shadow-sm"
                                            wire:model.live="filters.status">
                                            <option value="">All Statuses</option>
                                            <option value="null">Pending Payment</option>
                                            <option value="pending">Pending Approval</option>
                                            <option value="approved">Approved</option>
                                            <option value="rejected">Rejected</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-sm fw-semibold" style="color: #1e3a8a;">Due Date Range</label>
                                        <input type="text"
                                            class="form-control form-control-sm rounded-full shadow-sm"
                                            placeholder="Select date range"
                                            wire:model.live="filters.dateRange">
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                            wire:click="resetFilters">
                                            <i class="bi bi-x-circle me-1"></i> Reset Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button wire:click="printDuePayments"
                                class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                aria-label="Print due payments">
                                <i class="bi bi-printer me-1" aria-hidden="true"></i> Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="card-body p-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #eff6ff;">
                                <tr>
                                    <th class="ps-4 text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Invoice</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3" style="color: #1e3a8a;">Customer</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Amount</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Due Date</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Status</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($duePayments as $payment)
                                <tr class="border-bottom transition-all hover:bg-[#f1f5f9] {{ $loop->iteration % 2 == 0 ? 'bg-[#f9fafb]' : '' }} {{ now()->gt($payment->due_date) && $payment->status === null ? 'bg-danger bg-opacity-10' : '' }}">
                                    <td class="ps-4" data-label="Invoice">
                                        <div class="d-flex flex-column">
                                            <h6 class="mb-0 text-sm fw-semibold text-gray-800">{{ $payment->sale->invoice_number }}</h6>
                                            <p class="text-xs text-gray-600 mb-0">{{ $payment->sale->created_at->format('d M Y') }}</p>
                                        </div>
                                    </td>
                                    <td data-label="Customer">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-shape icon-md rounded-circle bg-primary bg-opacity-10 me-2 d-flex align-items-center justify-content-center">
                                                <span class="text-primary fw-bold">{{ substr($payment->sale->customer->name, 0, 1) }}</span>
                                            </div>
                                            <div>
                                                <p class="text-sm fw-semibold text-gray-800 mb-0">{{ $payment->sale->customer->name }}</p>
                                                <p class="text-xs text-gray-600 mb-0">{{ $payment->sale->customer->phone }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center" data-label="Amount">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="icon-shape icon-xs rounded-circle bg-success bg-opacity-10 me-2 text-center">
                                                <i class="bi bi-currency-dollar text-success"></i>
                                            </div>
                                            <span class="text-sm fw-semibold text-gray-800">Rs.{{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center" data-label="Due Date">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div class="icon-shape icon-xs rounded-circle 
                                                {{ now()->gt($payment->due_date) || now()->diffInDays($payment->due_date) <= 3 ? 'bg-danger bg-opacity-10' : 'bg-info bg-opacity-10' }} 
                                                me-2 text-center text-white">
                                                <i class="bi bi-calendar-date {{ now()->gt($payment->due_date) || now()->diffInDays($payment->due_date) <= 3 ? 'text-danger' : 'text-info' }}"></i>
                                            </div>
                                            <span class="text-sm {{ now()->gt($payment->due_date) || now()->diffInDays($payment->due_date) <= 3 ? 'text-danger fw-bold' : 'text-gray-800' }}">
                                                {{ $payment->due_date ? $payment->due_date->format('d M Y') : 'N/A' }}
                                            </span>
                                        </div>
                                        @if (now()->gt($payment->due_date))
                                        <span class="badge  text-xs mt-1" style="background-color: #ef4444; color: #ffffff; padding: 6px 12px; border-radius: 9999px;">
                                            {{ now()->diffForHumans($payment->due_date, ['parts' => 1]) }} overdue
                                        </span>
                                        @elseif(now()->diffInDays($payment->due_date) <= 3)
                                            <span class="badge  text-xs mt-1" style="background-color: #ef4444; color: #ffffff; padding: 6px 12px; border-radius: 9999px;">
                                            Due in {{ now()->diffForHumans($payment->due_date, ['parts' => 1]) }}
                                            </span>
                                            @else
                                            <span class="badge text-xs mt-1" style="background-color: #0ea5e9; color: #ffffff; padding: 6px 12px; border-radius: 9999px;">
                                                Due in {{ now()->diffForHumans($payment->due_date, ['parts' => 1]) }}
                                            </span>
                                            @endif
                                    </td>
                                    <td class="text-center" data-label="Status">
                                        {!! $payment->status_badge !!}
                                    </td>
                                    <td class="text-center" data-label="Actions">
                                        @if ($payment->status === null)
                                        <div class="btn-group">
                                            <button class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                                wire:click="getPaymentDetails({{ $payment->id }})">
                                                <i class="bi bi-currency-dollar me-1"></i> Receive
                                            </button>
                                            <button class="btn btn-light rounded-full shadow-sm px-4 py-2 ms-2 transition-transform hover:scale-105"
                                                wire:click="openExtendDueModal({{ $payment->id }})">
                                                <i class="bi bi-calendar-plus me-1"></i> Extend Due
                                            </button>
                                        </div>
                                        @elseif($payment->status === 'pending')
                                        <span class="btn btn-light rounded-full shadow-sm px-4 py-2 disabled opacity-75">
                                            <i class="bi bi-hourglass-split me-1"></i> Awaiting
                                        </span>
                                        @elseif($payment->status === 'rejected')
                                        <button class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                            wire:click="getPaymentDetails({{ $payment->id }})">
                                            <i class="bi bi-arrow-repeat me-1"></i> Resubmit
                                        </button>
                                        @else
                                        <span class="btn btn-light rounded-full shadow-sm px-4 py-2 disabled opacity-75">
                                            <i class="bi bi-check-circle me-1"></i> Complete
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-6">
                                        <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                                            <i class="bi bi-cash-coin text-gray-600 fs-3"></i>
                                        </div>
                                        <h5 class="text-gray-600 fw-normal">No Due Payments Found</h5>
                                        <p class="text-sm text-gray-500 mb-0">All customer payments are completed or no matching results found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-top">
                        {{ $duePayments->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Detail Modal -->
    <div wire:ignore.self class="modal fade" id="payment-detail-modal" tabindex="-1" aria-labelledby="payment-detail-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header text-white p-4"
                    style="background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%);">
                    <h5 class="modal-title fw-bold tracking-tight" id="payment-detail-modal-label">
                        <i class="bi bi-credit-card me-2"></i> Receive Due Payment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    @if ($paymentDetail)
                    <div class="row g-0">
                        <!-- Invoice Overview -->
                        <div class="col-md-3 bg-light p-4 border-end rounded-start-4">
                            <div class="text-center mb-4">
                                <div class="icon-shape icon-xl rounded-circle bg-primary bg-opacity-10 mx-auto d-flex align-items-center justify-content-center">
                                    <span class="text-primary fw-bold" style="font-size: 2rem;">{{ substr($paymentDetail->sale->customer->name, 0, 1) }}</span>
                                </div>
                                <h6 class="mt-3 mb-0 fw-bold text-gray-800">{{ $paymentDetail->sale->customer->name }}</h6>
                                <p class="text-sm text-gray-600 mb-0">{{ $paymentDetail->sale->customer->phone }}</p>
                            </div>
                            <h6 class="text-uppercase text-sm fw-semibold mb-3 border-bottom pb-2" style="color: #1e3a8a;">Invoice Details</h6>
                            <div class="mb-3">
                                <p class="mb-2 d-flex justify-content-between text-sm">
                                    <span class="text-gray-600">Invoice:</span>
                                    <span class="fw-bold text-gray-800">{{ $paymentDetail->sale->invoice_number }}</span>
                                </p>
                                <p class="mb-2 d-flex justify-content-between text-sm">
                                    <span class="text-gray-600">Sale Date:</span>
                                    <span class="text-gray-800">{{ $paymentDetail->sale->created_at->format('d/m/Y') }}</span>
                                </p>
                                <p class="mb-2 d-flex justify-content-between text-sm">
                                    <span class="text-gray-600">Due Date:</span>
                                    <span class="{{ now()->gt($paymentDetail->due_date) ? 'text-danger fw-bold' : 'text-gray-800' }}">
                                        {{ $paymentDetail->due_date->format('d/m/Y') }}
                                    </span>
                                </p>
                                <div class="card border-0 shadow-sm rounded-4 p-3 mt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-sm text-gray-600">Amount Due:</span>
                                        <span class="fw-bold text-primary" style="font-size: 1.5rem; color: #1e3a8a;">Rs.{{ number_format($paymentDetail->amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            @if (strpos($paymentDetail->sale->notes ?? '', 'Due date extended') !== false)
                            <div class="alert alert-warning bg-warning bg-opacity-10 border-0 rounded-4 mt-3 p-3 text-sm">
                                <div class="d-flex">
                                    <div class="me-2">
                                        <i class="bi bi-exclamation-triangle-fill text-warning"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-bold">Due date has been extended</p>
                                        @php
                                        $notes = explode("\n", $paymentDetail->sale->notes);
                                        $extensionNotes = array_filter($notes, function ($note) {
                                        return strpos($note, 'Due date extended') !== false;
                                        });
                                        @endphp
                                        @foreach ($extensionNotes as $note)
                                        <p class="mb-0 text-xs text-gray-600">{{ $note }}</p>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Payment Form -->
                        <div class="col-md-9">
                            <form wire:submit.prevent="submitPayment">
                                <div class="row g-0">
                                    <div class="col-lg-12">
                                        <div class="bg-light p-4 border-bottom rounded-top-4">
                                            <div class="d-flex align-items-center">
                                                <div class="icon-shape icon-md rounded-circle bg-white bg-opacity-25 p-2 me-3">
                                                    <i class="bi bi-wallet2 text-primary fs-4"></i>
                                                </div>
                                                <div>
                                                    <h5 class="mb-0 fw-bold text-gray-800" style="color: #1e3a8a;">Payment Collection</h5>
                                                    <p class="text-sm text-gray-600 mb-0">Record customer payment details for admin approval</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 p-4">
                                        <div class="mb-4">
                                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Received Amount <span class="text-danger">*</span></label>
                                            <input type="text"
                                                class="form-control rounded-4 shadow-sm @error('receivedAmount') is-invalid @enderror"
                                                wire:model="receivedAmount"
                                                required>
                                            @error('receivedAmount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <label class="form-label text-sm fw-semibold mb-2 mt-3" style="color: #1e3a8a;">Payment Method <span class="text-danger">*</span></label>
                                            <div class="input-group shadow-sm rounded-4">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-credit-card text-primary"></i>
                                                </span>
                                                <select class="form-select border-start-0 ps-0 rounded-end-4 @error('duePaymentMethod') is-invalid @enderror"
                                                    wire:model="duePaymentMethod"
                                                    required>
                                                    <option value="">-- Select payment method --</option>
                                                    <option value="cash">Cash</option>
                                                    <option value="cheque">Cheque</option>
                                                    <option value="bank_transfer">Bank Transfer</option>
                                                    <option value="credit_card">Credit Card</option>
                                                    <option value="debit_card">Debit Card</option>
                                                </select>
                                                @error('duePaymentMethod')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Payment Notes</label>
                                            <textarea class="form-control rounded-4 shadow-sm"
                                                rows="3"
                                                wire:model="paymentNote"
                                                placeholder="Add any notes about this payment (optional)"></textarea>
                                            <div class="form-text text-sm text-gray-600">Include any specific details about this payment.</div>
                                        </div>
                                        <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-4 d-flex align-items-center shadow-sm p-3">
                                            <i class="bi bi-info-circle-fill text-info fs-5 me-3"></i>
                                            <div>
                                                <p class="mb-0 text-sm text-gray-800">This payment will be sent for admin approval.</p>
                                                <p class="mb-0 text-xs text-gray-600">The customer's account will be updated once approved.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 p-4 bg-light border-start rounded-end-4">
                                        <div class="mb-4">
                                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Payment Receipt/Document</label>
                                            <div class="input-group shadow-sm rounded-4 mb-2">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-file-earmark-image text-primary"></i>
                                                </span>
                                                <input type="file"
                                                    class="form-control border-start-0 ps-0 rounded-end-4 @error('duePaymentAttachment') is-invalid @enderror"
                                                    wire:model="duePaymentAttachment">
                                                @error('duePaymentAttachment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="form-text text-sm text-gray-600">Upload receipt, cheque image, or other payment proof.</div>
                                        </div>
                                        <div class="card border-0 shadow-sm rounded-4 bg-white">
                                            <div class="card-header p-3" style="background-color: #eff6ff;">
                                                <div class="d-flex align-items-center">
                                                    <i class="bi bi-image text-primary me-2"></i>
                                                    <span class="text-sm fw-semibold" style="color: #1e3a8a;">Document Preview</span>
                                                </div>
                                            </div>
                                            <div class="card-body p-0 text-center">
                                                @if ($duePaymentAttachment)
                                                <div class="position-relative">
                                                    @if(is_array($duePaymentAttachmentPreview))
                                                    @if($duePaymentAttachmentPreview['type'] === 'pdf')
                                                    <div class="d-flex flex-column align-items-center p-4">
                                                        <i class="bi bi-file-earmark-pdf text-danger fs-1 mb-2"></i>
                                                        <span class="text-sm text-gray-600">PDF document</span>
                                                        <span class="text-xs text-gray-600">{{ $duePaymentAttachment->getClientOriginalName() }}</span>
                                                    </div>
                                                    @elseif($duePaymentAttachmentPreview['type'] === 'image' && !empty($duePaymentAttachmentPreview['preview']))
                                                    <img src="{{ $duePaymentAttachmentPreview['preview'] }}"
                                                        class="img-fluid"
                                                        style="max-height: 200px;">
                                                    @else
                                                    <div class="d-flex flex-column align-items-center p-4">
                                                        <i class="bi {{ $duePaymentAttachmentPreview['icon'] ?? 'bi-file-earmark' }} {{ $duePaymentAttachmentPreview['color'] ?? 'text-gray-600' }} fs-1 mb-2"></i>
                                                        <span class="text-sm text-gray-600">File attached</span>
                                                        <span class="text-xs text-gray-600">{{ $duePaymentAttachment->getClientOriginalName() }}</span>
                                                    </div>
                                                    @endif
                                                    @else
                                                    <div class="d-flex flex-column align-items-center p-4">
                                                        <i class="bi bi-file-earmark text-gray-600 fs-1 mb-2"></i>
                                                        <span class="text-sm text-gray-600">File attached</span>
                                                        <span class="text-xs text-gray-600">{{ $duePaymentAttachment->getClientOriginalName() }}</span>
                                                    </div>
                                                    @endif
                                                    <div class="position-absolute bottom-0 start-0 end-0 py-2 px-3 bg-dark bg-opacity-50 text-white text-start text-sm">
                                                        <i class="bi bi-check-circle-fill text-success me-1"></i> New attachment preview
                                                    </div>
                                                </div>
                                                @elseif($paymentDetail && $paymentDetail->due_payment_attachment)
                                                <div class="position-relative">
                                                    @php
                                                    $attachment = is_array($paymentDetail->due_payment_attachment)
                                                    ? ($paymentDetail->due_payment_attachment[0] ?? '')
                                                    : $paymentDetail->due_payment_attachment;
                                                    @endphp
                                                    @if(pathinfo($attachment, PATHINFO_EXTENSION) === 'pdf')
                                                    <div class="d-flex flex-column align-items-center p-4">
                                                        <i class="bi bi-file-earmark-pdf text-danger fs-1 mb-2"></i>
                                                        <a href="{{ asset('public/storage/' . $attachment) }}"
                                                            target="_blank"
                                                            class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105">
                                                            <i class="bi bi-eye me-1"></i> View PDF
                                                        </a>
                                                    </div>
                                                    @else
                                                    <img src="{{ asset('public/storage/' . $attachment) }}"
                                                        class="img-fluid"
                                                        style="max-height: 200px;"
                                                        onerror="this.onerror=null; this.src=''; this.parentNode.innerHTML='<div class=\'d-flex flex-column align-items-center p-4\'><i class=\'bi bi-file-earmark-image text-primary fs-1 mb-2\'></i><span class=\'text-sm text-gray-600\'>Image (cannot display preview)</span></div>';">
                                                    @endif
                                                    <div class="position-absolute bottom-0 start-0 end-0 py-2 px-3 bg-dark bg-opacity-50 text-white text-start text-sm">
                                                        <i class="bi bi-exclamation-circle-fill text-warning me-1"></i> Existing attachment
                                                    </div>
                                                </div>
                                                @else
                                                <div class="p-5 d-flex flex-column align-items-center">
                                                    <div class="icon-shape icon-md bg-light rounded-circle mb-3">
                                                        <i class="bi bi-file-earmark-plus fs-4 text-gray-600"></i>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mb-0">No document attached</p>
                                                    <p class="text-xs text-gray-600">Upload receipt or payment proof</p>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 p-4 bg-white border-top">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button"
                                                class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                                data-bs-dismiss="modal">
                                                <i class="bi bi-x me-1"></i> Cancel
                                            </button>
                                            <button type="submit"
                                                class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105">
                                                <i class="bi bi-send me-1"></i> Submit for Approval
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                            <i class="bi bi-credit-card text-gray-600 fs-3"></i>
                        </div>
                        <h5 class="text-gray-600 fw-normal">Loading Payment Details</h5>
                        <p class="text-sm text-gray-500 mb-0">Please wait while data is being loaded...</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Extend Due Date Modal -->
    <div wire:ignore.self class="modal fade" id="extend-due-modal" tabindex="-1" aria-labelledby="extend-due-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header text-white p-4"
                    style="background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%);">
                    <h5 class="modal-title fw-bold tracking-tight" id="extend-due-modal-label">
                        <i class="bi bi-calendar-plus me-2"></i> Extend Due Date
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <form wire:submit.prevent="extendDueDate">
                        <div class="text-center mb-4">
                            <div class="icon-shape icon-xl bg-warning bg-opacity-10 rounded-circle mx-auto mb-3">
                                <i class="bi bi-calendar-week text-warning fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-gray-800" style="color: #1e3a8a;">Extend Payment Due Date</h5>
                            <p class="text-sm text-gray-600">Provide a new due date and reason for extension</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">New Due Date <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm rounded-4">
                                <span class="input-group-text bg-white border-end-0">
                                    <i class="bi bi-calendar-date text-primary"></i>
                                </span>
                                <input type="date"
                                    class="form-control border-start-0 ps-0 rounded-end-4 @error('newDueDate') is-invalid @enderror"
                                    wire:model="newDueDate"
                                    min="{{ date('Y-m-d') }}">
                                @error('newDueDate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Reason for Extension <span class="text-danger">*</span></label>
                            <textarea class="form-control rounded-4 shadow-sm @error('extensionReason') is-invalid @enderror"
                                wire:model="extensionReason"
                                rows="3"
                                placeholder="Explain why the due date needs to be extended..."></textarea>
                            @error('extensionReason')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text text-sm text-gray-600">This information will be added to the sale notes.</div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <button type="button"
                                class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                data-bs-dismiss="modal">
                                <i class="bi bi-x me-1"></i> Cancel
                            </button>
                            <button type="submit"
                                class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105">
                                <i class="bi bi-check2-circle me-1"></i> Confirm Extension
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    body {
        font-family: 'Inter', sans-serif;
        font-size: 15px;
        color: #1f2937;
    }

    .tracking-tight {
        letter-spacing: -0.025em;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .transition-transform {
        transition: transform 0.2s ease;
    }

    .hover\:scale-105:hover {
        transform: scale(1.05);
    }

    .hover\:scale-110:hover {
        transform: scale(1.1);
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

    .icon-shape.icon-xl {
        width: 4.5rem;
        height: 4.5rem;
    }

    .icon-shape.icon-md {
        width: 2.5rem;
        height: 2.5rem;
    }

    .icon-shape.icon-xs {
        width: 1.5rem;
        height: 1.5rem;
    }

    .table {
        border-collapse: separate;
        border-spacing: 0;
    }

    .table th,
    .table td {
        border: 1px solid #e5e7eb;
        vertical-align: middle;
    }

    .table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }

    .table tbody tr:hover {
        background-color: #f1f5f9;
    }

    .rounded-full {
        border-radius: 9999px;
    }

    .rounded-4 {
        border-radius: 1rem;
    }

    .shadow-lg {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .shadow-sm {
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }

    .btn-light {
        background-color: #ffffff;
        border-color: #ffffff;
        color: #1e3a8a;
    }

    .btn-light:hover {
        background-color: #f1f5f9;
        border-color: #f1f5f9;
        color: #1e3a8a;
    }

    .bg-primary {
        background-color: #1e40af;
    }

    .bg-info {
        background-color: #0ea5e9;
    }

    .bg-success {
        background-color: #22c55e;
    }

    .bg-danger {
        background-color: #ef4444;
    }

    .bg-warning {
        background-color: #f59e0b;
    }

    .alert-info,
    .alert-warning {
        background-color: rgba(0, 0, 0, 0.05);
    }

    .form-control,
    .form-select {
        border-radius: 1rem;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #1e40af;
        box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        @this.on('openModal', (modalId) => {
            let modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        });

        @this.on('closeModal', (modalId) => {
            let modalElement = document.getElementById(modalId);
            let modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
        });

        @this.on('showToast', ({
            type,
            message
        }) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1f2937',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        });

        // Print due payments table
        Livewire.on('print-due-payments', function() {
            const printWindow = window.open('', '_blank', 'width=1000,height=700');
            const tableElement = document.querySelector('.table.table-hover').cloneNode(true);
            const actionColumnIndex = 5;
            const headerRow = tableElement.querySelector('thead tr');
            const headerCells = headerRow.querySelectorAll('th');
            headerCells[actionColumnIndex].remove();
            const rows = tableElement.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > actionColumnIndex) {
                    cells[actionColumnIndex].remove();
                }
            });

            const htmlContent = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Customer Due Payments - Print Report</title>
                    <meta charset="UTF-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1.0">
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
                    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
                    <style>
                        @page { size: landscape; margin: 1cm; }
                        body { font-family: 'Inter', sans-serif; padding: 20px; font-size: 14px; color: #1f2937; }
                        .print-container { max-width: 900px; margin: 0 auto; }
                        .print-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #1e40af; display: flex; justify-content: space-between; align-items: center; }
                        .print-header h2 { color: #1e40af; font-weight: 700; letter-spacing: -0.025em; }
                        .print-footer { margin-top: 20px; padding-top: 15px; border-top: 2px solid #e5e7eb; text-align: center; font-size: 12px; color: #6b7280; }
                        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                        th, td { border: 1px solid #e5e7eb; padding: 12px; text-align: center; vertical-align: middle; }
                        th { background-color: #eff6ff; font-weight: 600; text-transform: uppercase; color: #1e3a8a; }
                        tr:nth-child(even) { background-color: #f9fafb; }
                        tr:hover { background-color: #f1f5f9; }
                        .badge { padding: 6px 12px; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; color: #ffffff; }
                        .bg-primary { background-color: #1e40af; }
                        .bg-info { background-color: #0ea5e9; }
                        .bg-success { background-color: #22c55e; }
                        .bg-danger { background-color: #ef4444; }
                        .bg-warning { background-color: #f59e0b; }
                        .no-print { display: none; }
                        @media print {
                            .no-print { display: none; }
                            thead { display: table-header-group; }
                            tr { page-break-inside: avoid; }
                            body { padding: 10px; }
                            .print-container { max-width: 100%; }
                            table { -webkit-print-color-adjust: exact; color-adjust: exact; }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-container">
                        <div class="print-header">
                            <h2 class="fw-bold tracking-tight">Customer Due Payments</h2>
                            <div class="no-print">
                                <button class="btn btn-light rounded-full px-4" style="background-color:#ffffff;border-color:#ffffff;color:#1e3a8a;" onclick="window.print();">Print</button>
                                <button class="btn btn-light rounded-full px-4 ms-2" style="background-color:#ffffff;border-color:#ffffff;color:#1e3a8a;" onclick="window.close();">Close</button>
                            </div>
                        </div>
                        <div class="table-responsive">
                            ${tableElement.outerHTML}
                        </div>
                        <div class="print-footer">
                            <small>Generated on ${new Date().toLocaleString('en-US', { timeZone: 'Asia/Colombo' })}</small><br>
                            <small>NEW WATCH COMPANY (MR TRADING) | NO 44, DOOLMALA, THIHARIYA | Phone: (033) 228 7437</small>
                        </div>
                    </div>
                </body>
                </html>
            `;

            printWindow.document.open();
            printWindow.document.write(htmlContent);
            printWindow.document.close();
            printWindow.onload = function() {
                printWindow.focus();
            };
        });
    });
</script>
@endpush