<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-header-modern mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Return Items</h3>
                        <p class="mb-0">Manage and track all returned products</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-sm fw-semibold text-gray-600 mb-1">Total Returns</h6>
                            <h3 class="fw-bold text-gray-800">{{ $stats['total_returns'] }}</h3>
                        </div>
                        <div class="icon-wrapper" style="background-color: #f3f4f6; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-arrow-counterclockwise text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-sm fw-semibold text-gray-600 mb-1">Total Return Amount</h6>
                            <h3 class="fw-bold text-gray-800">Rs.{{ number_format($stats['total_return_amount'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="icon-wrapper" style="background-color: #fef3c7; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-cash text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-sm fw-semibold text-gray-600 mb-1">Today's Returns</h6>
                            <h3 class="fw-bold text-gray-800">{{ $stats['today_returns'] }}</h3>
                        </div>
                        <div class="icon-wrapper" style="background-color: #dbeafe; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-calendar-check text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h6 class="text-sm fw-semibold text-gray-600 mb-1">Today's Amount</h6>
                            <h3 class="fw-bold text-gray-800">Rs.{{ number_format($stats['today_return_amount'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="icon-wrapper" style="background-color: #dcfce7; border-radius: 50%; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-graph-up text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card animate-slide-in">
                <!-- Search & Actions Bar -->
                <div class="card-body border-bottom" style="background-color: #f8f9fa;">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6">
                            <div class="search-box-modern">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search text-primary-custom"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control"
                                        placeholder="Search by invoice, customer name, product, email or phone..."
                                        wire:model.live.debounce.300ms="search"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 text-lg-end">
                            <div class="btn-group" role="group">
                                <button wire:click="exportToCSV" class="btn-modern btn-secondary-modern me-2">
                                    <i class="bi bi-download me-1"></i> Export CSV
                                </button>
                                <button wire:click="printData" class="btn-modern btn-primary-modern">
                                    <i class="bi bi-printer me-1"></i> Print
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Results Pagination Control -->
                <div class="card-body border-bottom p-3" style="background-color: #f8f9fa; display: flex; justify-content: space-between; align-items: center;">
                    <small class="text-muted">
                        Showing <strong>{{ $returns->count() }}</strong> of <strong>{{ $returns->total() }}</strong> results
                    </small>
                    <div>
                        <label class="form-label mb-0 me-2" style="display: inline;">Items per page:</label>
                        <select wire:model.live="perPage" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>

                <!-- Table Content -->
                @if($returns->count())
                <div class="table-modern">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th class="text-center ps-4 py-3">ID</th>
                                    <th class="text-center py-3">Invoice</th>
                                    <th class="text-center py-3">Customer</th>
                                    <th class="text-center py-3">Product</th>
                                    <th class="text-center py-3">Qty</th>
                                    <th class="text-center py-3">Unit Price</th>
                                    <th class="text-center py-3">Total Amount</th>
                                    <th class="text-center py-3">Return Date</th>
                                    <th class="text-center py-3">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returns as $index => $return)
                                <tr class="transition-all hover:bg-gray-50">
                                    <td class="text-sm text-center ps-4 py-3">
                                        {{ $returns->firstItem() + $index }}
                                    </td>
                                    <td class="text-sm text-center py-3" data-label="Invoice">
                                        <span class="badge" style="background-color: #e0e7ff; color: #4f46e5; font-weight: 600;">
                                            {{ $return->invoice_number }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-center py-3" data-label="Customer">
                                        <div class="fw-500">{{ $return->customer_name }}</div>
                                        <small class="text-muted">{{ $return->email }}</small>
                                    </td>
                                    <td class="text-sm text-center py-3" data-label="Product">
                                        <div class="fw-500">{{ $return->product_name }}</div>
                                        <small class="text-muted">{{ $return->product_code }}</small>
                                    </td>
                                    <td class="text-sm text-center py-3" data-label="Qty">
                                        <span class="badge" style="background-color: #fef3c7; color: #92400e; font-weight: 600;">
                                            {{ $return->return_quantity }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-center py-3" data-label="Unit Price">
                                        Rs.{{ number_format($return->selling_price, 2) }}
                                    </td>
                                    <td class="text-sm text-center py-3 fw-bold" data-label="Total Amount">
                                        <span style="color: #9d1c20;">
                                            Rs.{{ number_format($return->total_amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-sm text-center py-3" data-label="Return Date">
                                        {{ \Carbon\Carbon::parse($return->created_at)->format('d M Y') }}
                                    </td>
                                    <td class="text-sm text-center py-3" data-label="Notes">
                                        @if($return->notes)
                                        <span class="d-inline-block text-truncate" style="max-width: 150px;" title="{{ $return->notes }}">
                                            {{ $return->notes }}
                                        </span>
                                        @else
                                        <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-6">
                                        <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                                            <i class="bi bi-inbox text-gray-600 fs-3"></i>
                                        </div>
                                        <h5 class="text-gray-600 fw-normal">No Return Items Found</h5>
                                        <p class="text-sm text-gray-500 mb-0">No matching results found for the current search.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="mt-3 px-4">
                    {{ $returns->links('livewire.custom-pagination') }}
                </div>
                @else
                <div class="text-center py-6">
                    <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto;margin-bottom:12px;">
                        <i class="bi bi-inbox text-gray-600 fs-3"></i>
                    </div>
                    <h5 class="text-gray-600 fw-normal">No Return Items Data Found</h5>
                    <p class="text-sm text-gray-500 mb-0">All return items are currently empty.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
    @include('components.admin-styles')
    <link href="https://cdn.jsdelivr.net/npm/animate.css@4.1.1/animate.min.css" rel="stylesheet">
    <style>
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }

        /* Print styles */
        .print-only {
            display: none;
        }

        @media print {
            body * {
                visibility: hidden;
            }

            #mainReturnTablePrint,
            #mainReturnTablePrint * {
                visibility: visible;
            }

            #mainReturnTablePrint {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            @page {
                size: auto;
                margin: 10mm;
            }

            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            * {
                color: #000 !important;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Print function
        document.addEventListener('livewire:initialized', function() {
            Livewire.on('print-return-table', function() {
                // Clone the current table
                const tableElement = document.querySelector('.table.table-hover').cloneNode(true);

                // Create a temporary print container
                const containerId = 'mainReturnTablePrint';
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
                        <h2 style="color:#9d1c20; margin:0;">Return Items Report</h2>
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
    </script>
    @endpush
</div>