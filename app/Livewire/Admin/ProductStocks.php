<?php

namespace App\Livewire\Admin;

use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProductsExport;
use App\Models\ProductDetail;

#[Layout('components.layouts.admin')]
#[Title('Product Management')]
class ProductStocks extends Component
{
    use WithPagination;

    public $search = '';
    
    // public function exportToCSV()
    // {
    //     try {
    //         return Excel::download(new ProductsExport, 'product_stocks_' . now()->format('Y-m-d_H-i-s') . '.csv');
    //     } catch (\Exception $e) {
    //         $this->dispatch('showToast', [
    //             'type' => 'error',
    //             'message' => 'Failed to export: ' . $e->getMessage()
    //         ]);
    //     }
    // }

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

        $products = $query->orderBy('product_name')->paginate(10);
        // $hasStock = $products->sum(function ($product) {
        //     return $product->sold + $product->available + $product->damage;
        // }) > 0;

        return view('livewire.admin.product-stocks', [
            'products' => $products,
            // 'hasStock' => $hasStock
        ]);
    }
}