<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\ReturnProduct;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

#[Title("Bills & Invoices")]
#[Layout('components.layouts.admin')]
class Bill extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedSale = null;
    public $saleDetails = null;
    public $filters = [
        'dateFrom' => '',
        'dateTo' => '',
        'paymentType' => '',
        'customerType' => '',
    ];

    public function mount()
    {
        // Initialize filters if needed
    }

    public function viewInvoice($saleId)
    {
        $this->selectedSale = Sale::with([
            'customer',
            'items.product',
            'payments',
            'user'
        ])->find($saleId);

        if ($this->selectedSale) {
            // Get return items for this sale
            $returnItems = ReturnProduct::with('product')
                ->where('sale_id', $saleId)
                ->get();

            // Calculate totals
            $totalReturnAmount = $returnItems->sum('total_amount');
            $adjustedGrandTotal = max(0, $this->selectedSale->total_amount - $totalReturnAmount);

            $this->saleDetails = [
                'sale' => $this->selectedSale,
                'items' => $this->selectedSale->items,
                'returnItems' => $returnItems,
                'totalReturnAmount' => $totalReturnAmount,
                'adjustedGrandTotal' => $adjustedGrandTotal,
                'payments' => $this->selectedSale->payments,
            ];

            $this->dispatch('openInvoiceModal');
        }
    }

    public function editInvoice($saleId)
    {
        // Redirect to store billing page with the sale ID to edit
        return redirect()->route('admin.store-billing', ['edit' => $saleId]);
    }

    public function resetFilters()
    {
        $this->filters = [
            'dateFrom' => '',
            'dateTo' => '',
            'paymentType' => '',
            'customerType' => '',
        ];
    }

    public function render()
    {
        $query = Sale::with(['customer', 'items', 'payments', 'user'])
            ->when($this->search, function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filters['dateFrom'], function ($q) {
                $q->whereDate('created_at', '>=', $this->filters['dateFrom']);
            })
            ->when($this->filters['dateTo'], function ($q) {
                $q->whereDate('created_at', '<=', $this->filters['dateTo']);
            })
            ->when($this->filters['paymentType'], function ($q) {
                $q->where('payment_type', $this->filters['paymentType']);
            })
            ->when($this->filters['customerType'], function ($q) {
                $q->where('customer_type', $this->filters['customerType']);
            })
            ->orderBy('created_at', 'desc');

        $sales = $query->paginate(10);

        // Calculate stats
        $totalSales = Sale::count();
        $todaySales = Sale::whereDate('created_at', today())->count();
        $totalRevenue = Sale::sum('total_amount');
        $todayRevenue = Sale::whereDate('created_at', today())->sum('total_amount');

        return view('livewire.admin.bill', [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'todaySales' => $todaySales,
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
        ]);
    }
}
