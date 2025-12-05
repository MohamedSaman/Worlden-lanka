<div class="container-fluid py-4">
    <!-- Page Header with Stats -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-header-modern mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Bills & Invoices</h3>
                        <p class="mb-0">View and manage all sales invoices</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="col-xl-3 col-md-6">
            <div class="stat-card primary animate-fade-in">
                <div class="stat-icon">
                    <i class="bi bi-receipt"></i>
                </div>
                <div class="stat-label">Total Sales</div>
                <div class="stat-value">{{ number_format($totalSales) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card success animate-fade-in" style="animation-delay: 0.1s;">
                <div class="stat-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="stat-label">Today's Sales</div>
                <div class="stat-value">{{ number_format($todaySales) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card warning animate-fade-in" style="animation-delay: 0.2s;">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div class="stat-label">Total Revenue</div>
                <div class="stat-value text-warning">Rs.{{ number_format($totalRevenue, 2) }}</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="stat-card info animate-fade-in" style="animation-delay: 0.3s;">
                <div class="stat-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="stat-label">Today's Revenue</div>
                <div class="stat-value text-info">Rs.{{ number_format($todayRevenue, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Sales Table -->
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
                                        placeholder="Search by invoice number or customer..."
                                        wire:model.live.debounce.300ms="search"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 text-lg-end">
                            <div class="dropdown d-inline-block">
                                <button class="btn-modern btn-secondary-modern" type="button"
                                    id="filterDropdown" data-bs-toggle="dropdown">
                                    <i class="bi bi-funnel"></i> Filters
                                </button>
                                <div class="dropdown-menu dropdown-menu-end p-4 shadow-modern-lg rounded-modern" style="width: 320px;">
                                    <h6 class="dropdown-header bg-light rounded-modern py-2 mb-3 text-center fw-bold text-primary-custom">Filter Options</h6>
                                    <div class="mb-3">
                                        <label class="form-label-modern">Date From</label>
                                        <input type="date" class="form-control-modern" wire:model.live="filters.dateFrom">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-modern">Date To</label>
                                        <input type="date" class="form-control-modern" wire:model.live="filters.dateTo">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-modern">Payment Type</label>
                                        <select class="form-select-modern" wire:model.live="filters.paymentType">
                                            <option value="">All</option>
                                            <option value="full">Full Payment</option>
                                            <option value="partial">Partial Payment</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-modern">Customer Type</label>
                                        <select class="form-select-modern" wire:model.live="filters.customerType">
                                            <option value="">All</option>
                                            <option value="retail">Retail</option>
                                            <option value="wholesale">Wholesale</option>
                                        </select>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn-modern btn-secondary-modern" wire:click="resetFilters">
                                            <i class="bi bi-arrow-counterclockwise"></i> Reset Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="table-modern">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th class="text-center">Items</th>
                                <th class="text-end">Total Amount</th>
                                <th class="text-center">Payment</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($sales as $sale)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary-custom">#{{ $sale->invoice_number }}</span>
                                </td>
                                <td>
                                    <div>
                                        <div class="fw-semibold">{{ $sale->customer->name ?? 'N/A' }}</div>
                                        <div class="small text-secondary">{{ $sale->customer->phone ?? '' }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="small">{{ $sale->created_at->format('d M Y') }}</div>
                                    <div class="small text-secondary">{{ $sale->created_at->format('h:i A') }}</div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-modern badge-info-modern">
                                        <i class="bi bi-box-seam"></i> {{ $sale->items->count() }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold">Rs.{{ number_format($sale->total_amount, 2) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($sale->payment_type === 'full')
                                    <span class="badge-modern badge-success-modern">Full</span>
                                    @else
                                    <span class="badge-modern badge-warning-modern">Partial</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($sale->payment_status === 'paid')
                                    <span class="badge-modern badge-success-modern">
                                        <i class="bi bi-check-circle-fill"></i> Paid
                                    </span>
                                    @elseif($sale->payment_status === 'partial')
                                    <span class="badge-modern badge-warning-modern">
                                        <i class="bi bi-clock-fill"></i> Partial
                                    </span>
                                    @else
                                    <span class="badge-modern badge-danger-modern">
                                        <i class="bi bi-exclamation-circle-fill"></i> Unpaid
                                    </span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button wire:click="viewInvoice({{ $sale->id }})"
                                            class="btn-action btn-view"
                                            title="View Receipt">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button wire:click="confirmDelete({{ $sale->id }})"
                                            class="btn-action btn-delete"
                                            title="Delete Sale"
                                            wire:loading.attr="disabled">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="text-center">
                                        <i class="bi bi-inbox fs-1 text"></i>
                                        <p class="text mt-3 mb-0">No sales found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($sales->hasPages())
                <div class="card-footer bg-white border-top p-4">
                    <div class="pagination-modern">
                        {{ $sales->links('livewire.custom-pagination') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
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
                        @if($saleDetails)
                        <a href="{{ route('admin.store-billing') }}?edit={{ $saleDetails['sale']->id }}"
                            class="btn btn-sm btn-warning rounded-full px-3 transition-all hover:shadow">
                            <i class="bi bi-pencil-square me-1"></i>Edit
                        </a>
                        @endif
                        <button type="button" class="btn btn-sm rounded-full px-3 transition-all hover:shadow"
                            id="printButton" style="background-color: #9d1c20;border-color:#fff; color: #fff;">
                            <i class="bi bi-printer me-1"></i>Print
                        </button>
                        <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100"
                            data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-4" id="receiptContent">
                    @if($saleDetails)
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
                                        $saleDetails['sale']->invoice_number }}</strong></p>
                                <p class="mb-1" style="color: #9d1c20;"><strong>Date: {{
                                        $saleDetails['sale']->created_at->setTimezone('Asia/Colombo')->format('d/m/Y h:i A') }}</strong>
                                </p>
                                <p class="mb-1" style="color: #9d1c20;">
                                    <strong>Payment Status: {{ ucfirst($saleDetails['sale']->payment_status) }}
                                    </strong>
                                </p>
                                <p class="mb-1" style="color: #9d1c20;"><strong>Delivery Note: {{ $saleDetails['sale']->delivery_note ?? 'N/A' }}</strong></p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="mb-2 fw-medium" style="color: #9d1c20;">CUSTOMER DETAILS</h6>
                                @if ($saleDetails['sale']->customer)
                                <p class="mb-1" style="color: #9d1c20;"><strong>Name: {{
                                        $saleDetails['sale']->customer->name }}</strong></p>
                                <p class="mb-1" style="color: #9d1c20;"><strong>Phone: {{
                                        $saleDetails['sale']->customer->phone ?? 'N/A' }}</strong></p>
                                <p class="mb-1" style="color: #9d1c20;"><strong>Type: {{
                                        ucfirst($saleDetails['sale']->customer_type) ?? 'N/A' }}</strong></p>
                                @else
                                <p class="text" style="color: #9d1c20;"><strong>Walk-in Customer</strong></p>
                                @endif
                            </div>
                        </div>

                        <h6 class="text mb-2 fw-medium" style="color: #9d1c20;">PURCHASED ITEMS</h6>
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered table-sm border-1" style="border-color: #9d1c20;">
                                <thead style="background-color: #9d1c20; color: #FFFFFF;">
                                    <tr>
                                        <th scope="col" class="text-center py-2">No</th>
                                        <th scope="col" class="text-center py-2">Item</th>
                                        @if($saleDetails && $saleDetails['sale']->items->some(fn($item) => $item->product && isset($item->product->customer_field['Size']) && $item->product->customer_field['Size'] && isset($item->product->customer_field['Color']) && $item->product->customer_field['Color']))
                                        <th scope="col" class="text-center py-2">Size | Color</th>
                                        @endif
                                        <th scope="col" class="text-center py-2">Price</th>
                                        <th scope="col" class="text-center py-2">Qty</th>
                                        <th scope="col" class="text-center py-2">Discount</th>
                                        <th scope="col" class="text-center py-2">Total</th>
                                    </tr>
                                </thead>
                                <tbody style="color: #9d1c20;">
                                    @foreach ($saleDetails['items'] as $index => $item)
                                    <tr class="transition-all hover:bg-gray-50">
                                        <td class="text-center py-2">{{ $index + 1 }}</td>
                                        <td class="text-center py-2">{{ $item->product->product_name ?? 'N/A' }}</td>
                                        @if($saleDetails && $saleDetails['sale']->items->some(fn($i) => $i->product && isset($i->product->customer_field['Size']) && $i->product->customer_field['Size'] && isset($i->product->customer_field['Color']) && $i->product->customer_field['Color']))
                                        <td class="text-center py-2">
                                            @if($item->product && isset($item->product->customer_field['Size']) && $item->product->customer_field['Size'] && isset($item->product->customer_field['Color']) && $item->product->customer_field['Color'])
                                            {{ $item->product->customer_field['Size'] }} | {{ $item->product->customer_field['Color'] }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                        @endif
                                        <td class="text-center py-2">Rs.{{ number_format($item->price, 2) }}</td>
                                        <td class="text-center py-2">{{ $item->quantity }}</td>
                                        <td class="text-center py-2">Rs.{{ number_format($item->discount * $item->quantity, 2) }}</td>
                                        <td class="text-center py-2">Rs.{{ number_format(($item->price * $item->quantity) - ($item->discount * $item->quantity), 2) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text mb-2 fw-medium" style="color: #9d1c20;">PAYMENT INFORMATION</h6>
                                @if ($saleDetails['sale']->payments->count() > 0)
                                @foreach ($saleDetails['sale']->payments as $payment)
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

                                @if ($saleDetails['sale']->notes)
                                <h6 class="text mt-3 mb-2 fw-medium" style="color: #9d1c20;">NOTES</h6>
                                <p class="font-italic" style="color: #9d1c20;">{{ $saleDetails['sale']->notes }}</p>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <div class="card border-1 rounded-3 shadow-sm" style="border-color: #9d1c20;">
                                    <div class="card-body p-3">
                                        <h6 class="card-title fw-bold tracking-tight" style="color: #9d1c20;">ORDER SUMMARY</h6>
                                        <div class="d-flex justify-content-between mb-2" style="color: #9d1c20;">
                                            <span><strong>Subtotal:</strong></span>
                                            <span><strong>Rs.{{ number_format($saleDetails['sale']->subtotal, 2) }}</strong></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" style="color: #9d1c20;">
                                            <span><strong>Total Discount:</strong></span>
                                            <span><strong>Rs.{{ number_format($saleDetails['sale']->discount_amount, 2) }}</strong></span>
                                        </div>
                                        <hr style="border-color: #9d1c20;">
                                        <div class="d-flex justify-content-between" style="color: #9d1c20;">
                                            <span class="fw-bold">Grand Total:</span>
                                            <span class="fw-bold">Rs.{{ number_format($saleDetails['sale']->total_amount, 2) }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4 pt-3 border-top" style="border-color: #9d1c20;">
                            <p class="mb-0 text small" style="color: #9d1c20;">Thank you for your purchase!</p>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-top py-3" style="border-color: #9d1c20; background: #F8F9FA;">
                    <button type="button"
                        class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow"
                        data-bs-dismiss="modal"
                        style="background-color: #9d1c20; border-color: #9d1c20; color: #FFFFFF;"
                        onmouseover="this.style.backgroundColor='#8b1a1e'; this.style.borderColor='#8b1a1e';"
                        onmouseout="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl border-0">
                <div class="modal-header border-0 bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="deleteConfirmModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Confirm Deletion
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <div class="mb-4">
                        <i class="bi bi-trash text-danger" style="font-size: 4rem;"></i>
                    </div>
                    <h5 class="mb-3">Are you sure you want to delete this sale?</h5>
                    <p class="text-muted mb-0">This action will:</p>
                    <ul class="text-start text-muted mt-2">
                        <li>Delete the sale record</li>
                        <li>Remove all sale items</li>
                        <li>Delete associated payments</li>
                        <li>Update customer balance if applicable</li>
                    </ul>
                    <div class="alert alert-warning mt-3 mb-0" role="alert">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        <strong>Warning:</strong> This action cannot be undone!
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill px-4" wire:click="deleteSale" data-bs-dismiss="modal" wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="bi bi-trash me-1"></i>Yes, Delete
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Deleting...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
@include('components.admin-styles')
<style>
    .btn-action {
        border: none;
        background: transparent;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 1.1rem;
    }

    .btn-view {
        color: #0d6efd;
    }

    .btn-view:hover {
        background-color: #e7f1ff;
        color: #0a58ca;
    }

    .btn-delete {
        color: #dc3545;
    }

    .btn-delete:hover {
        background-color: #f8d7da;
        color: #b02a37;
    }
</style>
@endpush

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
                    * { color: #000 !important; font:bold !important; }
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
        @this.on('openInvoiceModal', () => {
            let modal = new bootstrap.Modal(document.getElementById('receiptModal'));
            modal.show();
        });

        @this.on('showDeleteConfirmation', () => {
            let modal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
            modal.show();
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