<div class="px-3">
    @push('styles')
    <style>
        /* ===== Base Layout ===== */
        .container-fluid {
            width: 100%;
            padding-right: var(--bs-gutter-x, 0.75rem);
            padding-left: var(--bs-gutter-x, 0.75rem);
            margin-right: auto;
            margin-left: auto;
            overflow-x: hidden;
        }

        /* ===== Tabs ===== */
        .content-tabs {
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 1.5rem;
            overflow-x: auto;
            white-space: nowrap;
        }

        .content-tab {
            padding: 0.75rem 1.25rem;
            padding-bottom: 0;
            margin-right: 0.5rem;
            border-radius: 0.75rem 0.75rem 0 0;
            cursor: pointer;
            font-weight: 500;
            color: #6b7280;
            transition: all 0.3s ease;
        }

        .content-tab:hover {
            background: #f3f4f6;
            color: #233d7f;
        }

        .content-tab.active {
            color: #233d7f;
        }
        .content-tab.active::after {
            content: '';
            display: block;
            width: 100%;
            height: 2px;
            background: #233d7f;
            border-radius: 0.75rem 0.75rem 0 0;
            transition: all 0.3s ease;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* ===== Stat Cards ===== */
        .stat-card {
            background: #fff;
            border-radius: 1rem;
            padding: 1.2rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .stat-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: #6b7280;
        }

        .stat-value {
            font-size: 1.4rem;
            font-weight: 600;
            margin: 0.4rem 0;
            color: #233d7f;
        }
        /* progress */
        .progress{
            height: 8px;
            margin-bottom: 15px;
        }

        /* ===== Chart Card ===== */
        .chart-card {
            background: #fff;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
        }

        .chart-header {
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 1rem 1rem 0 0;
        }

        .chart-header h6 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
            color: #233d7f;
        }

        .chart-container {
            position: relative;
            height: 320px;
            padding: 1rem;
        }

        /* ===== List Card (Recent Sales) ===== */
        .card {
            border: 1px solid #e5e7eb;
            border-radius: 1rem;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            max-height: 400px;
            overflow-y: auto;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #233d7f;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9rem;
            font-weight: 600;
            margin-right: 0.75rem;
        }

        .amount {
            font-weight: 600;
            color: #233d7f;
        }

        /* ===== Inventory Widget ===== */
        .widget-container {
            background: #fff;
            border-radius: 1rem;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            padding: 1.2rem;
            margin-bottom: 1.5rem;
            max-height: 450px;
            overflow-y: auto;
        }

        .widget-header h6 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #233d7f;
        }

        .status-badge {
            padding: 0.25rem 0.6rem;
            border-radius: 0.4rem;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .in-stock {
            background: #d1e7dd;
            color: #0f5132;
        }

        .low-stock {
            background: #fff3cd;
            color: #664d03;
        }

        .out-of-stock {
            background: #f8d7da;
            color: #842029;
        }

        /* ===== Buttons ===== */
        .btn-outline-primary {
            border: 1px solid #233d7f;
            color: #233d7f;
            font-size: 0.8rem;
            font-weight: 500;
            border-radius: 0.5rem;
            padding: 0.35rem 0.75rem;
            transition: all 0.2s ease;
        }

        .btn-outline-primary:hover {
            background: #233d7f;
            color: #fff;
        }

        /* ===== Mobile Responsiveness ===== */
        @media (max-width: 992px) {

            /* Tabs */
            .content-tabs {
                display: flex;
                overflow-x: auto;
                scrollbar-width: none;
            }

            .content-tabs::-webkit-scrollbar {
                display: none;
            }

            .content-tab {
                flex: 1 0 auto;
                text-align: center;
            }

            /* Stat Cards */
            .stat-card {
                padding: 1rem;
            }

            .stat-label {
                font-size: 0.85rem;
            }

            .stat-value {
                font-size: 1.1rem;
            }

            /* Chart */
            .chart-container {
                height: 240px !important;
            }

            /* Recent Sales List */
            .card {
                max-height: unset;
            }

            .list-group-item {
                flex-wrap: wrap;
                gap: 0.5rem;
            }

            .amount {
                font-size: 0.85rem;
            }

            /* Inventory Widget */
            .widget-container {
                max-height: unset;
            }

            .widget-container h6 {
                font-size: 0.95rem;
            }

            .status-badge {
                font-size: 0.7rem;
                padding: 0.2rem 0.5rem;
            }
        }

        @media (max-width: 576px) {

            /* Tabs on extra small */
            .content-tab {
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem;
            }

            /* Sales chart select */
            .chart-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }

            /* Sales items */
            .avatar {
                width: 32px;
                height: 32px;
                font-size: 0.8rem;
            }

            .list-group-item h6 {
                font-size: 0.85rem;
            }

            .list-group-item p {
                font-size: 0.75rem;
            }

            /* Inventory items */
            .progress {
                height: 6px;
            }
        }
    </style>
    @endpush


    <!-- ===== Tabs ===== -->
    <div class="content-tabs">
        <div class="d-flex">
            <div class="content-tab active" data-tab="overview">Overview</div>
            <div class="content-tab" data-tab="analytics">Analytics</div>
            <div class="content-tab" data-tab="reports">Reports</div>
            <div class="content-tab" data-tab="notifications">Notifications</div>
        </div>
    </div>

    <!-- Overview Content -->
    <div id="overview" class="tab-content active">
        <!-- Stats Cards Row -->
        <div class="row mb-4">
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-label">Total Revenue</div>
                    </div>
                    <div class="stat-value">Rs.{{ number_format($totalRevenue, 2) }}</div>
                    <div class="stat-info mt-1">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Revenue</small>
                            <small>{{ $revenuePercentage }}% of total sales</small>
                        </div>
                        <div class="progress " >
                            <div class="progress-bar bg-success" role="progressbar" style=" width: {{ $revenuePercentage }}%;" aria-valuenow="{{ $revenuePercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted text-truncate-mobile">Rs.{{ number_format($totalRevenue) }} of Rs.{{ number_format($totalRevenue + $totalDueAmount) }}</small>
                        </div>
                    </div>
                    <div class="stat-info mt-3 pt-2 border-top border-1 border-gray-200">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-check-circle-fill text-success me-1"></i> Today Revenue</small>
                            <span class="badge bg-success">{{$todayRevenueCount}}</span>
                            
                        </div>
                        <small class="d-block text-end text-success">Rs.{{ number_format($todayRevenue, 2) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-label">Total Due Amount</div>
                    </div>
                    <div class="stat-value">Rs.{{ number_format($totalDueAmount, 2) }}</div>
                    <div class="stat-change-alert">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Due Amount</small>
                            <small>{{ $duePercentage }}% of total sales</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $duePercentage }}%;" aria-valuenow="{{ $duePercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted text-truncate-mobile">Rs.{{ number_format($totalDueAmount) }} due of Rs.{{ number_format($totalDueAmount + $totalRevenue) }}</small>
                        </div>
                    </div>
                    <div class="stat-info mt-3 pt-2 border-top border-1 border-gray-200">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-clock-fill text-danger me-1"></i> Partially Paid</small>
                            <span class="badge bg-danger">{{ $partialPaidCount }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="stat-label">Inventory Status</div>
                    </div>
                    <div class="stat-value">{{ number_format($totalStock) }} <span class="fs-6 text-muted">units</span></div>
                    <div class="stat-info">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Damaged Stock</small>
                            <small>{{ $totalStock > 0 ? round(($damagedStock / ($totalStock + $damagedStock)) * 100, 1) : 0 }}% of total</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $totalStock > 0 ? round(($damagedStock / ($totalStock + $damagedStock)) * 100, 1) : 0 }}%;" aria-valuenow="{{ $totalStock > 0 ? round(($damagedStock / ($totalStock + $damagedStock)) * 100, 1) : 0 }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted text-truncate-mobile">{{ number_format($damagedStock) }} damaged of {{ number_format($totalStock + $damagedStock) }}</small>
                        </div>
                    </div>
                    <div class="stat-info mt-3 pt-2 border-top border-1 border-gray-200">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-box-seam text-warning me-1"></i> Total</small>
                            <span class="badge bg-warning" style=" color: #FFFFFF;">{{ number_format($totalStock) }}</span>
                        </div>
                        <small class="d-block text-end text-warning">Rs.{{ number_format($totalInventoryValue, 2) }}</small>
                    </div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3 mb-3">
                <div class="stat-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="stat-label">Total Products Sold</div>
                    </div>
                    @php
                    $totalSold = collect($soldProducts)->sum('total_quantity');
                    $soldPercentage = $totalStock > 0 ? round(($totalSold / ($totalStock + $totalSold)) * 100, 1) : 0;
                    @endphp
                    <div class="stat-value">{{ number_format($totalSold) }} <span class="fs-6 text-muted">units</span></div>
                    <div class="stat-info">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Sold Units</small>
                            <small>{{ $soldPercentage }}% of total inventory</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $soldPercentage }}%;" aria-valuenow="{{ $soldPercentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted text-truncate-mobile">{{ number_format($totalSold) }} sold of {{ number_format($totalStock + $totalSold) }}</small>
                        </div>
                    </div>
                    <div class="stat-info mt-3 pt-2 border-top border-1 border-gray-200">
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="bi bi-cart-check text-success me-1"></i> Total</small>
                            <span class="badge bg-success" style="color: #FFFFFF;">{{ number_format($totalSold) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart and Recent Sales Section -->
        <div class="row">
            <div class="col-sm-12 col-lg-8 mb-4">
                <div class="chart-card">
                    <div class="chart-header d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-mobile-2">
                            <h6 class="mb-1 fw-bold tracking-tight" style="color: #233D7F;">Sales Overview</h6>
                            <p class="text-muted mb-0 small">Daily sales trend</p>
                        </div>
                        <select wire:model.live="filter" class="form-select form-select-sm" style="width: 150px;">
                            <option value="7_days">Last 7 Days</option>
                            <option value="30_days">Last 30 Days</option>
                        </select>
                    </div>
                    <div class="chart-container">
                        <canvas id="salesChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-sm-12 col-lg-4 mb-4">
                <div class="card">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start flex-wrap mb-3">
                            <div class="mb-2 mb-md-0">
                                <h6 class="card-title fw-bold tracking-tight" style="color: #233D7F;">Recent Sales</h6>
                                <p class="text-muted small mb-0">Latest transactions</p>
                            </div>
                            <a href="{{ route('admin.view-payments') }}" class="btn btn-outline-primary">
                                <i class="bi bi-list-ul"></i> View All
                            </a>
                        </div>
                        <ul class="list-group list-group-flush">
                            @forelse($recentSales->take(10) as $sale)
                            <li class="list-group-item d-flex align-items-center py-2">
                                <div class="avatar">
                                    {{ strtoupper(substr($sale->name, 0, 1)) }}{{ strtoupper(substr(strpos($sale->name, ' ') !== false ? substr($sale->name, strpos($sale->name, ' ') + 1, 1) : '', 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 text-truncate-mobile" style="color: #233D7F;">{{ $sale->name }}</h6>
                                    <!-- <p class="text-muted small mb-0 text-truncate-mobile">{{ $sale->email }}</p> -->
                                    <p class="text-muted small mb-0 text-truncate-mobile">{{ $sale->type }}</p>
                                </div>
                                <div class="amount text-end">
                                    +Rs.{{ number_format($sale->total_amount, 2) }}
                                    @if($sale->due_amount > 0)
                                    <span class="d-block text-danger small">Rs.{{ number_format($sale->due_amount, 2) }}</span>
                                    @else
                                    <span class="d-block badge bg-success mt-1 small">Paid</span>
                                    @endif
                                </div>
                            </li>
                            @empty
                            <li class="list-group-item text-center py-3">
                                <p class="text-muted mb-0">No sales recorded yet</p>
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Section -->
        <div class="row">
            <div class="col-lg-6 col-md-6 mb-4">
                <div class="widget-container">
                    <div class="widget-header d-flex justify-content-between align-items-start flex-wrap mb-3">
                        <div class="mb-2 mb-md-0">
                            <h6 class="fw-bold tracking-tight" style="color: #233D7F;">Inventory Status</h6>
                            <p class="text-muted small mb-0">Current stock levels and alerts</p>
                        </div>
                        <a href="{{ route('admin.product-stocks') }}" class="btn btn-outline-primary">
                            <i class="bi bi-box-seam"></i> View Details
                        </a>
                    </div>
                    <div class="inventory-container">
                        @forelse($productInventory->take(10) as $product)
                        @php
                        $stockPercentage = ($product->stock_quantity + $product->damage_quantity) > 0 ? round(($product->stock_quantity / ($product->stock_quantity + $product->damage_quantity)) * 100, 2) : 0;
                        $statusClass = $product->stock_quantity == 0 ? 'out-of-stock' : ($stockPercentage <= 25 ? 'low-stock' : 'in-stock' );
                        $statusText=$product->stock_quantity == 0 ? 'Out of Stock' : ($stockPercentage <= 25 ? 'Low Stock' : 'In Stock' );
                        $progressClass=$product->stock_quantity == 0 ? 'bg-danger' : ($stockPercentage <= 25 ? 'bg-warning' : 'bg-success' );
                        @endphp

                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0" style="color:#233d7f;">{{ $product->name }}</h6>
                            <div class="d-flex align-items-end flex-wrap mt-1 mt-md-0">
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                                <div class="ms-2 text-muted small">{{ $product->stock_quantity }}/{{ $product->stock_quantity + $product->damage_quantity }}</div>
                            </div>
                        </div>
                        <div class="progress mt-1">
                            <div class="progress-bar {{ $progressClass }}" style="width: {{ $stockPercentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <div class="alert alert-info border-0">No product inventory data available.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Analytics Content -->
<div id="analytics" class="tab-content">
    <div class="alert alert-info border-0" style="border-radius: 0.5rem; color: #233D7F; background-color: #F8F9FA;">
        Analytics content will appear here when this tab is selected.
    </div>
</div>

<!-- Reports Content -->
<div id="reports" class="tab-content">
    <div class="alert alert-info border-0" style="border-radius: 0.5rem; color: #233D7F; background-color: #F8F9FA;">
        Reports content will appear here when this tab is selected.
    </div>
</div>

<!-- Notifications Content -->
<div id="notifications" class="tab-content">
    <div class="alert alert-info border-0" style="border-radius: 0.5rem; color: #233D7F; background-color: #F8F9FA;">
        Notifications content will appear here when this tab is selected.
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('livewire:initialized', function() {
        // Get the canvas context
        const salesCtx = document.getElementById('salesChart')?.getContext('2d');
        if (!salesCtx) {
            console.error('Sales chart canvas not found');
            return;
        }

        let salesChartInstance;

        function renderSalesChart(labels, totals) {
            // Destroy existing chart instance if it exists
            if (salesChartInstance) {
                salesChartInstance.destroy();
            }

            // Ensure labels and totals are arrays
            labels = Array.isArray(labels) ? labels.map(date => {
                const d = new Date(date);
                return isNaN(d) ? date : d.toLocaleDateString('en-US', {
                    month: 'short',
                    day: 'numeric'
                });
            }) : [];
            totals = Array.isArray(totals) ? totals : [];

            salesChartInstance = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Daily Sales',
                        data: totals,
                        borderColor: '#233D7F',
                        backgroundColor: 'rgba(35, 61, 127, 0.2)',
                        tension: 0.3,
                        fill: true,
                        pointBackgroundColor: '#233D7F',
                        pointRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            enabled: true,
                            backgroundColor: '#233D7F',
                            titleColor: '#FFFFFF',
                            bodyColor: '#FFFFFF',
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return `Sales: Rs. ${Number(context.parsed.y).toFixed(2)}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#E9ECEF'
                            },
                            ticks: {
                                color: '#233D7F',
                                callback: function(value) {
                                    return 'Rs. ' + Number(value).toFixed(2);
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#233D7F',
                                maxRotation: 45,
                                minRotation: 45
                            }
                        }
                    }
                }
            });
        }

        // Initial chart render
        const initialData = @json($salesData);
        renderSalesChart(initialData.labels || [], initialData.totals || []);

        // Listen for chart updates
        window.Livewire.on('refreshSalesChart', (data) => {
            console.log('Refreshing chart with data:', data); // Debugging
            renderSalesChart(data.labels || [], data.totals || []);
        });

        // Tab switching logic
        const tabs = document.querySelectorAll('.content-tab');
        const tabContents = document.querySelectorAll('.tab-content');
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById(tab.dataset.tab).classList.add('active');
            });
        });
    });
</script>
@endpush
</div>