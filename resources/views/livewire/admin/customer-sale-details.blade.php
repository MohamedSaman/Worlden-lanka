<div class="container-fluid py-6 bg-gray-50 min-vh-100 transition-colors duration-300">
    <div class="card border-0 ">
        <!-- Card Header -->
        <div class="card-header text-white p-2  d-flex align-items-center"
            style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%); border-radius: 20px 20px 0 0;">
            <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center me-3">
                <i class="bi bi-people fs-4 text-white" aria-hidden="true"></i>
            </div>
            <div>
                <h3 class="mb-1 fw-bold tracking-tight text-white">Customer Sales Details</h3>
                <p class="text-white opacity-80 mb-0 text-sm">Monitor and manage your Customer Sales Records</p>
            </div>
        </div>
        <div class="card-header bg-transparent pb-4 mt-2 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <!-- Middle: Search Bar -->
            <div class="flex-grow-1 d-flex justify-content-lg">
                <div class="input-group " style="max-width: 600px;">
                    <span class="input-group-text bg-gray-100 border-0 px-3">
                        <i class="bi bi-search text-danger"></i>
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
                    style="color: #fff; background-color: #9d1c20; border: 1px solid #9d1c20;">
                    <i class="bi bi-download me-1" aria-hidden="true"></i> Export CSV
                </button>
                <button wire:click="printData"
                    class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                    aria-label="Print customer sales details"
                    style="color: #fff; background-color: #9d1c20; border: 1px solid #9d1c20;">
                    <i class="bi bi-printer me-1" aria-hidden="true"></i> Print
                </button>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-1  pt-5 bg-transparent">
            <!-- Sales Table or Empty State -->
            @if($customerSales->count())
            <div class="table-responsive  shadow-sm rounded-2 overflow-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th class="text-center ps-4 py-3">ID</th>
                            <th class="text-center py-3">Customer Name</th>
                            <th class="text-center py-3">Total Sales</th>
                            <th class="text-center py-3">Current Paid Amount</th>
                            <th class="text-center py-3">Current Due</th>
                            <th class="text-center py-3">Brought-Forward Due</th>
                            <th class="text-center py-3">Total Due Amount</th>
                            <th class="text-center py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customerSales as $index => $customer)
                        <tr class="transition-all hover:bg-gray-50">
                            <td class="text-sm text-center  ps-4 py-3">
                                {{ $customerSales->firstItem() + $index }}
                            </td>
                            <td class="text-sm text-center py-3" data-label="Customer Name">{{ $customer->name }}</td>
                            <td class="text-sm text-center py-3 text-gray-800" data-label="Total Sales">Rs.{{ number_format($customer->total_sales ?? 0, 2) }}</td>
                            <td class="text-sm text-center py-3">
                                <span class="badge"
                                    style="background-color:#22c55e;
                                             color: #ffffff; padding: 6px 12px; border-radius: 9999px; font-weight: 600;">
                                    Rs.{{ number_format($customer->total_paid ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="text-sm text-center py-3">
                                <span class="badge"
                                    style="background-color: {{ $customer->total_due > 0 ? '#ef4444' : '#22c55e' }};
                                             color: #ffffff; padding: 6px 12px; border-radius: 9999px; font-weight: 600;">
                                    Rs.{{ number_format($customer->total_due ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="text-sm text-center py-3">
                                <span class="badge"
                                    style="background-color: {{ $customer->total_back_forward_amount > 0 ? '#ef4444' : '#22c55e' }};
                                             color: #ffffff; padding: 6px 12px; border-radius: 9999px; font-weight: 600;">
                                    Rs.{{ number_format($customer->total_back_forward_amount ?? 0, 2) }}
                                </span>
                            </td>
                            <td class="text-sm text-center py-3">
                                <span class="badge"
                                    style="background-color:#ef4444;
                                             color: #ffffff; padding: 6px 12px; border-radius: 9999px; font-weight: 600;">
                                    Rs.{{ number_format(($customer->total_back_forward_amount ?? 0) + ($customer->total_due ?? 0), 2) }}
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
                    style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%);">
                    <h5 class="modal-title fw-bold tracking-tight" id="customerSalesModalLabel">
                        <i class="bi bi-person me-2"></i>
                        {{ $modalData ? $modalData['customer']->name . '\'s Sales History' : 'Sales History' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Print-only company header for modal prints -->
                    <div class="print-only text-center mb-4" style="text-align:center; margin-bottom:16px;">
                        <h3 class="mb-1 fw-bold tracking-tight" style="color: #9d1c20; margin:0;">PLUS</h3>
                        <p class="mb-0 text-muted small" style="color: #6B7280; margin:0;">NO 20/2/1, 2nd FLOOR,HUNTER BUILDING,BANKSHALLL STREET,COLOMBO-11</p>
                        <p class="mb-0 text-muted small" style="color: #6B7280; margin:0;">Phone: 011 - 2332786 | Email: plusaccessories.lk@gmail.com</p>
                    </div>
                    @if($modalData)
                    <!-- Customer Information Section -->
                    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
                        <div class="card-body p-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2 text-sm text-gray-800"><strong>Name:</strong> {{ $modalData['customer']->name }}</p>
                                    <p class="mb-2 text-sm text-gray-800"><strong>Email:</strong> {{ $modalData['customer']->email }}</p>
                                    <p class="mb-2 text-sm text-gray-800"><strong>Phone:</strong> {{ $modalData['customer']->phone }}</p>
                                    <p class="mb-2 text-sm text-gray-800"><strong>Type:</strong>
                                        <span class="badge"
                                            style="background-color: {{ $modalData['customer']->type == 'wholesale' ? '#9d1c20' : '#d34d51ff' }};
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
                    <div class="row mb-4 no-print g-3">
                        <!-- Total Sales Card -->
                        @php $accountTotalDue = $modalData['accountTotals']['total_due'] ?? 0; @endphp
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-4">
                                    <h6 class="text-sm fw-semibold text-gray-800 mb-2" style="color: #9d1c20;">Total Sales</h6>
                                    <h3 class="fw-bold text-gray-800">Rs.{{ number_format($modalData['salesSummary']->total_amount, 2) }}</h3>
                                    @php
                                    $todaySales = collect($modalData['invoices'])->where('created_at', '>=', now()->startOfDay());
                                    $todaySalesAmount = $todaySales->sum('total_amount');
                                    $todayInvoiceCount = $todaySales->count();
                                    @endphp

                                    <p class="text-sm text-gray-500 mb-0">Today Invoices: {{ $todayInvoiceCount }} invoices</p>
                                    <p class="text-sm text-gray-500 mb-0">
                                        Today Sales Amount: Rs.{{ number_format($todaySalesAmount, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Paid Card -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-4">
                                    <h6 class="text-sm fw-semibold text-gray-800 mb-2" style="color: #22c55e;">Total Paid</h6>
                                    <h3 class="fw-bold" style="color: #22c55e;">Rs.{{ number_format($modalData['paymentSums']['paid'] ?? 0, 2) }}</h3>
                                    <p class="text-xs text-gray-500 mb-0">
                                        Current Paid: Rs.{{ number_format($modalData['paymentSums']['current'] ?? 0, 2) }}
                                    </p>
                                    <p class="text-xs text-gray-500 mb-0">Brought-Forward Paid: Rs.{{ number_format($modalData['paymentSums']['forward'] ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Total Due Card -->
                        <div class="col-md-4 col-sm-6">
                            <div class="card border-0 shadow-sm rounded-4 h-100">
                                <div class="card-body text-center p-4">
                                    <h6 class="text-sm fw-semibold text-gray-800 mb-2" style="color: #ef4444;">Total Due</h6>
                                    <h3 class="fw-bold" style="color: #ef4444;">Rs.{{ number_format($accountTotalDue, 2) }}</h3>
                                    <p class="text-sm text-gray-500 mb-0">
                                        Current Due: Rs.{{ number_format($modalData['accountTotals']['current_due'] ?? 0, 2) }}

                                    </p>
                                    <p class="text-sm text-gray-500 mb-0">Brought-Forward: Rs.{{ number_format($modalData['accountTotals']['back_forward_due'] ?? 0, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>


                    <!-- Payment Progress Bar -->
                    @php
                    $paid = $modalData['paymentSums']['paid'] ?? 0;
                    $due = $accountTotalDue ?? 0;

                    $denominator = $due + $paid;

                    $paymentPercentage = $denominator > 0 ? round(($paid / $denominator) * 100) : 0;
                    @endphp
                    <div class="card border-0 shadow-sm rounded-4 mb-4 no-print">
                        <div class="card-body p-4">
                            <p class="fw-bold mb-2 text-sm text-gray-800" style="color: #9d1c20;">Payment Progress</p>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1" style="height: 10px;">
                                    <div class="progress-bar"
                                        role="progressbar"
                                        style="background-color: #9d1c20; width: {{ $paymentPercentage }}%;"
                                        aria-valuenow="{{ $paymentPercentage }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100">
                                    </div>
                                </div>
                                <span class="ms-3 fw-bold text-sm text-gray-800">{{ $paymentPercentage }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Summary (Brought-Forward, Invoices, and Paid) -->
                    <div class="card border-0 shadow-sm rounded-4">
                        <div class="card-header p-4" style="background-color: #eff6ff;">
                            <h5 class="card-title mb-0 fw-bold text-sm" style="color: #9d1c20;">Sales Summary</h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="position-sticky top-0" style="background-color: #eff6ff;">
                                        <tr>
                                            <th class="ps-4 text-uppercase text-xs fw-semibold py-3 text-center" style="color: #9d1c20;">No</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3" style="color: #9d1c20;">Description</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3" style="color: #9d1c20;">Date</th>
                                            <th class="text-uppercase text-xs fw-semibold py-3 text-end" style="color: #9d1c20;">Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $grandTotal = 0; @endphp
                                        @forelse($modalData['invoiceSummaryRows'] ?? [] as $row)
                                        @php
                                        $amt = floatval($row['amount'] ?? 0);
                                        if (($row['type'] ?? '') === 'paid') {
                                        $grandTotal -= $amt; // subtract paid amounts
                                        } else {
                                        $grandTotal += $amt; // add back-forward and invoice amounts
                                        }
                                        @endphp
                                        <tr class="border-bottom transition-all hover:bg-[#f1f5f9] {{ $loop->iteration % 2 == 0 ? 'bg-[#f9fafb]' : '' }}">
                                            <td class="ps-4 text-center text-sm text-gray-800">{{ $loop->iteration }}</td>
                                            <td class="text-sm text-gray-600">{{ $row['description'] }}</td>
                                            <td class="text-sm text-gray-600">
                                                @if(!empty($row['date']))
                                                {{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}
                                                @else
                                                —
                                                @endif
                                            </td>
                                            <td class="text-end fw-bold text-sm text-gray-800">
                                                @if(($row['type'] ?? '') === 'paid')
                                                <span style="color: #ef4444;">({{ number_format($row['amount'] ?? 0, 2) }})</span>
                                                @else
                                                {{ number_format($row['amount'] ?? 0, 2) }}
                                                @endif
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-6"> Not found Payment Records </td>
                                        </tr>
                                        @endforelse
                                        @php $accountTotalDue = $modalData['accountTotals']['total_due'] ?? 0; @endphp
                                        <tr class="bg-[#f1f5f9] fw-bold">
                                            <td colspan="3" class="text-end text-gray-800">Balance Total Due Amount</td>
                                            <td class="text-end text-gray-800">{{ number_format($accountTotalDue, 2) }}</td>
                                        </tr>
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
                        wire:click="exportModalToCSV" aria-label="Export sales summary to CSV">
                        <i class="bi bi-download me-1"></i> Export CSV
                    </button>
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
<style>
    /* Hidden by default, visible only in print */
    .print-only {
        display: none;
    }

    /* Print only the modal content */
    @media print {
        body * {
            visibility: hidden !important;
        }

        #customerSalesModal,
        #customerSalesModal *,
        #mainCustomerSalesPrint,
        #mainCustomerSalesPrint * {
            visibility: visible !important;
        }

        .print-only {
            display: block !important;
        }

        #customerSalesModal,
        #mainCustomerSalesPrint {
            position: absolute !important;
            left: 0 !important;
            top: 0 !important;
            width: 100% !important;
        }

        .modal-backdrop {
            display: none !important;
        }

        .modal-dialog {
            max-width: 100% !important;
            margin: 0 !important;
        }

        .modal-content {
            border: none !important;
            box-shadow: none !important;
        }

        /* Optional: hide modal close button on print */
        .modal-header .btn-close,
        .no-print {
            display: none !important;
        }

        /* Hide modal footer (buttons) and any buttons inside modal content when printing */
        #customerSalesModal .modal-footer,
        #customerSalesModal button,
        #customerSalesModal .btn {
            display: none !important;
        }

        /* Print entire modal content without internal scrollbars */
        @page {
            size: auto;
            margin: 10mm;
        }

        html,
        body {
            height: auto !important;
            overflow: visible !important;
        }

        #customerSalesModal .modal-body,
        #customerSalesModal .card-body,
        #customerSalesModal .table-responsive,
        #mainCustomerSalesPrint .table-responsive {
            max-height: none !important;
            overflow: visible !important;
        }

        #customerSalesModal thead,
        #mainCustomerSalesPrint thead {
            position: static !important;
            /* disable sticky header for print */
        }

        /* Avoid breaking rows across pages */
        #customerSalesModal table,
        #mainCustomerSalesPrint table {
            page-break-inside: auto;
        }

        #customerSalesModal tr,
        #mainCustomerSalesPrint tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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
            // Clone the current table and remove the Action column
            const tableElement = document.querySelector('.table.table-hover').cloneNode(true);
            const actionColumnIndex = 7; // 0-based index; Action is the 8th column
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

            // Create a temporary print container in the current page
            const containerId = 'mainCustomerSalesPrint';
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
                    <h2 style="color:#9d1c20; margin:0;">Customer Sales Details</h2>
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
        });
    });

    // Modal print function
    function printModalContent() {
        // Directly trigger the browser print dialog without opening a new window
        window.print();
    }
</script>
@endpush