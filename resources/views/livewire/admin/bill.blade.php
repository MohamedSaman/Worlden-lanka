<div class="container-fluid py-6 bg-gray-50 min-vh-100">
    <!-- Page Header with Stats -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <!-- Header Content -->
                <div class="card-header text-white p-2 rounded-t-4 d-flex align-items-center"
                    style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%);">
                    <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center me-3">
                        <i class="bi bi-receipt-cutoff text-white fs-4" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h3 class="mb-1 fw-bold tracking-tight text-white">Bills & Invoices</h3>
                        <p class="text-white opacity-80 mb-0 text-sm">View and manage all sales invoices</p>
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="card-body p-5">
                    <div class="row g-4">
                        <!-- Total Sales Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md bg-primary bg-opacity-10 rounded-circle me-3">
                                            <i class="bi bi-receipt text-primary fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 mb-1">Total Sales</p>
                                            <h4 class="fw-bold mb-0" style="color: #9d1c20;">{{ $totalSales }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today Sales Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md bg-success bg-opacity-10 rounded-circle me-3">
                                            <i class="bi bi-calendar-check text-success fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 mb-1">Today's Sales</p>
                                            <h4 class="fw-bold mb-0 text-success">{{ $todaySales }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Revenue Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md bg-warning bg-opacity-10 rounded-circle me-3">
                                            <i class="bi bi-currency-dollar text-warning fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 mb-1">Total Revenue</p>
                                            <h4 class="fw-bold mb-0 text-warning">Rs.{{ number_format($totalRevenue, 2) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Today Revenue Card -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card border-0 shadow-lg rounded-4 h-100 transition-all hover:scale-105">
                                <div class="card-body p-4">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-shape icon-md bg-info bg-opacity-10 rounded-circle me-3">
                                            <i class="bi bi-cash-coin text-info fs-5"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-600 mb-1">Today's Revenue</p>
                                            <h4 class="fw-bold mb-0 text-info">Rs.{{ number_format($todayRevenue, 2) }}</h4>
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

    <!-- Sales Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <!-- Search & Filter Bar -->
                <div class="card-header p-4" style="background-color: #eff6ff;">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                        <div class="flex-grow-1">
                            <div class="input-group" style="max-width: 600px;">
                                <span class="input-group-text bg-gray-100 border-0 px-3">
                                    <i class="bi bi-search text-danger"></i>
                                </span>
                                <input type="text"
                                    class="form-control"
                                    placeholder="Search by invoice number or customer..."
                                    wire:model.live.debounce.300ms="search"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="dropdown">
                                <button class="btn btn-light rounded-pill shadow-sm px-4 py-2" type="button"
                                    id="filterDropdown" data-bs-toggle="dropdown"
                                    style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;">
                                    <i class="bi bi-funnel me-1"></i> Filters
                                </button>
                                <div class="dropdown-menu p-4 shadow-lg border-0 rounded-4" style="width: 300px;">
                                    <h6 class="dropdown-header bg-light rounded py-2 mb-3 text-center">Filter Options</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-sm">Date From</label>
                                        <input type="date" class="form-control" wire:model.live="filters.dateFrom">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-sm">Date To</label>
                                        <input type="date" class="form-control" wire:model.live="filters.dateTo">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-sm">Payment Type</label>
                                        <select class="form-select" wire:model.live="filters.paymentType">
                                            <option value="">All</option>
                                            <option value="full">Full Payment</option>
                                            <option value="partial">Partial Payment</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-sm">Customer Type</label>
                                        <select class="form-select" wire:model.live="filters.customerType">
                                            <option value="">All</option>
                                            <option value="retail">Retail</option>
                                            <option value="wholesale">Wholesale</option>
                                        </select>
                                    </div>
                                    <div class="d-grid">
                                        <button class="btn btn-secondary btn-sm" wire:click="resetFilters">
                                            Reset Filters
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="card-body p-5">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #eff6ff;">
                                <tr>
                                    <th class="text-uppercase text-xs fw-semibold py-3" style="color: #9d1c20;">Invoice #</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3" style="color: #9d1c20;">Customer</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3" style="color: #9d1c20;">Date</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">Items</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-end" style="color: #9d1c20;">Total Amount</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">Payment Type</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">Status</th>
                                    <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sales as $sale)
                                <tr class="border-bottom">
                                    <td>
                                        <span class="fw-bold text-primary">#{{ $sale->invoice_number }}</span>
                                    </td>
                                    <td>
                                        <div>
                                            <div class="fw-semibold">{{ $sale->customer->name ?? 'N/A' }}</div>
                                            <div class="text-sm text-muted">{{ $sale->customer->phone ?? '' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-sm">{{ $sale->created_at->format('d M Y') }}</div>
                                        <div class="text-xs text-muted">{{ $sale->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $sale->items->count() }} items</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold">Rs.{{ number_format($sale->total_amount, 2) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($sale->payment_type === 'full')
                                        <span class="badge bg-success">Full</span>
                                        @else
                                        <span class="badge bg-warning">Partial</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($sale->payment_status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                        @elseif($sale->payment_status === 'partial')
                                        <span class="badge bg-warning">Partial</span>
                                        @else
                                        <span class="badge bg-danger">Unpaid</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button wire:click="viewInvoice({{ $sale->id }})"
                                            class="btn btn-sm btn-primary rounded-pill">
                                            <i class="bi bi-eye me-1"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <i class="bi bi-inbox fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">No sales found</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($sales->hasPages())
                    <div class="card-footer p-4 bg-white border-top rounded-b-4">
                        <div class="mt-3">
                            {{ $sales->links('livewire.custom-pagination') }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Details Modal -->
    @if($saleDetails)
    <div wire:ignore.self class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header text-white p-4" style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%);">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-receipt me-2"></i> Invoice Details - #{{ $saleDetails['sale']->invoice_number }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row mb-4">
                        <!-- Customer Info -->
                        <div class="col-md-5">
                            <h6 class="fw-bold mb-3" style="color: #9d1c20;">Customer Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Name:</td>
                                    <td class="fw-semibold">{{ $saleDetails['sale']->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Phone:</td>
                                    <td>{{ $saleDetails['sale']->customer->phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Invoice Info -->
                        <div class="col-md-5">
                            <h6 class="fw-bold mb-3" style="color: #9d1c20;">Invoice Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text-muted">Invoice Number:</td>
                                    <td class="fw-semibold">#{{ $saleDetails['sale']->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Date:</td>
                                    <td>{{ $saleDetails['sale']->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Cashier:</td>
                                    <td>{{ $saleDetails['sale']->user->name ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Items Table -->
                    <h6 class="fw-bold mb-3" style="color: #9d1c20;">Items Purchased</h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead style="background-color: #eff6ff;">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Discount</th>
                                    <th class="text-end">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($saleDetails['items'] as $item)
                                <tr>
                                    <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">Rs.{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">Rs.{{ number_format($item->discount, 2) }}</td>
                                    <td class="text-end fw-bold">Rs.{{ number_format($item->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Return Items -->
                    @if($saleDetails['returnItems']->count() > 0)
                    <h6 class="fw-bold mb-3 text-danger">
                        <i class="bi bi-arrow-return-left me-2"></i>Returned Items
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead class="table-danger">
                                <tr>
                                    <th>Product</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Total</th>
                                    <th>Notes</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($saleDetails['returnItems'] as $return)
                                <tr>
                                    <td>{{ $return->product->product_name ?? 'N/A' }}</td>
                                    <td class="text-center">{{ $return->return_quantity }}</td>
                                    <td class="text-end">Rs.{{ number_format($return->selling_price, 2) }}</td>
                                    <td class="text-end fw-bold">Rs.{{ number_format($return->total_amount, 2) }}</td>
                                    <td>{{ $return->notes ?? '-' }}</td>
                                    <td>{{ $return->created_at->format('d M Y') }}</td>
                                </tr>
                                @endforeach
                                <tr class="table-danger">
                                    <td colspan="3" class="text-end fw-bold">Total Returns:</td>
                                    <td class="text-end fw-bold">Rs.{{ number_format($saleDetails['totalReturnAmount'], 2) }}</td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @endif

                    <!-- Payment Summary -->
                    <div class="row">
                        <div class="col-md-6 offset-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3" style="color: #9d1c20;">Payment Summary</h6>
                                    <table class="table table-sm table-borderless mb-0">
                                        <tr>
                                            <td class="text-muted">Subtotal:</td>
                                            <td class="text-end">Rs.{{ number_format($saleDetails['sale']->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Discount:</td>
                                            <td class="text-end text-danger">-Rs.{{ number_format($saleDetails['sale']->discount_amount, 2) }}</td>
                                        </tr>
                                        @if($saleDetails['totalReturnAmount'] > 0)
                                        <tr>
                                            <td class="text-muted">Returns:</td>
                                            <td class="text-end text-danger">-Rs.{{ number_format($saleDetails['totalReturnAmount'], 2) }}</td>
                                        </tr>
                                        @endif
                                        <tr class="border-top">
                                            <td class="fw-bold fs-5" style="color: #9d1c20;">Grand Total:</td>
                                            <td class="text-end fw-bold fs-5" style="color: #9d1c20;">
                                                Rs.{{ number_format($saleDetails['adjustedGrandTotal'], 2) }}
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" onclick="printInvoice()">
                        <i class="bi bi-printer me-2"></i>Print Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    body {
        font-family: 'Inter', sans-serif;
    }

    .tracking-tight {
        letter-spacing: -0.025em;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .hover\:scale-105:hover {
        transform: scale(1.05);
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

    .icon-shape.icon-md {
        width: 2.5rem;
        height: 2.5rem;
    }

    .rounded-4 {
        border-radius: 1rem;
    }

    .shadow-lg {
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
<script>
    function printInvoice() {
        // Get modal element
        const modal = document.getElementById('invoiceModal');
        if (!modal) {
            alert('Please open an invoice first.');
            return;
        }

        // Get sale details from modal
        const invoiceNumber = modal.querySelector('.modal-title').textContent.split('#')[1]?.trim() || '';
        const modalBody = modal.querySelector('.modal-body');

        // Extract customer and invoice info
        const customerSection = modalBody.querySelectorAll('.col-md-5')[0];
        const invoiceSection = modalBody.querySelectorAll('.col-md-5')[1];

        // Extract items table
        const itemsTable = modalBody.querySelector('.table-bordered');

        // Extract return items if exists
        const returnSection = modalBody.querySelector('.text-danger')?.closest('div');
        let returnHTML = '';
        if (returnSection) {
            const returnTable = returnSection.querySelector('.table-bordered');
            if (returnTable) {
                returnHTML = `
                    <h6 style="color: #dc3545; font-weight: bold; margin-top: 20px; margin-bottom: 10px;">
                        <i class="bi bi-arrow-return-left"></i> Returned Items
                    </h6>
                    ${returnTable.outerHTML}
                `;
            }
        }

        // Extract payment summary
        const summaryCard = modalBody.querySelector('.col-md-6.offset-md-6 .card-body');
        let summaryHTML = '';
        if (summaryCard) {
            const summaryTable = summaryCard.querySelector('table');
            if (summaryTable) {
                const rows = summaryTable.querySelectorAll('tr');
                rows.forEach(row => {
                    const label = row.querySelector('td:first-child')?.textContent.trim() || '';
                    const value = row.querySelector('td:last-child')?.textContent.trim() || '';
                    const isTotal = row.classList.contains('border-top');
                    summaryHTML += `
                        <div class="summary-row ${isTotal ? 'total' : ''}">
                            <span>${label}</span>
                            <span>${value}</span>
                        </div>
                    `;
                });
            }
        }

        // Create a hidden iframe for printing
        let printFrame = document.getElementById('printFrame');
        if (!printFrame) {
            printFrame = document.createElement('iframe');
            printFrame.id = 'printFrame';
            printFrame.style.position = 'absolute';
            printFrame.style.width = '0';
            printFrame.style.height = '0';
            printFrame.style.border = 'none';
            document.body.appendChild(printFrame);
        }

        const htmlContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Invoice ${invoiceNumber}</title>
                <meta charset="UTF-8">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
                <style>
                    @page { size: A4; margin: 1cm; }
                    body {
                        font-family: Arial, sans-serif;
                        padding: 20px;
                        font-size: 13px;
                        color: #333;
                    }
                    .company-header {
                        text-align: center;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #9d1c20;
                        padding-bottom: 15px;
                    }
                    .company-name {
                        font-size: 32px;
                        font-weight: bold;
                        color: #9d1c20;
                        margin: 0;
                    }
                    .company-address {
                        font-size: 11px;
                        color: #666;
                        margin: 5px 0;
                    }
                    .receipt-title {
                        font-size: 24px;
                        font-weight: bold;
                        color: #9d1c20;
                        text-align: center;
                        margin: 15px 0;
                        text-decoration: underline;
                    }
                    .info-row {
                        display: flex;
                        justify-content: space-between;
                        margin-bottom: 20px;
                    }
                    .info-section {
                        width: 48%;
                    }
                    .info-section h6 {
                        color: #9d1c20;
                        font-weight: bold;
                        border-bottom: 1px solid #ddd;
                        padding-bottom: 5px;
                        margin-bottom: 10px;
                        font-size: 14px;
                    }
                    .info-section table {
                        width: 100%;
                        font-size: 12px;
                    }
                    .info-section td {
                        padding: 3px 0;
                    }
                    .items-table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 20px 0;
                    }
                    .items-table th {
                        background-color: #9d1c20;
                        color: white;
                        padding: 8px;
                        text-align: left;
                        font-size: 12px;
                    }
                    .items-table td {
                        padding: 6px 8px;
                        border-bottom: 1px solid #ddd;
                        font-size: 12px;
                    }
                    .summary-section {
                        display: flex;
                        justify-content: flex-end;
                        margin-top: 20px;
                    }
                    .summary-box {
                        width: 400px;
                        border: 2px solid #9d1c20;
                        padding: 15px;
                    }
                    .summary-row {
                        display: flex;
                        justify-content: space-between;
                        padding: 5px 0;
                        border-bottom: 1px solid #eee;
                    }
                    .summary-row.total {
                        font-size: 16px;
                        font-weight: bold;
                        color: #9d1c20;
                        border-top: 2px solid #9d1c20;
                        margin-top: 10px;
                        padding-top: 10px;
                        border-bottom: none;
                    }
                    .footer {
                        text-align: center;
                        margin-top: 40px;
                        padding-top: 20px;
                        border-top: 1px solid #ddd;
                        font-size: 11px;
                        color: #666;
                        clear: both;
                    }
                    @media print {
                        body { padding: 10px; }
                    }
                </style>
            </head>
            <body>
                <div class="company-header">
                    <div class="company-name">PLUS</div>
                    <div class="company-address">NO 20/2/1, 2nd FLOOR, HUNTER BUILDING, BANKSHALLL STREET, COLOMBO-11</div>
                    <div class="company-address">Phone: 011 - 2332786 | Email: plusaccessories.lk@gmail.com</div>
                </div>
                
                <div class="receipt-title">SALES RECEIPT</div>
                
                <!-- Customer and Invoice Info in One Row -->
                <div class="info-row">
                    <div class="info-section">
                        ${customerSection ? customerSection.innerHTML : ''}
                    </div>
                    <div class="info-section">
                        ${invoiceSection ? invoiceSection.innerHTML : ''}
                    </div>
                </div>
                
                <!-- Items Purchased -->
                <h6 style="color: #9d1c20; font-weight: bold; margin-top: 20px; margin-bottom: 10px;">Items Purchased</h6>
                ${itemsTable ? itemsTable.outerHTML : ''}
                
                <!-- Return Items (if any) -->
                ${returnHTML}
                
                <!-- Payment Summary in One Row -->
                <div class="summary-section">
                    <div class="summary-box">
                        <h6 style="color: #9d1c20; font-weight: bold; margin-bottom: 15px;">Payment Summary</h6>
                        ${summaryHTML}
                    </div>
                </div>
                
                <div class="footer">
                    <p><strong>Thank you for your purchase!</strong></p>
                </div>
            </body>
            </html>
        `;

        const frameDoc = printFrame.contentWindow || printFrame.contentDocument;
        frameDoc.document.open();
        frameDoc.document.write(htmlContent);
        frameDoc.document.close();

        // Wait for content to load then print
        setTimeout(() => {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
        }, 500);
    }

    document.addEventListener('livewire:initialized', () => {
        @this.on('openInvoiceModal', () => {
            let modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
            modal.show();
        });
    });
</script>
@endpush