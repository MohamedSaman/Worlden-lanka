<div class="container-fluid py-6 transition-colors duration-300">
    <div class="card border-0">
        <!-- Card Header -->

        <div class="card-header text-white p-3 rounded-t-4 d-flex flex-column flex-md-row align-items-start align-items-md-center"
            style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%); border-radius: 20px 20px 0 0;">
            <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-2 d-flex align-items-center justify-content-center me-md-3 mb-2 mb-md-0">
                <i class="bi bi-shield-lock text-white fs-5 fs-md-4" aria-hidden="true"></i>
            </div>
            <div class="text-center text-md-start">
                <h3 class="mb-1 fw-bold tracking-tight text-white fs-5 fs-md-4">Product Stock Details</h3>
                <p class="text-white opacity-80 mb-0 text-sm">Monitor and manage your product inventorys</p>
            </div>
        </div>
        <div class="card-header bg-transparent pb-4 mt-4 d-flex flex-column justify-content-between gap-3 border-bottom" style="border-color: #233D7F;">
            <!-- Search Bar -->
            <div class="w-100 d-flex justify-content-center justify-content-lg-start">
                <div class="input-group" style="max-width: 400px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                    <span class="input-group-text bg-gray-100 border-0 px-3">
                        <i class="bi bi-search text-danger"></i>
                    </span>
                    <input type="text"
                        class="form-control"
                        placeholder="Search products..."
                        wire:model.live.debounce.300ms="search"
                        autocomplete="off">
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center justify-content-lg-end">
                <button wire:click="exportToCSV"
                    class="btn text-white rounded-pill shadow-sm px-3 py-2 transition-transform hover:scale-105"
                    aria-label="Export stock details to CSV"
                    style="background-color: #9d1c20; border-color: #9d1c20; font-size: 0.875rem;">
                    <i class="bi bi-download me-1" aria-hidden="true"></i> 
                    <span class="d-none d-sm-inline">Export </span>CSV
                </button>
                <button id="printButton"
                    class="btn text-white rounded-pill shadow-sm px-3 py-2 transition-transform hover:scale-105"
                    aria-label="Print stock details"
                    style="background-color: #9d1c20; border-color: #9d1c20; font-size: 0.875rem;">
                    <i class="bi bi-printer me-1" aria-hidden="true"></i> Print
                </button>
                <button wire:click="toggleShowAll"
                    class="btn btn-outline-secondary rounded-pill shadow-sm px-3 py-2"
                    aria-label="Toggle show all or paginated"
                    style="font-size: 0.875rem;">
                    @if($showAll)
                        <i class="bi bi-list-nested me-1" aria-hidden="true"></i> 
                        <span class="d-none d-sm-inline">Show </span>Paginated
                    @else
                        <i class="bi bi-list-task me-1" aria-hidden="true"></i> 
                        <span class="d-none d-sm-inline">Show </span>All
                    @endif
                </button>
            </div>
        </div>

        <!-- Card Body -->
        <div class="card-body p-1  pt-5 bg-transparent">

            <!-- Stock Table or Empty State -->

            <div class="table-responsive shadow-sm rounded-2 overflow-auto" style="min-height: 400px;">
                <table class="table table-sm table-hover">
                    <thead class="sticky-top">
                        <tr style="background-color: #f8f9fa;">
                            <th class="text-center py-3 ps-2 ps-md-4 d-none d-md-table-cell" style="min-width: 60px;">ID</th>
                            <th class="text-center py-3 d-none d-lg-table-cell" style="min-width: 80px;">Image</th>
                            <th class="text-center py-3" style="min-width: 150px;">Product</th>
                            <th class="text-center py-3 d-none d-sm-table-cell" style="min-width: 120px;">Code</th>
                            <th class="text-center py-3 d-none d-lg-table-cell" style="min-width: 100px;">Category</th>
                            <th class="text-center py-3 d-none d-md-table-cell" style="min-width: 80px;">Sold</th>
                            <th class="text-center py-3" style="min-width: 100px;">Stock</th>
                            <th class="text-center py-3 d-none d-sm-table-cell" style="min-width: 80px;">Damage</th>
                            <th class="text-center py-3 d-none d-lg-table-cell" style="min-width: 80px;">Total</th>
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
                            <td class="text-sm text-center py-3 d-none d-lg-table-cell">
                                @if($product->image)
                                <div class="image-wrapper rounded-lg shadow-sm transition-transform hover:scale-110 mx-auto">
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                        class="rounded-lg"
                                        style="width: 40px; height: 40px; object-fit: cover;"
                                        alt="Image of {{ $product->product_name }}"
                                        onerror="this.onerror=null; this.src=''; this.parentNode.innerHTML='<div style=\'width:40px;height:40px;background-color:#f3f4f6;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;\'><i class=\'bi bi-watch text-gray-600\'></i></div>';">
                                </div>
                                @else
                                <div style="width:30px;height:30px;background-color:#f3f4f6;border-radius:0.5rem;display:flex;align-items:center;justify-content:center; margin:0 auto;">
                                    <i class="bi bi-box-seam text-gray-600"></i>
                                </div>
                                @endif
                            </td>
                            <td class="text-sm py-3">
                                <div class="d-flex align-items-center">
                                    <!-- Mobile image -->
                                    <div class="d-lg-none me-2 flex-shrink-0">
                                        @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}"
                                            class="rounded"
                                            style="width: 32px; height: 32px; object-fit: cover;"
                                            alt="{{ $product->product_name }}">
                                        @else
                                        <div style="width:32px;height:32px;background-color:#f3f4f6;border-radius:0.5rem;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-box-seam text-gray-600" style="font-size: 0.8rem;"></i>
                                        </div>
                                        @endif
                                    </div>
                                    <div class="text-start">
                                        <div class="fw-medium">{{ $product->product_name }}</div>
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
    </div>
    @push('styles')
    <style>
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

        .image-wrapper {
            display: inline-block;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            transition: transform 0.2s ease;
        }

        /* Responsive Table Styles */
        @media (max-width: 767.98px) {
            .table-responsive {
                font-size: 0.875rem;
            }
            
            .card-header {
                padding: 1rem !important;
            }
            
            .btn {
                font-size: 0.8rem !important;
            }
            
            .table td {
                padding: 0.5rem 0.25rem !important;
                vertical-align: middle;
            }
            
            .table th {
                padding: 0.75rem 0.25rem !important;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 575.98px) {
            .container-fluid {
                padding-left: 0.5rem;
                padding-right: 0.5rem;
            }
            
            .card {
                margin: 0;
            }
            
            .input-group {
                max-width: 100% !important;
            }
            
            .table {
                font-size: 0.8rem;
            }
        }

        /* Sticky header for better mobile experience */
        .table thead th {
            position: sticky;
            top: 0;
            z-index: 10;
            background-color: #f8f9fa !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Better scrollbar styling */
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }

        .table-responsive::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
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
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
            <style>
                body { font-family: 'Inter', sans-serif; padding: 20px; font-size: 14px; color: #1f2937; }
                .print-container { max-width: 900px; margin: 0 auto; }
                .print-header { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #1e40af; display: flex; justify-content: space-between; align-items: center; }
                .print-header h2 { color: #1e40af; font-weight: 700; letter-spacing: -0.025em; }
                .print-footer { margin-top: 20px; padding-top: 15px; border-top: 2px solid #e5e7eb; text-align: center; font-size: 12px; color: #6b7280; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
                th, td { padding: 12px; border: 1px solid #e5e7eb; text-align: center; vertical-align: middle; }
                th { background-color: #eff6ff; font-weight: 600; text-transform: uppercase; color: #1e3a8a; }
                .img-preview { width:48px; height:48px; object-fit:cover; border-radius:0.5rem; border:1px solid #e5e7eb; }
                .no-image { width:48px; height:48px; background-color:#f3f4f6; display:flex; align-items:center; justify-content:center; border-radius:0.5rem; border:1px solid #e5e7eb; }
                .badge { padding:0.25rem 0.75rem; border-radius:9999px; font-size:0.875rem; font-weight:500; color:#ffffff; }
                .bg-green { background-color:#22c55e; }
                .bg-red { background-color:#ef4444; }
                @media print { .no-print { display:none; } thead { display:table-header-group; } tr { page-break-inside: avoid; } body { padding:10px; } .print-container { max-width:100%; } }
            </style>
        </head>
        <body>
            <div class="print-container">
                <div class="print-header">
                    <h2 class="fw-bold tracking-tight">Product Stock Details</h2>
                    <div class="no-print">
                        <button class="btn btn-primary rounded-full px-4" style="background-color:#1e40af;border-color:#1e40af;" onclick="window.print();">Print</button>
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