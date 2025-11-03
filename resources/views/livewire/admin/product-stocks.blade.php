<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-header-modern mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Product Stock Details</h3>
                        <p class="mb-0">Monitor and manage your product inventorys</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card animate-slide-in">
                <!-- Search & Actions -->
                <div class="card-body border-bottom" style="background-color: #f8f9fa;">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6">
                            <div class="search-box-modern">
                                <i class="bi bi-search"></i>
                                <input type="text"
                                    class="form-control"
                                    placeholder="Search products..."
                                    wire:model.live.debounce.300ms="search"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex gap-2 justify-content-lg-end">
                                <button wire:click="exportToCSV"
                                    class="btn btn-modern btn-secondary-modern">
                                    <i class="bi bi-download me-1"></i>
                                    <span class="d-none d-sm-inline">Export </span>CSV
                                </button>
                                <button id="printButton"
                                    class="btn btn-modern btn-primary-modern">
                                    <i class="bi bi-printer me-1"></i> Print
                                </button>
                                <button wire:click="toggleShowAll"
                                    class="btn btn-modern btn-outline-modern">
                                    @if($showAll)
                                    <i class="bi bi-list-nested me-1"></i>
                                    <span class="d-none d-sm-inline">Show </span>Paginated
                                    @else
                                    <i class="bi bi-list-task me-1"></i>
                                    <span class="d-none d-sm-inline">Show </span>All
                                    @endif
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="table-modern">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">ID</th>
                                <th class=" py-3" style="min-width: 150px;">Product</th>
                                <th class="text-center py-3 d-none d-sm-table-cell" style="min-width: 120px;">Code</th>
                                <th class="text-center py-3 d-none d-lg-table-cell" style="min-width: 100px;">Category</th>
                                <th class="text-center py-3 d-none d-md-table-cell" style="min-width: 80px;">Sold</th>
                                <th class="text-center py-3" style="min-width: 100px;">Stock</th>
                                <th class="text-center py-3 d-none d-sm-table-cell" style="min-width: 80px;">Damage</th>
                                <th class="text-center py-3 d-none d-lg-table-cell" style="min-width: 80px;">Total</th>
                                <th class="text-center py-3 d-none d-lg-table-cell" style="min-width: 80px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $isPaginated = $products instanceof \Illuminate\Pagination\AbstractPaginator;
                            @endphp
                            @forelse($products as $index => $product)
                            <tr class="transition-all hover:bg-gray-50">
                                <td class="text-sm text-center ps-2 ps-md-4 py-3 d-none d-md-table-cell">
                                    {{ $isPaginated ? ($products->firstItem() + $index) : ($loop->iteration) }}
                                </td>
                                <td class="text-sm py-3">
                                    <div class="fw-medium text-center d-none d-sm-table-cell">{{ $product->product_name }}</div>
                                    <div class="d-flex align-items-center">
                                        <div class="text-start ms-4">
                                            <div class="fw-medium d-md-none text-muted small">{{ $product->product_name }}</div>
                                            <!-- Mobile info -->
                                            <div class="d-md-none text-muted small">
                                                Code: {{ $product->product_code }}<br>
                                                <span class="d-lg-none">{{ $product->category?->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-sm text-center py-3 d-none d-sm-table-cell">{{ $product->product_code }}</td>
                                <td class="text-sm text-center py-3 d-none d-lg-table-cell">{{ $product->category?->name ?? 'N/A' }}</td>
                                <td class="text-sm text-center py-3 d-none d-md-table-cell">{{ $product->sold }}</td>
                                <td class="text-sm text-center py-3">
                                    <span class="badge"
                                        style="background-color: {{ $product->stock_quantity > 0 ? '#22c55e' : '#ef4444' }};
                                             color: #ffffff; padding: 4px 8px; border-radius: 12px; font-weight: 600; font-size: 0.75rem;">
                                        {{ $product->stock_quantity }}
                                    </span>
                                    <!-- Mobile additional info -->
                                    <div class="d-md-none mt-1 text-muted small">
                                        Sold: {{ $product->sold }} | Damage: {{ $product->damage_quantity }}
                                    </div>
                                </td>
                                <td class="text-sm text-center py-3 d-none d-sm-table-cell">{{ $product->damage_quantity }}</td>
                                <td class="text-sm text-center py-3 d-none d-lg-table-cell">
                                    {{ $product->sold + $product->stock_quantity + $product->damage_quantity }}
                                </td>
                                <td class="text-sm text-center py-3">
                                    <button wire:click="viewProductSales({{ $product->id }})"
                                        class="btn text-primary btn-sm"
                                        aria-label="View customer sales details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>

                            </tr>
                            @empty
                            <tr>
                                <td class="text-sm text-center py-5 w-100" colspan="9">
                                    <div class="d-flex flex-column align-items-center justify-content-center" style="min-height: 200px;">
                                        <div style="width:72px;height:72px;background-color:#f3f4f6;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:16px;">
                                            <i class="bi bi-box-seam text-gray-600 fs-3"></i>
                                        </div>
                                        <h5 class="text-gray-600 fw-normal mb-2">No Product Stock Found</h5>
                                        <p class="text-sm text-gray-500 mb-0 text-center px-3">No matching results found for the current search.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                @if($products instanceof \Illuminate\Pagination\AbstractPaginator)
                <div class="d-flex justify-content-end mt-4">
                    {{ $products->links('livewire::bootstrap') }}
                </div>
                @endif

            </div>

            <!-- show product customer wise  -->
            @if($showModal)
            <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-labelledby="salesModalLabel" style="background: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content rounded-3 shadow-lg">
                        <div class="modal-header text-white" style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%); border-top-left-radius: 0.3rem; border-top-right-radius: 0.3rem;">
                            <h5 class="modal-title fw-bold" id="salesModalLabel">Sales Details for: {{ $selectedProduct->product_name }}</h5>
                            <button type="button" class="btn-close btn-close-white" wire:click="$set('showModal', false)" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                                <div class="fs-6 fw-semibold"><span class="text-muted">Total Sold Quantity:</span> {{ $totalSold }}</div>
                                <div class="fs-6 fw-semibold"><span class="text-muted">Available Quantity:</span> {{ $availableQuantity }}</div>
                            </div>

                            @if($saleItems->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th scope="col" class="fw-semibold">Customer Name</th>
                                            <th scope="col" class="fw-semibold">Invoice</th>
                                            <th scope="col" class="fw-semibold">Quantity</th>
                                            <th scope="col" class="fw-semibold">Unit Price</th>
                                            <th scope="col" class="fw-semibold">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($saleItems as $item)
                                        <tr class="animate__animated animate__fadeIn" style="animation-duration: 0.3s;">
                                            <td>{{ $item->customer_name }}</td>
                                            <td>{{ $item->invoice_number }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>${{ number_format($item->price, 2) }}</td>
                                            <td>${{ number_format($item->quantity * $item->price, 2) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @else
                            <p class="text-center text-muted my-4">No sales found for this product.</p>
                            @endif
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            <button type="button" class="btn btn-secondary px-4 py-2 rounded-3" wire:click="$set('showModal', false)">Close</button>
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
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
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

            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('printButton').addEventListener('click', function() {
                    printProductStockDetails();
                });
            });

            function printProductStockDetails() {
                const tableContent = document.querySelector('.table-responsive')?.cloneNode(true) || '';
                const printWindow = window.open('', '_blank', 'height=600,width=800');

                printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Product Stock Details - Print Report</title>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
            <style>
                body { font-family: 'Courier New', monospace !important; padding: 20px; font-size: 14px; color: #000 !important; }
                * { color: #000 !important; }
                .print-container { max-width: 900px; margin: 0 auto; }
                .print-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #000; display: flex; justify-content: space-between; align-items: center; }
                .print-header h2 { color: #000; font-weight: 700; letter-spacing: -0.025em; }
                .print-footer { margin-top: 20px; padding-top: 15px; border-top: 2px solid #000; text-align: center; font-size: 12px; color: #000; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { padding: 12px; border: 1px solid #000; text-align: center; vertical-align: middle; }
                th { background-color: #fff; font-weight: 600; text-transform: uppercase; color: #000; }
                .img-preview { width:48px; height:48px; object-fit:cover; border-radius:0.5rem; border:1px solid #000; }
                .no-image { width:48px; height:48px; background-color:#f3f4f6; display:flex; align-items:center; justify-content:center; border-radius:0.5rem; border:1px solid #000; }
                .badge { padding:0.25rem 0.75rem; border-radius:9999px; font-size:0.875rem; font-weight:500; color:#000; border: 1px solid #000; background: #fff; }
                .bg-green, .bg-red { background: #fff !important; color: #000 !important; }
                @media print { .no-print { display:none; } thead { display:table-header-group; } tr { page-break-inside: avoid; } body { padding:10px; } .print-container { max-width:100%; } * { color: #000 !important; font-weight: bold !important; } }
            </style>
        </head>
        <body>
            <div class="print-container">
                <div class="print-header">
                    <h2 class="fw-bold tracking-tight">Product Stock Details</h2>
                    <div class="no-print">
                        <button class="btn btn-primary rounded-full px-4" style="background-color:#000;border-color:#000;" onclick="window.print();">Print</button>
                        <button class="btn btn-outline-secondary rounded-full px-4 ms-2" onclick="window.close();">Close</button>
                    </div>
                </div>
                ${tableContent.outerHTML}
                <div class="print-footer">
                    <small>Generated on ${new Date().toLocaleString('en-US', { timeZone: 'Asia/Colombo' })}</small><br>
                    <p>PLUS <br> NO 20/2/1, 2nd FLOOR,HUNTER BUILDING,BANKSHALLL STREET,COLOMBO-11 | Phone: 011 - 2332786 <br> Email: plusaccessories.lk@gmail.com</p>
                </div>
            </div>
        </body>
        </html>
    `);

                printWindow.document.close();
                printWindow.focus();
            }
        </script>
        @endpush
    </div>