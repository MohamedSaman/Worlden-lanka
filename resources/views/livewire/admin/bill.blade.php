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
                                    <button wire:click="viewInvoice({{ $sale->id }})"
                                        class="btn-action btn-view"
                                        title="View Invoice">
                                        <i class="bi bi-eye"></i>
                                    </button>
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
                                    <td class="text">Name:</td>
                                    <td class="fw-semibold">{{ $saleDetails['sale']->customer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text">Phone:</td>
                                    <td>{{ $saleDetails['sale']->customer->phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Invoice Info -->
                        <div class="col-md-5">
                            <h6 class="fw-bold mb-3" style="color: #9d1c20;">Invoice Information</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td class="text">Invoice Number:</td>
                                    <td class="fw-semibold">#{{ $saleDetails['sale']->invoice_number }}</td>
                                </tr>
                                <tr>
                                    <td class="text">Date:</td>
                                    <td>{{ $saleDetails['sale']->created_at->format('d M Y, h:i A') }}</td>
                                </tr>
                                <tr>
                                    <td class="text">Cashier:</td>
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
                                    <th class="text-center">Size</th>
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
                                    <td class="text-center">
                                        @if($item->product && $item->product->customer_field)
                                        {{ $item->product->customer_field['Size'] ?? '-' }}
                                        @else
                                        -
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $item->quantity }}</td>
                                    <td class="text-end">Rs.{{ number_format($item->price, 2) }}</td>
                                    <td class="text-end">Rs.{{ number_format($item->discount, 2) }}</td>
                                    <td class="text-end fw-bold">Rs.{{ ($item->quantity * $item->price) - $item->discount }}</td>

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
                                            <td class="text">Subtotal:</td>
                                            <td class="text-end">Rs.{{ number_format($saleDetails['sale']->subtotal, 2) }}</td>
                                        </tr>
                                        <tr>
                                            <td class="text">Discount:</td>
                                            <td class="text-end text-danger">-Rs.{{ number_format($saleDetails['sale']->discount_amount, 2) }}</td>
                                        </tr>
                                        @if($saleDetails['totalReturnAmount'] > 0)
                                        <tr>
                                            <td class="text">Returns:</td>
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
                    <button type="button" class="btn btn-warning" wire:click="editInvoice({{ $saleDetails['sale']->id }})" data-bs-dismiss="modal">
                        <i class="bi bi-pencil-square me-2"></i>Edit Invoice
                    </button>
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
@include('components.admin-styles')
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
                            font-family: "Consolas", "Lucida Console", monospace;
                            font-size: 14px;
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
                        color: #000;
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
                        color: #000;
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