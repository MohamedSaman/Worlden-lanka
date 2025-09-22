<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductDetail;

#[Layout('components.layouts.admin')]
#[Title('Product Management')]
class ProductStocks extends Component
{
    use WithPagination;

    public $search = '';
    public bool $showAll = false;

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