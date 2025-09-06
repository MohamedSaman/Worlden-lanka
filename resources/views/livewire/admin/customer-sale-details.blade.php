<div class="container-fluid py-6 bg-gray-50 min-vh-100 transition-colors duration-300">
    <div class="card border-0 ">
        <!-- Card Header -->
        <div class="card-header bg-transparent pb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">

            <!-- Left: Icon + Title -->
            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <div class="icon-shape icon-lg bg-opacity-25 p-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-people fs-4" aria-hidden="true" style="color:#233D7F;"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-bold tracking-tight text-dark">Customer Sales Details</h3>
                    <p class="text-dark opacity-80 mb-0 text-sm">Monitor and manage your Customer Sales Records</p>
                </div>
            </div>

            <!-- Middle: Search Bar -->
            <div class="flex-grow-1 d-flex justify-content-lg-center">
                <div class="input-group " style="max-width: 400px;">
                    <span class="input-group-text bg-gray-100 border-0 px-3">
                        <i class="bi bi-search text-primary"></i>
                    </span>
                    <input type="text"
                        class="form-control "
                        placeholder="Search customers..."
                        wire:model.live.debounce.300ms="search"
                        autocomplete="off">
                </div>
            </div>

            <!-- Right: Buttons -->
            <div class="d-flex gap-2 flex-shrink-0 justify-content-lg-end">
                <button wire:click="exportToCSV"
                    class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                    aria-label="Export customer sales to CSV"
                    style="color: #fff; background-color: #233D7F; border: 1px solid #233D7F;">
                    <i class="bi bi-download me-1" aria-hidden="true"></i> Export CSV
                </button>
                <button wire:click="printData"
                    class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                    aria-label="Print customer sales details"
                    style="color: #fff; background-color: #233D7F; border: 1px solid #233D7F;">
                    <i class="bi bi-printer me-1" aria-hidden="true"></i> Print
                </button>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-1  pt-5 bg-transparent">

            <!-- Sales Table or Empty State -->
            @if($customerSales->count())
            <div class="table-responsive  shadow-sm rounded-2 overflow-hidden">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th class="text-center ps-4 py-3">ID</th>
                            <th class="text-center py-3">Customer Name</th>
                            <th class="text-center py-3">Email</th>
                            <th class="text-center py-3">Type</th>
                            <th class="text-center py-3">Invoices</th>
                            <th class="text-center py-3">Total Sales</th>
                            <th class="text-center py-3">Total Paid</th>
                            <th class="text-center py-3">Total Due</th>
                            <th class="text-center py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerSales as $index => $customer)
                        <tr class="transition-all hover:bg-gray-50">
                            <td class="text-sm text-center  ps-4 py-3" >
                                {{ $customerSales->firstItem() + $index }}
                            </td>
                            <td class="text-sm text-center py-3" data-label="Customer Name">{{ $customer->name }}</td>
                            <td class="text-sm text-center py-3">{{ $customer->email }}</td>
                            <td class="text-sm text-center py-3">
                                {{ ucfirst($customer->type) }}
                            </td>
                            <td class="text-sm text-center py-3" >{{ $customer->invoice_count }}</td>
                            <td class="text-sm text-center py-3 text-gray-800" data-label="Total Sales">Rs.{{ number_format($customer->total_sales, 2) }}</td>
                            <td class="text-sm text-center py-3">
                                <span class="badge"
                                    style="background-color: {{ $customer->total_sales - $customer->total_due > 0 ? '#22c55e' : '#ef4444' }};
                                             color: #ffffff; padding: 6px 12px; border-radius: 9999px; font-weight: 600;">
                                    Rs.{{ number_format($customer->total_sales - $customer->total_due, 2) }}
                                </span>
                            </td>
                            <td class="text-sm text-center py-3">
                                <span class="badge"
                                    style="background-color: {{ $customer->total_due > 0 ? '#ef4444' : '#22c55e' }};
                                             color: #ffffff; padding: 6px 12px; border-radius: 9999px; font-weight: 600;">
                                    Rs.{{ number_format($customer->total_due, 2) }}
                                </span>
                            </td>
                            <td class="text-sm text-center py-3">
                                <button wire:click="viewSaleDetails({{ $customer->customer_id }})"
                                    class="btn text-primary btn-sm"
                                    aria-label="View customer sales details">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-6">
                                <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                                    <i class="bi bi-person text-gray-600 fs-3"></i>
                                </div>
                                <h5 class="text-gray-600 fw-normal">No Customer Sales Found</h5>
                                <p class="text-sm text-gray-500 mb-0">No matching results found for the current search.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $customerSales->links('livewire.custom-pagination') }}
            </div>
            @else
            <div class="text-center py-6">
                <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                    <i class="bi bi-person text-gray-600 fs-3"></i>
                </div>
                <h5 class="text-gray-600 fw-normal">No Customer Sales Data Found</h5>
                <p class="text-sm text-gray-500 mb-0">All customer sales records are empty.</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Customer Sale Details Modal -->
    <div wire:ignore.self class="modal fade" id="customerSalesModal" tabindex="-1" aria-labelledby="customerSalesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content rounded-4 shadow-lg">
                <div class="modal-header text-white p-4"
                    style="background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%);">
                    <h5 class="modal-title fw-bold tracking-tight" id="customerSalesModalLabel">
                        <i class="bi bi-person me-2"></i>
                        {{ $modalData ? $modalData['customer']->name . '\'s Sales History' : 'Sales History' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    @if($modalData)
                    <!-- Customer Information Section -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2 text-sm text-gray-800"><strong>Name:</strong> {{ $modalData['customer']->name }}</p>
                                    <p class="mb-2 text-sm text-gray-800"><strong>Email:</strong> {{ $modalData['customer']->email }}</p>
                                    <p class="mb-2 text-sm text-gray-800"><strong>Phone:</strong> {{ $modalData['customer']->phone }}</p>
                                    <p class="mb-2 text-sm text-gray-800"><strong>Type:</strong>
                                        <span class="badge"
                                            style="background-color: {{ $modalData['customer']->type == 'wholesale' ? '#1e40af' : '#0ea5e9' }};
                                                     color: #ffffff; padding: 6px 12px; border-radius: 9999px; font-weight: 600;">
                                            {{ ucfirst($modalData['customer']->type) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="mb-2 text-sm text-gray-800"><strong>Business Name:</strong> {{ $modalData['customer']->business_name ?? 'N/A' }}</p>
                                    <p class="mb-2 text-sm text-gray-800"><strong>Address:</strong> {{ $modalData['customer']->address ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sales Summary Cards -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-4">
                                    <h6 class="text-sm fw-semibold text-gray-800 mb-2" style="color: #1e3a8a;">Total Sales Amount</h6>
                                    <h3 class="fw-bold text-gray-800">Rs.{{ number_format($modalData['salesSummary']->total_amount, 2) }}</h3>
                                    <p class="text-sm text-gray-500 mb-0">Across {{ count($modalData['invoices']) }} invoices</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-4">
                                    <h6 class="text-sm fw-semibold text-gray-800 mb-2" style="color: #22c55e;">Amount Paid</h6>
                                    <h3 class="fw-bold" style="color: #22c55e;">Rs.{{ number_format($modalData['salesSummary']->total_paid, 2) }}</h3>
                                    <p class="text-sm text-gray-500 mb-0">
                                        {{ round(($modalData['salesSummary']->total_paid / $modalData['salesSummary']->total_amount) * 100) }}% of total
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-4">
                                    <h6 class="text-sm fw-semibold text-gray-800 mb-2" style="color: #ef4444;">Amount Due</h6>
                                    <h3 class="fw-bold" style="color: #ef4444;">Rs.{{ number_format($modalData['salesSummary']->total_due, 2) }}</h3>
                                    <p class="text-sm text-gray-500 mb-0">
                                        {{ round(($modalData['salesSummary']->total_due / $modalData['salesSummary']->total_amount) * 100) }}% outstanding
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Progress Bar -->
                    @php
                    $paymentPercentage = $modalData['salesSummary']->total_amount > 0
                    ? round(($modalData['salesSummary']->total_paid / $modalData['salesSummary']->total_amount) * 100)
                    : 0;
                    @endphp
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body p-4">
                            <p class="fw-bold mb-2 text-sm text-gray-800" style="color: #1e3a8a;">Payment Progress</p>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar"
                                        role="progressbar"
                                        style="background-color: #1e40af; width: {{ $paymentPercentage }}%;"
                                        aria-valuenow="{{ $paymentPercentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="ms-3 fw-bold text-sm text-gray-800">{{ $paymentPercentage }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product-wise Sales Table -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header p-4" style="background-color: #eff6ff;">
                            <h5 class="card-title mb-0 fw-bold text-sm" style="color: #1e3a8a;">Product-wise Sales</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="position-sticky top-0" style="background-color: #eff6ff;">
                                        <tr>
                                            <th class="ps-4 text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">#</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3" style="color: #1e3a8a;">Product</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3" style="color: #1e3a8a;">Invoice #</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3" style="color: #1e3a8a;">Date</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Quantity</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3 text-end" style="color: #1e3a8a;">Unit Price</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3 text-end" style="color: #1e3a8a;">Discount</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3 text-end" style="color: #1e3a8a;">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($modalData['productSales'] as $item)
                                        <tr class="border-bottom transition-all hover:bg-[#f1f5f9] {{ $loop->iteration % 2 == 0 ? 'bg-[#f9fafb]' : '' }}">
                                            <td class="ps-4 text-center text-sm text-gray-800" data-label="#">{{ $loop->iteration }}</td>
                                            <td class="text-sm" data-label="Product">
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <h6 class="fw-bold mb-1 text-gray-800">{{ $item->product_name }}</h6>
                                                        <small class="text-muted d-block">
                                                            {{ $item->product_brand ?? '' }} {{ $item->product_model ?? '' }}
                                                        </small>
                                                        <span class="badge" style="background-color: #f3f4f6; color: #1f2937; padding: 6px 12px; border-radius: 9999px;">
                                                            {{ $item->product_code }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-sm text-gray-600" data-label="Invoice #">{{ $item->invoice_number }}</td>
                                            <td class="text-sm text-gray-600" data-label="Date">{{ \Carbon\Carbon::parse($item->sale_date)->format('d M Y') }}</td>
                                            <td class="text-center text-sm text-gray-800" data-label="Quantity">{{ $item->total_quantity }}</td>
                                            <td class="text-end text-sm text-gray-800" data-label="Unit Price">Rs.{{ number_format($item->price, 2) }}</td>
                                            <td class="text-end text-sm text-gray-800" data-label="Discount">Rs.{{ number_format($item->discount, 2) ?: 0 }}</td>
                                            <td class="text-end fw-bold text-sm text-gray-800" data-label="Total">Rs.{{ number_format($item->total_sales, 2) }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-center py-6">
                                                <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                                                    <i class="bi bi-box-seam text-gray-600 fs-3"></i>
                                                </div>
                                                <h5 class="text-gray-600 fw-normal">No Product Sales Found</h5>
                                                <p class="text-sm text-gray-500 mb-0">No product sales data available.</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                            <i class="bi bi-person text-gray-600 fs-3"></i>
                        </div>
                        <h5 class="text-gray-600 fw-normal">Loading Customer Sales Data</h5>
                        <p class="text-sm text-gray-500 mb-0">Please wait while data is being loaded...</p>
                    </div>
                    @endif
                </div>
                <div class="modal-footer p-4">
                    <button type="button" class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                        onclick="printModalContent()" aria-label="Print customer sales details">
                        <i class="bi bi-printer me-1"></i> Print Details
                    </button>
                    <button type="button" class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                        data-bs-dismiss="modal" aria-label="Close modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><script>
    // Toast notifications for Livewire events
    document.addEventListener('livewire:initialized', () => {
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
    });

    // Modal opening
    window.addEventListener('open-customer-sale-details-modal', event => {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('customerSalesModal'));
            modal.show();
        }, 500);
    });

    // Main table print function
    document.addEventListener('livewire:initialized', function() {
        Livewire.on('print-customer-table', function() {
            const printWindow = window.open('', '_blank', 'width=1000,height=700');
            const tableElement = document.querySelector('.table.table-hover').cloneNode(true);
            const actionColumnIndex = 8;
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
                    <title>Customer Sales Details - Print Report</title>
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
                            <h2 class="fw-bold tracking-tight">Customer Sales Details</h2>
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

    // Modal print function
    function printModalContent() {
        const customerName = document.querySelector('#customerSalesModalLabel').innerText.trim();
        const modalBody = document.querySelector('.modal-body');
        const customerInfoSection = modalBody.querySelector('.card.mb-4');
        const customerDetails = {};
        customerInfoSection.querySelectorAll('p').forEach(p => {
            const text = p.innerText;
            if (text.includes(':')) {
                const [key, value] = text.split(':');
                customerDetails[key.trim()] = value.trim();
            }
        });

        const summaryCards = modalBody.querySelectorAll('.row.mb-4 .card-body');
        const summaryData = {
            totalSales: summaryCards[0]?.querySelector('h3')?.innerText || '0',
            totalPaid: summaryCards[1]?.querySelector('h3')?.innerText || '0',
            totalDue: summaryCards[2]?.querySelector('h3')?.innerText || '0',
        };

        const progressBar = modalBody.querySelector('.progress-bar');
        const paymentPercentage = progressBar?.getAttribute('aria-valuenow') || '0';
        const productTable = modalBody.querySelector('.table');

        const htmlContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>${customerName}</title>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
                <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
                <style>
                    @page { size: landscape; margin: 1cm; }
                    body { font-family: 'Inter', sans-serif; padding: 20px; font-size: 14px; color: #1f2937; }
                    .print-container { max-width: 900px; margin: 0 auto; }
                    .print-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #1e40af; text-align: center; }
                    .print-header h2 { color: #1e40af; font-weight: 700; letter-spacing: -0.025em; }
                    .print-footer { margin-top: 20px; padding-top: 15px; border-top: 2px solid #e5e7eb; text-align: center; font-size: 12px; color: #6b7280; }
                    .customer-info { border: 1px solid #e5e7eb; border-radius: 1rem; padding: 15px; margin-bottom: 20px; background: #f9fafb; }
                    .summary-row { display: flex; justify-content: space-between; margin-bottom: 20px; }
                    .summary-card { width: 31%; border: 1px solid #e5e7eb; border-radius: 1rem; padding: 15px; text-align: center; background: #ffffff; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); }
                    .progress-container { border: 1px solid #e5e7eb; border-radius: 1rem; padding: 15px; margin-bottom: 20px; background: #ffffff; }
                    .progress-bar-container { height: 10px; background-color: #e5e7eb; border-radius: 5px; margin: 10px 0; }
                    .progress-bar-fill { height: 100%; background-color: #1e40af; border-radius: 5px; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                    th, td { border: 1px solid #e5e7eb; padding: 12px; text-align: left; vertical-align: middle; }
                    th { background-color: #eff6ff; font-weight: 600; text-transform: uppercase; color: #1e3a8a; }
                    tr:nth-child(even) { background-color: #f9fafb; }
                    tr:hover { background-color: #f1f5f9; }
                    .text-right { text-align: right; }
                    .text-center { text-align: center; }
                    .font-bold { font-weight: 600; }
                    .badge { padding: 6px 12px; border-radius: 9999px; font-size: 0.875rem; font-weight: 600; color: #ffffff; }
                    .bg-primary { background-color: #1e40af; }
                    .bg-info { background-color: #0ea5e9; }
                    .bg-light { background-color: #f3f4f6; color: #1f2937; }
                    .print-btn { display: block; margin: 20px auto; padding: 10px 20px; background: #1e40af; color: #ffffff; border: none; border-radius: 9999px; cursor: pointer; }
                    @media print {
                        .print-btn { display: none; }
                        body { -webkit-print-color-adjust: exact; color-adjust: exact; }
                        table { -webkit-print-color-adjust: exact; color-adjust: exact; }
                    }
                </style>
            </head>
            <body>
                <div class="print-container">
                    <div class="print-header">
                        <h2 class="fw-bold tracking-tight">${customerName}</h2>
                        <p>Generated on: ${new Date().toLocaleString('en-US', { timeZone: 'Asia/Colombo' })}</p>
                    </div>
                    <div class="customer-info">
                        <div style="display: flex; flex-wrap: wrap;">
                            <div style="width: 50%;">
                                <p><strong>Name:</strong> ${customerDetails['Name'] || 'N/A'}</p>
                                <p><strong>Email:</strong> ${customerDetails['Email'] || 'N/A'}</p>
                                <p><strong>Phone:</strong> ${customerDetails['Phone'] || 'N/A'}</p>
                                <p><strong>Type:</strong> <span class="badge bg-primary">${customerDetails['Type'] || 'N/A'}</span></p>
                            </div>
                            <div style="width: 50%;">
                                <p><strong>Business Name:</strong> ${customerDetails['Business Name'] || 'N/A'}</p>
                                <p><strong>Address:</strong> ${customerDetails['Address'] || 'N/A'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="summary-row">
                        <div class="summary-card">
                            <h5 style="color: #1e3a8a;">Total Sales Amount</h5>
                            <h3>${summaryData.totalSales}</h3>
                        </div>
                        <div class="summary-card">
                            <h5 style="color: #22c55e;">Amount Paid</h5>
                            <h3>${summaryData.totalPaid}</h3>
                        </div>
                        <div class="summary-card">
                            <h5 style="color: #ef4444;">Amount Due</h5>
                            <h3>${summaryData.totalDue}</h3>
                        </div>
                    </div>
                    <div class="progress-container">
                        <p class="font-bold" style="color: #1e3a8a;">Payment Progress</p>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: ${paymentPercentage}%;"></div>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>0%</span>
                            <span>${paymentPercentage}%</span>
                            <span>100%</span>
                        </div>
                    </div>
                    <h5 style="color: #1e3a8a;">Product-wise Sales</h5>
                    ${productTable ? productTable.outerHTML : '<p>No product sales data available</p>'}
                    <div class="print-footer">
                        <p>WORLDEN LANKA | NO 20/2/1, 2nd FLOOR,HUNTER BUILDING,BANKSHALLL STREET,COLOMBO-11 | Phone: 011 - 2332786 | Email: plusaccessories.lk@gmail.com</p>
                    </div>
                    <button class="print-btn" onclick="window.print(); setTimeout(() => window.close(), 500);">
                        Print Report
                    </button>
                </div>
            </body>
            </html>
        `;

        const printWindow = window.open('', '_blank', 'width=1000,height=800');
        printWindow.document.open();
        printWindow.document.write(htmlContent);
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.focus();
        };
    }
</script>
@endpush