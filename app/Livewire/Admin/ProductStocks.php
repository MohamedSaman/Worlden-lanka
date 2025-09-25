<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductDetail;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.admin')]
#[Title('Product Management')]
class ProductStocks extends Component
{
    use WithPagination;

    public $search = '';
    public bool $showAll = false;
    protected $paginationTheme = 'bootstrap';

    // When the search term changes, reset to the first page to avoid invalid page issues
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function toggleShowAll(): void
    {
        $this->showAll = !$this->showAll;
        $this->resetPage();
    }

    public function exportToCSV()
    {
        try {
            $query = ProductDetail::query()
                ->with(['category'])
                ->where(function ($query) {
                    $query->where('product_name', 'like', "%{$this->search}%")
                        ->orWhere('product_code', 'like', "%{$this->search}%")
                        ->orWhereHas('category', function ($q) {
                            $q->where('name', 'like', "%{$this->search}%");
                        });
                });

            $products = $query->orderBy('product_name')->get();

            $filename = 'product_stocks_' . now()->format('Y-m-d_H-i-s') . '.csv';

            return response()->streamDownload(function () use ($products) {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['#', 'Product Name', 'Product Code', 'Category', 'Sold', 'Available', 'Damage', 'Total']);

                $rowNumber = 1;
                foreach ($products as $product) {
                    $total = ($product->sold ?? 0) + ($product->stock_quantity ?? 0) + ($product->damage_quantity ?? 0);
                    fputcsv($handle, [
                        $rowNumber++,
                        $product->product_name,
                        $product->product_code,
                        optional($product->category)->name,
                        $product->sold,
                        $product->stock_quantity,
                        $product->damage_quantity,
                        $total,
                    ]);
                }

                fclose($handle);
            }, $filename, ['Content-Type' => 'text/csv']);
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to export: ' . $e->getMessage(),
            ]);
        }
    }

    // Inside ProductStocks.php
    public $selectedProduct;
    public $saleItems = [];
    public $showModal = false;
    public $totalSold = 0;
    public $availableQuantity = 0;


    // Open modal and load customer list for a specific product
    public function viewProductSales($productId)
    {
        $this->selectedProduct = ProductDetail::with('category')->find($productId);

        $this->saleItems = DB::table('sales_items')
            ->join('sales', 'sales.id', '=', 'sales_items.sale_id')
            ->join('customers', 'customers.id', '=', 'sales.customer_id')
            ->select(
                'sales.invoice_number',
                'customers.name as customer_name',
                'sales_items.quantity',
                'sales_items.price'
            )
            ->where('sales_items.product_id', $productId)
            ->get();

        // Total sold quantity
        $this->totalSold = $this->saleItems->sum('quantity');

        // Available quantity from product stock
        $this->availableQuantity = $this->selectedProduct->stock_quantity ?? 0;

        $this->showModal = true;
    }


    public function render()
    {
        $query = ProductDetail::query()
            ->with(['category'])
            ->where(function ($query) {
                $query->where('product_name', 'like', "%{$this->search}%")
                    ->orWhere('product_code', 'like', "%{$this->search}%")
                    ->orWhereHas('category', function ($q) {
                        $q->where('name', 'like', "%{$this->search}%");
                    });
            });

        $products = $this->showAll
            ? $query->orderBy('product_name')->get()
            : $query->orderBy('product_name')->paginate(10);

        return view('livewire.admin.product-stocks', [
            'products' => $products,
        ]);
    }
}
