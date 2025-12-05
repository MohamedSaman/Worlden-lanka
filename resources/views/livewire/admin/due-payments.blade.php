<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-header-modern mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Customer Due Payments</h3>
                        <p class="mb-0">Manage and collect pending payments from customers</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card primary animate-fade-in">

                <div class="stat-label">Total Current Due Payments</div>
                <div class="stat-value">{{ number_format($duePaymentsCount) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card warning animate-fade-in" style="animation-delay: 0.1s;">

                <div class="stat-label">Total Due Amount</div>
                <div class="stat-value text-warning">Rs.{{ number_format($totalDue, 2) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card success animate-fade-in" style="animation-delay: 0.2s;">

                <div class="stat-label">Today's Due Payments</div>
                <div class="stat-value">{{ number_format($todayDuePaymentsCount) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card info animate-fade-in" style="animation-delay: 0.3s;">

                <div class="stat-label">Today's Due Amount</div>
                <div class="stat-value text-info">Rs.{{ number_format($todayDuePayments, 2) }}</div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            <div class="card animate-slide-in">
                <!-- Search & Filter Bar -->
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
                                        placeholder="Search customers..."
                                        wire:model.live.debounce.300ms="search"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 text-lg-end">
                            <div class="dropdown d-inline-block me-2">
                                <button class="btn-modern btn-secondary-modern" type="button"
                                    id="filterDropdown" data-bs-toggle="dropdown">
                                    <i class="bi bi-funnel"></i> Filters
                                    @if ($filters['status'] || $filters['dateFrom'] || $filters['dateTo'])
                                    <span class="badge bg-primary ms-1">!</span>
                                    @endif
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-4 shadow-modern-lg rounded-modern" style="width: 320px;">
                                    <h6 class="dropdown-header bg-light rounded-modern py-2 mb-3 text-center fw-bold text-primary-custom">Filter Options</h6>
                                    <div class="mb-3">
                                        <label class="form-label-modern">Payment Status</label>
                                        <select class="form-select-modern" wire:model.live="filters.status">
                                            <option value="">All Statuses</option>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-modern">Due Date From</label>
                                        <input type="date" class="form-control-modern" wire:model.live="filters.dateFrom">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-modern">Due Date To</label>
                                        <input type="date" class="form-control-modern" wire:model.live="filters.dateTo">
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn-modern btn-secondary-modern" wire:click="resetFilters">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <button wire:click="printDuePayments"
                                class="btn-modern btn-primary-modern"
                                aria-label="Print due payments">
                                <i class="bi bi-printer"></i> Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="table-modern">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Customer</th>
                                <th class="text-end">Current Due</th>
                                <th class="text-end">Brought-Forward</th>
                                <th class="text-end">Total Due</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($duePayments as $customer)
                            @php
                            $overdue = false;
                            if ($customer && $customer->customerAccounts && $customer->customerAccounts->count() > 0) {
                            $lastAccount = $customer->customerAccounts->last();
                            if ($lastAccount && $lastAccount->created_at) {
                            try {
                            $overdue = now()->gt($lastAccount->created_at->addDays(30)) && ($customer->customer_accounts_sum_current_due_amount ?? 0) > 0;
                            } catch (\Throwable $e) {
                            $overdue = false;
                            }
                            }
                            }
                            @endphp
                            <tr @if($overdue) class="table-danger-subtle" @endif>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $customer->name ?? 'N/A' }}</div>
                                        @if($customer->phone ?? false)
                                        <div class="small text-secondary">{{ $customer->phone }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold">Rs.{{ number_format($customer->adjusted_current_due ?? 0, 2) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold">Rs.{{ number_format($customer->back_forward_amount ?? 0, 2) }}</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-primary-custom">Rs.{{ number_format($customer->adjusted_total_due ?? 0, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($overdue)
                                    <span class="badge-modern badge-danger-modern">
                                        <i class="bi bi-exclamation-circle-fill"></i> Overdue
                                    </span>
                                    @elseif(($customer->adjusted_current_due ?? 0) > 0 || ($customer->back_forward_amount ?? 0) > 0)
                                    <span class="badge-modern badge-warning-modern">
                                        <i class="bi bi-clock-fill"></i> Pending
                                    </span>
                                    @else
                                    <span class="badge-modern badge-success-modern">
                                        <i class="bi bi-check-circle-fill"></i> Paid
                                    </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if (($customer->adjusted_current_due ?? 0) > 0 || ($customer->back_forward_amount ?? 0) > 0)
                                    <button wire:click="getPaymentDetails({{ $customer->id }})"
                                        class="btn-modern btn-view"
                                        title="Receive Payment">
                                        <i class="bi bi-currency-dollar"></i> Receive
                                    </button>
                                    @else
                                    <button class="btn-action btn-view" disabled title="Paid">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-3 mb-0">No due payments found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($duePayments->hasPages())
                <div class="card-footer bg-white border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="text-sm text-muted">
                            Showing {{ $duePayments->firstItem() }} to {{ $duePayments->lastItem() }} of {{ $duePayments->total() }} results
                        </div>
                        <div>
                            {{ $duePayments->links('livewire::bootstrap') }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>


    <!-- Payment Detail Modal -->
    <div wire:ignore.self class="modal fade" id="payment-detail-modal" tabindex="-1" aria-labelledby="payment-detail-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header text-white p-4"
                    style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%);">
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
                                    <span class="text-primary fw-bold" style="font-size: 2rem;">{{ substr($paymentDetail->sale->customer->name ?? $paymentDetail->name, 0, 1) }}</span>
                                </div>
                                <h6 class="mt-3 mb-0 fw-bold text-gray-800">{{ $paymentDetail->sale->customer->name ?? $paymentDetail->name }}</h6>
                                <p class="text-sm text-gray-600 mb-0">{{ $paymentDetail->sale->customer->phone ?? $paymentDetail->phone }}</p>
                            </div>
                            <h6 class="text-uppercase text-sm fw-semibold mb-3 border-bottom pb-2" style="color: #9d1c20;">Due Payment Details</h6>
                            <div class="card border-0 shadow-sm rounded-4 p-3 mt-3 bg-light">
                                <div>
                                    <span class="text-sm text-gray-600">Current Due:</span>
                                    <span class="fw-bold" style="font-size: 1.5rem; color: #9d1c20;">Rs.{{ number_format($currentDueAmount, 2) }}</span>
                                </div>
                                <div class="mt-2">
                                    <span class="text-sm text-gray-600">Brought-Forward:</span>
                                    <span class="fw-bold" style="font-size: 1.5rem; color: #9d1c20;">Rs.{{ number_format($backForwardAmount, 2) }}</span>
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
                                            <h5 class="mb-0 fw-bold text-gray-800" style="color: #9d1c20;">Payment Collection</h5>
                                            <p class="text-sm text-gray-600 mb-0">Record customer payment details for admin approval</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4">
                                    <div class="row">
                                        <div class="col-md-6 mb-4">
                                            <label class="form-label text-sm fw-semibold mb-2" style="color: #9d1c20;">
                                                Apply To: <span class="text-danger">*</span>
                                                <p class="text-muted small mb-2">Select one or both payment targets</p>
                                                <fieldset aria-labelledby="applyToTargets" class="mb-2">
                                                    <legend id="applyToTargets" class="visually-hidden">Payment Targets</legend>
                                                    @if($currentDueAmount > 0)
                                                    <div class="form-check mb-2">
                                                        <input type="checkbox"
                                                            class="form-check-input"
                                                            id="applyToCurrent"
                                                            wire:model="applyToCurrent"
                                                            aria-checked="{{ $applyToCurrent ? 'true' : 'false' }}"
                                                            aria-label="Apply payment to current due">
                                                        <label class="form-check-label" for="applyToCurrent">
                                                            <strong>Current Due</strong> - Rs.{{ number_format($currentDueAmount, 2) }}
                                                        </label>
                                                    </div>
                                                    @endif
                                                    @if($backForwardAmount > 0)
                                                    <div class="form-check mb-2">
                                                        <input type="checkbox"
                                                            class="form-check-input"
                                                            id="applyToBackForward"
                                                            wire:model="applyToBackForward"
                                                            aria-checked="{{ $applyToBackForward ? 'true' : 'false' }}"
                                                            aria-label="Apply payment to brought-forward">
                                                        <label class="form-check-label" for="applyToBackForward">
                                                            <strong>Brought-Forward</strong> - Rs.{{ number_format($backForwardAmount, 2) }}
                                                        </label>
                                                    </div>
                                                    @endif
                                                </fieldset>
                                                @if($currentDueAmount <= 0 && $backForwardAmount <=0)
                                                    <div class="alert alert-info">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    No outstanding dues found for this customer.
                                        </div>
                                        @endif
                                        <!-- Show total payable amount based on selection -->
                                        @php
                                        $totalPayable = 0;
                                        if($applyToCurrent) {
                                        $totalPayable += $currentDueAmount;
                                        }
                                        if($applyToBackForward) {
                                        $totalPayable += max(0, $backForwardAmount);
                                        }
                                        @endphp
                                        @if($totalPayable > 0)
                                        <div class="alert alert-info mt-2 small">
                                            <i class="bi bi-calculator me-1"></i>
                                            <strong>Maximum Payable:</strong> Rs.{{ number_format($totalPayable, 2) }}
                                        </div>
                                        @endif
                                        @if($applyToCurrent && $applyToBackForward)
                                        <div class="alert alert-success mt-2 small">
                                            <i class="bi bi-arrow-right-circle me-1"></i>
                                            <strong>Payment Priority:</strong>Brought-Forward will be paid first, then remaining balance will be applied to Current Due.
                                        </div>
                                        @endif
                                    </div>
                                    <div class="col-md-6 mb-4">
                                        <label class="form-label text-sm fw-semibold mb-2" style="color: #9d1c20;">Cash Amount</label>
                                        <input type="text"
                                            class="form-control rounded-4 shadow-sm @error('receivedAmount') is-invalid @enderror"
                                            wire:model="receivedAmount"
                                            placeholder="Enter cash amount"
                                            @if($currentDueAmount <=0 && $backForwardAmount <=0) disabled @endif>
                                        @error('receivedAmount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($currentDueAmount <= 0 && $backForwardAmount <=0)
                                            <small class="text-muted">No outstanding dues to collect payment for.</small>
                                            @endif
                                    </div>
                                </div>

                                <div class="border rounded-4 p-3 mb-4 shadow-sm bg-light">
                                    <h6 class="text-sm fw-semibold mb-3" style="color: #9d1c20;">Add Cheque Details</h6>
                                    @if($currentDueAmount <= 0 && $backForwardAmount <=0)
                                        <div class="alert alert-warning">
                                        <i class="bi bi-exclamation-triangle me-2"></i>
                                        Cannot add cheques when there are no outstanding dues.
                                </div>
                                @else
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-semibold">Cheque No. <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-sm rounded-3 shadow-sm" wire:model="chequeNumber" placeholder="Cheque Number">
                                        @error('chequeNumber') <span class="text-danger text-xs">{{ $message }}</span> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-xs fw-semibold">Bank Name <span class="text-danger">*</span></label>
                                        <select class="form-control form-control-sm rounded-3 shadow-sm" wire:model="bankName">
                                            <option value="">-- Select a bank --</option>
                                            @foreach($banks as $bank)
                                            <option value="{{ $bank }}">{{ $bank }}</option>
                                            @endforeach
                                        </select>
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
                                @endif
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
                                <label class="form-label text-sm fw-semibold mb-2" style="color: #9d1c20;">Payment Notes</label>
                                <textarea class="form-control rounded-4 shadow-sm" rows="3" wire:model="paymentNote" placeholder="Add any notes about this payment (optional)"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-white border-top rounded-bottom-end-4">
                        <div class="d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105" data-bs-dismiss="modal">
                                <i class="bi bi-x me-1"></i> Cancel
                            </button>
                            <button type="submit"
                                class="btn btn-primary text-white rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                                @if($currentDueAmount <=0 && $backForwardAmount <=0) disabled @endif>
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

    <!-- Extend Due Date Modal -->
    <div wire:ignore.self class="modal fade" id="extend-due-modal" tabindex="-1" aria-labelledby="extend-due-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header text-white p-4"
                    style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%);">
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
                            <h5 class="fw-bold text-gray-800" style="color: #9d1c20;">Extend Payment Due Date</h5>
                            <p class="text-sm text-gray-600">Provide a new due date and reason for extension</p>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-sm fw-semibold mb-2" style="color: #9d1c20;">New Due Date <span class="text-danger">*</span></label>
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
                            <label class="form-label text-sm fw-semibold mb-2" style="color: #9d1c20;">Reason for Extension <span class="text-danger">*</span></label>
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
@include('components.admin-styles')
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('openModal', (modalId) => {
            let modal = new bootstrap.Modal(document.getElementById(modalId));
            modal.show();
        });

        Livewire.on('closeModal', (modalId) => {
            let modalElement = document.getElementById(modalId);
            let modal = bootstrap.Modal.getInstance(modalElement);
            modal.hide();
        });

        Livewire.on('showToast', ({
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
            const actionColumnIndex = 5; // Actions column (0-indexed: Customer, Current, Brought-Forward, Total, Status, Actions)
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
                            <p>PLUS <br> NO 20/2/1, 2nd FLOOR,HUNTER BUILDING,BANKSHALLL STREET,COLOMBO-11 | Phone: 011 - 2332786 <br> Email: plusaccessories.lk@gmail.com</p>
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
                // Overwrite print styles for font and color
                const style = printWindow.document.createElement('style');
                style.innerHTML = `
                    body { font-family: "Courier New", monospace !important; font-size: 14px !important; color: #000 !important; }
                    * { color: #000 !important; }
                    .table-bordered th, .table-bordered td { border: 1px solid #000 !important; padding: 2px 6px !important; font-size: 12px !important; }
                    @media print { * { color: #000 !important; font-weight: bold !important; } }
                `;
                printWindow.document.head.appendChild(style);
                printWindow.focus();
            };
        });
    });
</script>
@endpush