<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\DB;
use App\Models\ReturnProduct;

#[Layout('components.layouts.admin')]
#[Title('Return Items')]
class ReturnList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $perPage = 10;

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function exportToCSV()
    {
        $returns = $this->getReturnData()->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="return_items_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($returns) {
            $file = fopen('php://output', 'w');

            // Add headers
            fputcsv($file, ['#', 'Invoice Number', 'Customer Name', 'Product Name', 'Product Code', 'Return Qty', 'Unit Price', 'Total Amount', 'Return Date', 'Notes']);

            // Add data rows
            foreach ($returns as $index => $return) {
                fputcsv($file, [
                    $index + 1,
                    $return->invoice_number,
                    $return->customer_name,
                    $return->product_name,
                    $return->product_code,
                    $return->return_quantity,
                    'Rs.' . number_format($return->selling_price, 2),
                    'Rs.' . number_format($return->total_amount, 2),
                    \Carbon\Carbon::parse($return->created_at)->format('d M Y'),
                    $return->notes ?? '-',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function printData()
    {
        $this->dispatch('print-return-table');
    }

    private function getReturnData()
    {
        $query = DB::table('return_products')
            ->join('sales', 'return_products.sale_id', '=', 'sales.id')
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->join('product_details', 'return_products.product_id', '=', 'product_details.id')
            ->select(
                'return_products.id',
                'return_products.return_quantity',
                'return_products.selling_price',
                'return_products.total_amount',
                'return_products.notes',
                'return_products.created_at',
                'sales.invoice_number',
                'sales.sales_date',
                'customers.id as customer_id',
                'customers.name as customer_name',
                'customers.email',
                'customers.phone',
                'product_details.product_name',
                'product_details.product_code',
                'product_details.id as product_id'
            )
            ->orderBy('return_products.created_at', 'desc');

        // Apply search filter
        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('sales.invoice_number', 'like', $term)
                    ->orWhere('customers.name', 'like', $term)
                    ->orWhere('customers.email', 'like', $term)
                    ->orWhere('product_details.product_name', 'like', $term)
                    ->orWhere('product_details.product_code', 'like', $term)
                    ->orWhere('customers.phone', 'like', $term);
            });
        }

        return $query;
    }

    public function render()
    {
        $returns = $this->getReturnData()->paginate($this->perPage);

        $stats = [
            'total_returns' => DB::table('return_products')->count(),
            'total_return_amount' => DB::table('return_products')->sum('total_amount'),
            'today_returns' => DB::table('return_products')->whereDate('created_at', today())->count(),
            'today_return_amount' => DB::table('return_products')->whereDate('created_at', today())->sum('total_amount'),
        ];

        return view('livewire.admin.return-list', [
            'returns' => $returns,
            'stats' => $stats,
        ]);
    }
}
