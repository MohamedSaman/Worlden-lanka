<div class="container-fluid py-6 bg-gray-50 min-vh-100 transition-colors duration-300">
    <!-- Page Header with Stats (unchanged) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <!-- Header Content (unchanged) -->
                <div class="card-header text-white p-5 rounded-t-4 d-flex align-items-center"
                    style="background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%);">
                    <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-cash-stack text-white fs-4" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 fw-bold tracking-tight text-white">Cheque Payments Details</h3>
                        <p class="text-white opacity-80 mb-0 text-sm">Manage and collect pending cheque payments from customers</p>
                    </div>
                </div>

                <!-- Stats Cards (unchanged) -->
                <div class="card-body p-5">
                    <div class="row g-4">
                        <!-- Pending Cheques Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-info bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-hourglass text-info"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Pending Cheques</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h3 class="mb-0 fw-bold text-gray-800">{{ $pendingChequeCount }}</h3>
                                                <span class="badge bg-info bg-opacity-10 text-info ms-2 rounded-full" style="padding: 6px 12px;">To Collect</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Complete Cheque Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-success bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-clock-history text-success"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Complete Cheque</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h3 class="mb-0 fw-bold text-gray-800">{{ $completeChequeCount }}</h3>
                                                <span class="badge bg-success bg-opacity-10 text-success ms-2 rounded-full" style="padding: 6px 12px;">Complete</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Return Cheques Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md rounded-circle bg-danger bg-opacity-10 me-3 text-center">
                                            <i class="bi bi-exclamation-circle text-danger"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Return Cheques</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h3 class="mb-0 fw-bold text-gray-800">{{ $returnChequeCount }}</h3>
                                                <span class="badge bg-danger bg-opacity-10 text-danger ms-2 rounded-full" style="padding: 6px 12px;">Attention</span>
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
                                            <p class="text-xs text-gray-600 mb-0 text-uppercase fw-semibold">Total Cheque Due</p>
                                            <div class="d-flex align-items-baseline mt-1">
                                                <h4 class="mb-0 fw-bold text-gray-800">Rs.{{ number_format($totalDueAmount, 2) }}</h4>
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
                <!-- Search & Filter Bar (unchanged, but added print button wire:click if needed) -->
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
                            <button wire:click="printDueChequePayments"
                                class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                aria-label="Print due cheque payments">
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
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Customer</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Cheque Number</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Amount</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Cheque Date</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Status</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($duePayments as $cheque)
                                <tr>
                                    <td class="text-center fw-bold">{{ $cheque->payment->sale->invoice_number ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $cheque->customer->name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $cheque->cheque_number ?? 'N/A' }}</td>
                                    <td class="text-center">Rs. {{ number_format($cheque->cheque_amount ?? 0, 2) }}</td>
                                    <td class="text-center">{{ $cheque->cheque_date?->format('d/m/Y') ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <span class="px-3 py-1 fw-semibold {{ $cheque->status === 'pending' ? 'text-warning' : ($cheque->status === 'complete' ? 'text-success' : 'text-danger ') }}">
                                            {{ ucfirst($cheque->status ?? 'Pending') }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        @if($cheque->status == 'pending')
                                        <div class="d-flex justify-content-center gap-2">
                                            <button wire:click="completePaymentDetails({{ $cheque->id }})" class="btn btn-sm btn-success rounded-pill">Complete</button>
                                            <button wire:click="returnCheque({{ $cheque->id }})" class="btn btn-sm btn-danger rounded-pill">Return</button>
                                        </div>
                                        @elseif($cheque->status == 'return')
                                        <div class="d-flex justify-content-center gap-2">
                                            <button wire:click="refundCheque({{ $cheque->id }})" class="btn btn-sm btn-warning rounded-pill">Refund</button>
                                        </div>
                                        @else
                                        <span class="text-success">completed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-gray-600">No due cheque payments found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-top">
                        {{ $duePayments->links() }} <!-- Add pagination -->
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
                        <i class="bi bi-credit-card me-2"></i> Receive Cheque Payment
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
                                                    <h5 class="mb-0 fw-bold text-gray-800" style="color: #1e3a8a;">Cheque Payment Collection</h5>
                                                    <p class="text-sm text-gray-600 mb-0">Record cheque payment details for admin approval</p>
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
                                                <input type="text"
                                                    class="form-control border-start-0 ps-0 rounded-end-4"
                                                    value="Cheque"
                                                    readonly>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Payment Notes</label>
                                            <textarea class="form-control rounded-4 shadow-sm"
                                                rows="3"
                                                wire:model="paymentNote"
                                                placeholder="Add any notes about this cheque payment (optional)"></textarea>
                                            <div class="form-text text-sm text-gray-600">Include any specific details about this payment.</div>
                                        </div>
                                        <div class="alert alert-info bg-info bg-opacity-10 border-0 rounded-4 d-flex align-items-center shadow-sm p-3">
                                            <i class="bi bi-info-circle-fill text-info fs-5 me-3"></i>
                                            <div>
                                                <p class="mb-0 text-sm text-gray-800">This cheque payment will be sent for admin approval.</p>
                                                <p class="mb-0 text-xs text-gray-600">The customer's account will be updated once approved.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 p-4 bg-light border-start rounded-end-4">
                                        <div class="mb-4">
                                            <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Cheque Receipt/Document</label>
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
                                            <div class="form-text text-sm text-gray-600">Upload cheque image or payment proof.</div>
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
                                                    <p class="text-xs text-gray-600">Upload cheque image or payment proof</p>
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
                        <i class="bi bi-calendar-plus me-2"></i> Extend Cheque Due Date
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <form wire:submit.prevent="extendDueDate">
                        <div class="text-center mb-4">
                            <div class="icon-shape icon-xl bg-warning bg-opacity-10 rounded-circle mx-auto mb-3">
                                <i class="bi bi-calendar-week text-warning fs-2"></i>
                            </div>
                            <h5 class="fw-bold text-gray-800" style="color: #1e3a8a;">Extend Cheque Payment Due Date</h5>
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
            if (modal) {
                modal.hide();
            }
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

        // Print due cheque payments table
        Livewire.on('print-due-cheque-payments', function() {
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
                    <title>Due Cheque Payments - Print Report</title>
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
                            <h2 class="fw-bold tracking-tight">Due Cheque Payments</h2>
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