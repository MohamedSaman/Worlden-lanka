<?php

namespace App\Livewire\Admin;

use App\Models\ProductDetail;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Product Re-Entry')]
class ProductReEntry extends Component
{
    use WithPagination;
    
    public $search = '';
    public $selectedProductId = null;
    public $selectedProduct = null;
    public $addStock = 0;
    public $addDamage = 0;
    public $notes = '';

    public function selectProduct($productId)
    {
        $this->selectedProductId = $productId;
        $this->selectedProduct = ProductDetail::with('category')->find($productId);
        $this->addStock = 0;
        $this->addDamage = 0;
        $this->notes = '';
    }

    public function updateStock()
    {
        $this->validate([
            'addStock' => 'required|integer|min:0',
            'addDamage' => 'required|integer|min:0',
        ]);

        if ($this->selectedProduct) {
            // Update the product stock
            $this->selectedProduct->stock_quantity += ($this->addStock -$this->addDamage);
            $this->selectedProduct->damage_quantity += $this->addDamage;
            $this->selectedProduct->save();

            // You might want to create a stock history record here with the notes

            // Reset form
            $this->addStock = 0;
            $this->addDamage = 0;
            $this->notes = '';
            
            // Show success message
            session()->flash('message', 'Stock updated successfully.');
            
            // Refresh the selected product data
            $this->selectedProduct->refresh();
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

        $products = $query->orderBy('product_name')->paginate(10);

        return view('livewire.admin.product-re-entry', [
            'products' => $products,
        ]);
    }
}