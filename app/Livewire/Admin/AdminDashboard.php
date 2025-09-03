<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Models\Sale;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Dashboard')]
class AdminDashboard extends Component
{
    public $totalRevenue = 0;
    public $todayRevenue = 0;
    public $todayRevenueCount = 0;
    public $totalDueAmount = 0;
    public $totalSales = 0;
    public $revenuePercentage = 0;
    public $duePercentage = 0;
    public $previousMonthRevenue = 0;
    public $revenueChangePercentage = 0;
    public $fullPaidAmount = 0;
    public $partialPaidCount = 0;
    public $partialPaidAmount = 0;
    public $totalStock = 0;
    public $damagedStock = 0;
    public $damagedValue = 0;
    public $totalInventoryValue = 0;
    public $recentSales = [];
    public $productInventory = [];
    public $brandSales = [];
    public $soldProducts = [];
    public $salesData = [];
    public $filter = '7_days';

    public function mount()
    {
        // Get sales statistics
        $salesStats = Sale::select(
            DB::raw('SUM(total_amount) as total_sales'),
            DB::raw('SUM(due_amount) as total_due'),
            DB::raw('COUNT(*) as sales_count')
        )->first();

        $this->totalSales = $salesStats->total_sales ?? 0;
        $this->totalDueAmount = $salesStats->total_due ?? 0;
        $this->totalRevenue = $this->totalSales - $this->totalDueAmount;
        $this->todayRevenue = Sale::whereDate('created_at', Carbon::today())
            ->sum('total_amount') - Sale::whereDate('created_at', Carbon::today())
            ->sum('due_amount');
        $this->todayRevenueCount = Sale::whereDate('created_at', Carbon::today())->count();

        // Calculate percentages
        if ($this->totalSales > 0) {
            $this->revenuePercentage = round(($this->totalRevenue / $this->totalSales) * 100, 1);
            $this->duePercentage = round(($this->totalDueAmount / $this->totalSales) * 100, 1);
        }

        // Get previous month's revenue for comparison
        $previousMonthSales = Sale::whereMonth(
            'created_at',
            '=',
            now()->subMonth()->month
        )->select(
            DB::raw('SUM(total_amount - due_amount) as revenue')
        )->first();

        $this->previousMonthRevenue = $previousMonthSales->revenue ?? 0;

        if ($this->previousMonthRevenue > 0) {
            $this->revenueChangePercentage = round((($this->totalRevenue - $this->previousMonthRevenue) / $this->previousMonthRevenue) * 100, 1);
        }

        // Get partially paid invoices data
        $partialPaidData = Sale::where('payment_status', 'partial')
            ->select(
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(due_amount) as amount')
            )->first();

        $this->partialPaidCount = $partialPaidData->count ?? 0;
        $this->partialPaidAmount = $partialPaidData->amount ?? 0;

        // Get inventory statistics
        $stockStats = DB::table('product_details')
            ->select(
                DB::raw('SUM(stock_quantity) as total_stock'),
                DB::raw('SUM(damage_quantity) as damaged_stock')
            )->first();

        $this->totalStock = $stockStats->total_stock ?? 0;
        $this->damagedStock = $stockStats->damaged_stock ?? 0;

        // Calculate damaged inventory value
        $damagedValue = DB::table('product_details')
            ->select(DB::raw('SUM(damage_quantity * supplier_price) as damaged_value'))
            ->first();

        $this->damagedValue = $damagedValue->damaged_value ?? 0;

        // Calculate total inventory value
        $totalInventoryValue = DB::table('product_details')
            ->select(DB::raw('SUM(stock_quantity * supplier_price) as total_value'))
            ->first();

        $this->totalInventoryValue = $totalInventoryValue->total_value ?? 0;

        $this->loadRecentSales();
        $this->loadProductInventory();
        $this->loadBrandSales();
        $this->loadSoldProducts();
        $this->loadSalesData();
    }

    public function loadRecentSales()
    {
        $this->recentSales = DB::table('sales')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->select(
                'sales.id',
                'sales.invoice_number',
                'sales.total_amount',
                'sales.payment_status',
                'sales.created_at',
                'customers.name',
                'customers.email',
                'customers.type',
                'sales.due_amount'
            )
            ->orderBy('sales.created_at', 'desc')
            ->limit(5)
            ->get();
    }

    public function loadProductInventory()
    {
        $this->productInventory = DB::table('product_details')
            ->select(
                'product_details.id',
                'product_details.product_code as code',
                'product_details.product_name as name',
                'product_details.category_id',
                'product_details.supplier_price',
                'product_details.selling_price',
                'product_details.stock_quantity',
                'product_details.damage_quantity'
            )
            ->orderBy('product_details.stock_quantity', 'asc')
            ->limit(5)
            ->get();
    }

    public function loadBrandSales()
    {
        $this->brandSales = DB::table('sales_items')
            ->join('product_details', 'sales_items.product_id', '=', 'product_details.id')
            ->join('product_categories', 'product_details.category_id', '=', 'product_categories.id')
            ->select(
                'product_categories.name as brand',
                DB::raw('SUM(sales_items.quantity * sales_items.price) as total_sales')
            )
            ->groupBy('product_categories.name')
            ->orderBy('total_sales', 'desc')
            ->get()
            ->toArray();
    }

    public function loadSoldProducts()
    {
        $this->soldProducts = DB::table('sales_items')
            ->join('product_details', 'sales_items.product_id', '=', 'product_details.id')
            ->join('product_categories', 'product_details.category_id', '=', 'product_categories.id')
            ->select(
                'product_categories.name as category',
                DB::raw('SUM(sales_items.quantity) as total_quantity')
            )
            ->groupBy('product_categories.name')
            ->orderBy('total_quantity', 'desc')
            ->get()
            ->toArray();
    }

    public function updatedFilter()
    {
        $this->loadSalesData();
    }

    public function loadSalesData()
    {
        $startDate = match ($this->filter) {
            '7_days' => Carbon::now()->subDays(6), // including today
            '30_days' => Carbon::now()->subDays(29),
            default => Carbon::now()->subDays(6),
        };

        // Generate all dates in the range to ensure continuous data
        $dates = [];
        $currentDate = $startDate->copy();
        $endDate = Carbon::now();
        while ($currentDate <= $endDate) {
            $dates[$currentDate->format('Y-m-d')] = 0;
            $currentDate->addDay();
        }

        // Fetch sales data
        $sales = DB::table('sales')
            ->select(
                DB::raw('DATE(created_at) as sale_date'),
                DB::raw('SUM(total_amount) as total_sales')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('sale_date', 'asc')
            ->get();

        // Merge sales data with all dates
        foreach ($sales as $sale) {
            $dates[$sale->sale_date] = $sale->total_sales;
        }

        $this->salesData = [
            'labels' => array_keys($dates),
            'totals' => array_values($dates),
        ];

        // Dispatch event to update chart
        $this->dispatch('refreshSalesChart', $this->salesData);
    }

    public function render()
    {
        return view('livewire.admin.admin-dashboard');
    }
}