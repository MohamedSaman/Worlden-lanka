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
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-info bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-hourglass text-info"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Total Due Payments</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h3 class="mb-0 fw-bold text-gray-800">{{ $duePaymentsCount }}</h3>
                                                <span class="badge bg-info bg-opacity-10 text-info ms-2 rounded-full" style="padding: 6px 12px;">To Collect</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Due Amount Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-success bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-currency-dollar text-success"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Total Due Amount</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h4 class="mb-0 fw-bold text-gray-800">
                                                    Rs.{{ number_format($totalDue, 2) }}
                                                </h4>
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
                                        <div class="icon-shape icon-md rounded-circle bg-success bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-hourglass text-info"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Today Due Payment</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h4 class="mb-0 fw-bold text-gray-800">
                                                    {{ $todayDuePaymentsCount }}
                                                </h4>
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
                                        <div class="icon-shape icon-md rounded-circle bg-success bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-currency-dollar text-success"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Total Due Amount</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h4 class="mb-0 fw-bold text-gray-800">
                                                    Rs.{{ number_format($todayDuePayments, 2) }}
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
                                            <span class="text-sm fw-semibold text-gray-800">Rs.{{ number_format($payment->amount, 2) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center" data-label="Status">
                                        @if ($payment->status === null)
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-full px-3 py-1" style="font-size: 0.75rem;">Pending</span>

                                        @elseif($payment->status === 'Paid')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-full px-3 py-1" style="font-size: 0.75rem;">Paid</span>

                                        @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-full px-3 py-1" style="font-size: 0.75rem;">No show</span>
                                        @endif
                                    </td>
                                    <td class="text-center" data-label="Actions">
                                        @if ($payment->status === null)
                                        <button class="btn btn-primary text-white rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                            wire:click="getPaymentDetails({{ $payment->id }})">
                                            <i class="bi bi-currency-dollar me-1"></i> Receive
                                        </button>
                                        @else
                                        <button class="btn btn-success text-white rounded-full shadow-sm px-4 py-2 pe-none">
                                            <i class="bi bi-check-circle-fill me-1"></i> Paid
                                        </button>
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
                    @if ($duePayments->hasPages())
                    <div class="card-footer p-4 bg-white border-top rounded-b-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div class="text-sm text-gray-600">
                                Showing <span class="fw-semibold text-gray-800">{{ $duePayments->firstItem() }}</span> to <span class="fw-semibold text-gray-800">{{ $duePayments->lastItem() }}</span> of <span class="fw-semibold text-gray-800">{{ $duePayments->total() }}</span> results
                            </div>
                            <div class="pagination-container">
                                {{ $duePayments->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                    @endif
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
                                <p class="mb-2">
                                    <span class="text-gray-600">Invoice:</span>
                                    <span class="fw-bold text-gray-800 fs-6">{{ $paymentDetail->sale->invoice_number }}</span>
                                </p>
                                <p class="mb-2 d-flex justify-content-between text-sm">
                                    <span class="text-gray-600">Sale Date:</span>
                                    <span class="text-gray-800">{{ $paymentDetail->sale->created_at->format('d/m/Y') }}</span>
                                </p>

                                <div class="card border-0 shadow-sm rounded-4 p-3 mt-3 bg-light">
                                    <div>
                                        <span class="text-sm text-gray-600">Amount Due:</span>
                                        <span class="fw-bold" style="font-size: 1.5rem; color: #1e3a8a;">Rs.{{ number_format($paymentDetail->amount, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <div class="col-md-9 p-0">
                            <form wire:submit.prevent="submitPayment">
                                <div class="bg-light p-4 border-bottom rounded-top-end-4">
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

                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Cash Amount</label>
                                            <input type="text" class="form-control rounded-4 shadow-sm @error('receivedAmount') is-invalid @enderror" wire:model="receivedAmount" placeholder="Enter cash amount">
                                            @error('receivedAmount')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="border rounded-4 p-3 mb-4 shadow-sm bg-light">
                                        <h6 class="text-sm fw-semibold mb-3" style="color: #1e3a8a;">Add Cheque Details</h6>
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-6">
                                                <label class="form-label text-xs fw-semibold">Cheque No. <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm rounded-3 shadow-sm" wire:model="chequeNumber" placeholder="Cheque Number">
                                                @error('chequeNumber') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-xs fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm rounded-3 shadow-sm" wire:model="bankName" placeholder="Bank Name">
                                                @error('bankName') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-xs fw-semibold">Amount <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control form-control-sm rounded-3 shadow-sm" wire:model="chequeAmount" placeholder="Amount">
                                                @error('chequeAmount') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label text-xs fw-semibold">Cheque Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control form-control-sm rounded-3 shadow-sm" wire:model="chequeDate">
                                                @error('chequeDate') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" wire:click="addCheque" class="btn btn-primary btn-sm w-100 rounded-3 shadow-sm">
                                                    <i class="bi bi-plus-circle me-1"></i> Add
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="table-responsive mb-4">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="text-xs fw-semibold text-center">Cheque No.</th>
                                                    <th class="text-xs fw-semibold">Bank</th>
                                                    <th class="text-xs fw-semibold text-center">Date</th>
                                                    <th class="text-xs fw-semibold text-end">Amount</th>
                                                    <th class="text-xs fw-semibold text-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($cheques as $index => $cheque)
                                                <tr>
                                                    <td class="text-center">{{ $cheque['number'] }}</td>
                                                    <td>{{ $cheque['bank'] }}</td>
                                                    <td class="text-center">{{ \Carbon\Carbon::parse($cheque['date'])->format('d/m/Y') }}</td>
                                                    <td class="text-end">Rs.{{ number_format($cheque['amount'], 2) }}</td>
                                                    <td class="text-center">
                                                        <button type="button" wire:click="removeCheque({{ $index }})" class="btn btn-danger btn-sm p-0" style="width: 24px; height: 24px; line-height: 1;">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">No cheques added yet.</td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                            @if(!empty($cheques))
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-end fw-bold">Total Cheque Amount:</td>
                                                    <td class="text-end fw-bold">Rs.{{ number_format(collect($cheques)->sum('amount'), 2) }}</td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                            @endif
                                        </table>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Payment Notes</label>
                                            <textarea class="form-control rounded-4 shadow-sm" rows="3" wire:model="paymentNote" placeholder="Add any notes about this payment (optional)"></textarea>
                                        </div>
                                    </div>

                                    <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-4 d-flex align-items-center shadow-sm p-3 mb-4">
                                        <i class="bi bi-info-circle-fill text-info fs-5 me-3"></i>
                                        <div>
                                            <p class="mb-0 text-sm text-gray-800">This payment will be sent for admin approval.</p>
                                            <p class="mb-0 text-xs text-gray-600">The customer's account will be updated once approved.</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 bg-white border-top rounded-bottom-end-4">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105" data-bs-dismiss="modal">
                                            <i class="bi bi-x me-1"></i> Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary text-white rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105">
                                            <i class="bi bi-send me-1"></i> Submit
                                        </button>
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

        @this.on('showToast', ({ type, message }) => {
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
            const tableElement = document.querySelector('.table.table-hover');
            if (!tableElement || tableElement.querySelectorAll('tbody tr').length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Data to Print',
                    text: 'No due payments are available to print.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#1e40af',
                });
                return;
            }

            const clonedTable = tableElement.cloneNode(true);
            const actionColumnIndex = 4; // Corrected index for Actions column
            const headerRow = clonedTable.querySelector('thead tr');
            const headerCells = headerRow.querySelectorAll('th');
            if (headerCells.length > actionColumnIndex) {
                headerCells[actionColumnIndex].remove();
            }
            const rows = clonedTable.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > actionColumnIndex) {
                    cells[actionColumnIndex].remove();
                }
            });

            // Debug: Log the cloned table HTML
            console.log('Cloned Table HTML:', clonedTable.outerHTML);

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
                        body {
                            font-family: 'Inter', sans-serif;
                            padding: 20px;
                            font-size: 15px;
                            color: #1f2937;
                            background: #ffffff;
                        }
                        .print-container {
                            max-width: 900px;
                            margin: 0 auto;
                        }
                        .print-header {
                            margin-bottom: 20px;
                            padding-bottom: 15px;
                            border-bottom: 2px solid #1e40af;
                            text-align: center;
                            color: #1e40af;
                            font-weight: 700;
                            letter-spacing: -0.025em;
                        }
                        .print-footer {
                            margin-top: 20px;
                            padding-top: 15px;
                            border-top: 2px solid #e5e7eb;
                            text-align: center;
                            font-size: 12px;
                            color: #6b7280;
                        }
                        table {
                            width: 100%;
                            border-collapse: separate;
                            border-spacing: 0;
                        }
                        th, td {
                            border: 1px solid #e5e7eb;
                            padding: 12px;
                            vertical-align: middle;
                        }
                        th {
                            background-color: #eff6ff;
                            color: #1e3a8a;
                            text-transform: uppercase;
                            font-weight: 600;
                            font-size: 0.75rem;
                            text-align: center;
                        }
                        td {
                            text-align: center;
                        }
                        tr:nth-child(even) {
                            background-color: #f9fafb;
                        }
                        tr:hover {
                            background-color: #f1f5f9;
                        }
                        .invoice-cell {
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                        }
                        .customer-cell {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .customer-icon {
                            width: 2.5rem;
                            height: 2.5rem;
                            background-color: #1e40af;
                            color: #ffffff;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-right: 0.5rem;
                            font-weight: 700;
                        }
                        .amount-cell {
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .amount-icon {
                            width: 1.5rem;
                            height: 1.5rem;
                            background-color: #22c55e;
                            color: #ffffff;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            margin-right: 0.5rem;
                        }
                        .badge {
                            padding: 6px 12px;
                            border-radius: 9999px;
                            font-size: 0.875rem;
                            font-weight: 600;
                        }
                        .bg-info { background-color: #0ea5e9; color: #ffffff; }
                        .bg-success { background-color: #22c55e; color: #ffffff; }
                        .bg-danger { background-color: #ef4444; color: #ffffff; }
                        .bg-warning { background-color: #f59e0b; color: #ffffff; }
                        .bg-secondary { background-color: #6b7280; color: #ffffff; }
                        .btn-light {
                            border-color: #1e3a8a;
                            background-color:#1e3a8a;
                            color:#fff;
                            padding: 6px 15px;
                            border-radius: 6px;
                            text-decoration: none;
                            display: inline-block;
                        }
                        
                        .no-print { 
                            display: block; 
                            
                        }
                        @media print {
                            .no-print { display: none; }
                            thead { display: table-header-group; }
                            tr { page-break-inside: avoid; }
                            body { padding: 10px; }
                            .print-container { max-width: 100%; }
                            table { -webkit-print-color-adjust: exact; color-adjust: exact; }
                            .btn-light { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="print-container">
                        <div class="print-header">
                            <h2>Customer Due Payments</h2>
                        </div>
                        <div class="table-responsive">
                            ${clonedTable.outerHTML}
                        </div>
                        <div class="print-footer">
                            <small>Generated on ${new Date().toLocaleString('en-US', { timeZone: 'Asia/Colombo' })}</small><br>
                            <p>PLUS</p>
                            <small>Importers Of Garment Accessories & Machinery</small><br>
                            <small>20/2/1, 2nd FLOOR,HUNTER BUILDING,BANKSHALLL STREET,COLOMBO-11 | Phone: 011 - 2332786 | Email: plusaccessories.lk@gmail.com</small>
                            <div class="no-print" style="margin-top: 15px;">
                                <a href="#" class="btn-light" onclick="window.print(); return false;">Print</a>
                                <a href="#" class="btn-light" style="margin-left: 10px;" onclick="window.close(); return false;">Close</a>
                            </div>
                        </div>
                    </div>
                </body>
                </html>
            `;

            const printWindow = window.open('', '_blank', 'width=1000,height=700');
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