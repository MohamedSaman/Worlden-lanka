<div>
    @push('styles')
    @include('components.admin-styles')
    <style>
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
            cursor: pointer;
        }

        .badge-custom {
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }

        .status-partial {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-pending {
            background-color: #f8d7da;
            color: #721c24;
        }

        .print-only {
            display: none;
        }

        @media print {
            body,
            html {
                font-family: "Courier New", monospace !important;
                font-size: 14px !important;
                color: #000 !important;
                height: auto !important;
                overflow: visible !important;
            }

            * {
                color: #000 !important;
            }

            body * {
                visibility: hidden !important;
            }

            #manualSalesPrint,
            #manualSalesPrint * {
                visibility: visible !important;
            }

            .print-only {
                display: block !important;
            }

            #manualSalesPrint {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
            }

            .no-print {
                display: none !important;
            }

            @page {
                size: auto;
                margin: 10mm;
            }

            #manualSalesPrint .table-responsive {
                max-height: none !important;
                overflow: visible !important;
            }

            #manualSalesPrint thead {
                position: static !important;
            }

            #manualSalesPrint table {
                page-break-inside: auto;
            }

            #manualSalesPrint tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            * {
                color: #000 !important;
                font-weight: bold !important;
            }
        }
    </style>
    @endpush

    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #9d1c20 0%, #d34d51 100%);">
                    <div class="card-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2 class="text-white mb-1 fw-bold">
                                    <i class="bi bi-receipt-cutoff me-2"></i>Manual Sales Records
                                </h2>
                                <p class="text-white-50 mb-0">
                                    View and manage all manual billing transactions
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <a href="{{ route('admin.manual-billing') }}" class="btn btn-light btn-lg">
                                    <i class="bi bi-plus-circle me-2"></i>New Manual Sale
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label small fw-bold">Search</label>
                                <input type="text" class="form-control" wire:model.live.debounce.300ms="search"
                                    placeholder="Invoice, customer name or phone...">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Start Date</label>
                                <input type="date" class="form-control" wire:model.live="startDate">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">End Date</label>
                                <input type="date" class="form-control" wire:model.live="endDate">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label small fw-bold">Payment Status</label>
                                <select class="form-select" wire:model.live="paymentStatus">
                                    <option value="">All Status</option>
                                    <option value="paid">Paid</option>
                                    <option value="partial">Partial</option>
                                    <option value="pending">Pending</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button class="btn btn-secondary w-100" wire:click="$set('search', ''); $set('paymentStatus', '')">
                                    <i class="bi bi-arrow-clockwise me-1"></i>Reset Filters
                                </button>
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button class="btn btn-danger w-100" onclick="printManualSalesTable()">
                                    <i class="bi bi-printer me-1"></i>Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Invoice #</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Items</th>
                                        <th>Total Amount</th>
                                        <th>Due Amount</th>
                                        <th>Payment Status</th>
                                        <th>Payment Type</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($sales as $sale)
                                    <tr>
                                        <td>
                                            <strong class="text-danger">{{ $sale->invoice_number }}</strong>
                                        </td>
                                        <td>{{ $sale->created_at->format('d M Y') }}</td>
                                        <td>
                                            <div>
                                                <strong>{{ $sale->customer->name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $sale->customer->phone ?? '' }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $sale->items->count() }} items</td>
                                        <td>
                                            <strong>Rs.{{ number_format($sale->total_amount, 2) }}</strong>
                                        </td>
                                        <td>
                                            @if($sale->due_amount > 0)
                                            <span class="text-danger fw-bold">Rs.{{ number_format($sale->due_amount, 2) }}</span>
                                            @else
                                            <span class="text-success">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-custom status-{{ $sale->payment_status }}">
                                                {{ ucfirst($sale->payment_status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($sale->payment_type) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-sm btn-info" wire:click="viewSale({{ $sale->id }})"
                                                    data-bs-toggle="modal" data-bs-target="#saleDetailModal"
                                                    title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <a href="{{ route('admin.manual-billing') }}?edit={{ $sale->id }}" 
                                                    class="btn btn-sm btn-warning"
                                                    title="Edit Sale">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button class="btn btn-sm btn-danger" 
                                                    wire:click="deleteSale({{ $sale->id }})"
                                                    onclick="return confirm('Are you sure you want to delete this sale?')'"
                                                    title="Delete Sale">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="bi bi-inbox fs-1 text-muted d-block mb-3"></i>
                                            <p class="text-muted">No manual sales found</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
                            {{ $sales->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sale Detail Modal -->
        @if($selectedSale)
        <div class="modal fade" id="saleDetailModal" tabindex="-1" aria-labelledby="saleDetailModalLabel"
            aria-hidden="true" wire:ignore.self>
            <div class="modal-dialog modal-xl">
                <div class="modal-content rounded-4 shadow-xl"
                    style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #FFFFFF, #F8F9FA);">
                    <div class="modal-header"
                        style="background-color: #9d1c20; color: #FFFFFF; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                        <h5 class="modal-title fw-bold tracking-tight" id="saleDetailModalLabel">
                            <i class="bi bi-receipt me-2"></i>Sales Receipt
                        </h5>
                        <div class="ms-auto d-flex gap-2">
                            <button type="button" class="btn btn-sm rounded-full px-3 transition-all hover:shadow"
                                style="background-color: #9d1c20;border-color:#fff; color: #fff;" onclick="printSaleReceipt()">
                                <i class="bi bi-printer me-1"></i>Print
                            </button>
                            <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100"
                                data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                    </div>
                    <div class="modal-body p-4" id="saleReceiptContent">
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
                                            $selectedSale->invoice_number }}</strong></p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Date: {{
                                            $selectedSale->created_at->setTimezone('Asia/Colombo')->format('d/m/Y h:i A') }}</strong>
                                    </p>
                                    <p class="mb-1" style="color: #9d1c20;">
                                        <strong>Payment Status: {{ ucfirst($selectedSale->payment_status) }}
                                        </strong>
                                    </p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Delivery Note: {{ $selectedSale->delivery_note ?? 'N/A' }}</strong></p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-2 fw-medium" style="color: #9d1c20;">CUSTOMER DETAILS</h6>
                                    @if ($selectedSale->customer)
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Name: {{
                                            $selectedSale->customer->name }}</strong></p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Phone: {{
                                            $selectedSale->customer->phone ?? 'N/A' }}</strong></p>
                                    <p class="mb-1" style="color: #9d1c20;"><strong>Type: {{
                                            ucfirst($selectedSale->customer_type) ?? 'N/A' }}</strong></p>
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
                                            <th scope="col" class="text-center py-2">Category</th>
                                            <th scope="col" class="text-center py-2">Price</th>
                                            <th scope="col" class="text-center py-2">Qty</th>
                                            <th scope="col" class="text-center py-2">Discount</th>
                                            <th scope="col" class="text-center py-2">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody style="color: #9d1c20;">
                                        @foreach ($selectedSale->items as $index => $item)
                                        <tr class="transition-all hover:bg-gray-50">
                                            <td class="text-center py-2">{{ $index + 1 }}</td>
                                            <td class="text-center py-2">{{ $item->product_name }}</td>
                                            <td class="text-center py-2">{{ $item->category ?? 'N/A' }}</td>
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
                                    @if ($selectedSale->payments->count() > 0)
                                    @foreach ($selectedSale->payments as $payment)
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

                                    @if ($selectedSale->notes)
                                    <h6 class="text mt-3 mb-2 fw-medium" style="color: #9d1c20;">NOTES</h6>
                                    <p class="font-italic" style="color: #9d1c20;">{{ $selectedSale->notes }}</p>
                                    @endif
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-1 rounded-3 shadow-sm" style="border-color: #9d1c20;">
                                        <div class="card-body p-3">
                                            <h6 class="card-title fw-bold tracking-tight" style="color: #9d1c20;">ORDER SUMMARY</h6>
                                            <div class="d-flex justify-content-between mb-2" style="color: #9d1c20;">
                                                <span><strong>Subtotal:</strong></span>
                                                <span><strong>Rs.{{ number_format($selectedSale->subtotal, 2) }}</strong></span>
                                            </div>
                                            <div class="d-flex justify-content-between mb-2" style="color: #9d1c20;">
                                                <span><strong>Discount:</strong></span>
                                                <span><strong>Rs.{{ number_format($selectedSale->discount_amount, 2) }}</strong></span>
                                            </div>
                                            <hr class="my-2" style="border-color: #9d1c20;">
                                            <div class="d-flex justify-content-between mb-2" style="color: #9d1c20;">
                                                <span class="fw-bold fs-5"><strong>Grand Total:</strong></span>
                                                <span class="fw-bold fs-5"><strong>Rs.{{ number_format($selectedSale->total_amount, 2) }}</strong></span>
                                            </div>
                                            @if ($selectedSale->due_amount > 0)
                                            <div class="d-flex justify-content-between mb-2 text-danger">
                                                <span><strong>Due Amount:</strong></span>
                                                <span><strong>Rs.{{ number_format($selectedSale->due_amount, 2) }}</strong></span>
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
        function printSaleReceipt() {
            const receiptContent = document.querySelector('#saleReceiptContent')?.innerHTML || '';
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

        window.addEventListener('open-sale-modal', event => {
            var modal = new bootstrap.Modal(document.getElementById('saleDetailModal'));
            modal.show();
        });

        function printManualSalesTable() {
            // Clone the current table and remove the Actions column
            const tableElement = document.querySelector('.table.table-hover').cloneNode(true);
            const actionColumnIndex = 8; // 0-based index; Actions is the 9th column
            const headerRow = tableElement.querySelector('thead tr');
            const headerCells = headerRow ? headerRow.querySelectorAll('th') : [];
            if (headerCells.length > actionColumnIndex) {
                headerCells[actionColumnIndex].remove();
            }
            const rows = tableElement.querySelectorAll('tbody tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length > actionColumnIndex) {
                    cells[actionColumnIndex].remove();
                }
            });

            // Create a temporary print container
            const containerId = 'manualSalesPrint';
            let container = document.getElementById(containerId);
            if (container) container.remove();
            container = document.createElement('div');
            container.id = containerId;
            container.style.padding = '20px';
            container.innerHTML = `
                <div class="text-center mb-4" style="text-align:center; margin-bottom:16px;">
                    <h3 class="mb-1 fw-bold tracking-tight" style="color: #9d1c20; margin:0;">PLUS</h3>
                    <p class="mb-0 text-muted small" style="color: #6B7280; margin:0;">NO 20/2/1, 2nd FLOOR,HUNTER BUILDING,BANKSHALLL STREET,COLOMBO-11</p>
                    <p class="mb-0 text-muted small" style="color: #6B7280; margin:0;">Phone: 011 - 2332786 | Email: plusaccessories.lk@gmail.com</p>
                </div>
                <div style="margin-bottom: 12px; padding-bottom: 8px; display:flex; justify-content:space-between; align-items:center;">
                    <h2 style="color:#9d1c20; margin:0;">Manual Sales Records</h2>
                    <small>Generated on ${new Date().toLocaleString('en-US', { timeZone: 'Asia/Colombo' })}</small>
                </div>
            `;
            const wrapper = document.createElement('div');
            wrapper.appendChild(tableElement);
            container.appendChild(wrapper);
            document.body.appendChild(container);

            window.print();
            setTimeout(() => {
                container.remove();
            }, 500);
        }
    </script>
    @endpush
</div>
