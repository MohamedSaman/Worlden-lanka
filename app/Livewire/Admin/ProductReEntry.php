<?php

namespace App\Livewire\Admin;

use App\Models\ProductDetail;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\CustomerAccount;
use App\Models\ReturnProduct;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Product Re-Entry')]
class ProductReEntry extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    // Left panel searches
    public $searchCustomer = '';
    public $searchInvoice = '';
    public $customerResults = [];
    public $invoiceResults = [];

    // Selected context
    public $selectedCustomerId = null;
    public $selectedCustomer = null;
    public $selectedInvoiceId = null;
    public $selectedInvoice = null; // Sale with relations
    public $selectedInvoiceItems = [];
    public $returnedProducts = []; // Previously returned products for selected invoice

    // Product search/selection on right
    public $productSearch = '';
    public $productResults = [];
    public $selectedProductId = null;
    public $selectedProduct = null;

    // Re-entry inputs
    public $addStock = 0;      // re-entry into available stock
    public $addDamage = 0;     // increases damage only
    public $returnNotes = '';  // notes for return

    public function updatedProductSearch(): void
    {
        // Live search products
        $term = trim($this->productSearch);
        if ($term === '') {
            $this->productResults = [];
            return;
        }
        $this->productResults = ProductDetail::with('category')
            ->where(function ($q) use ($term) {
                $q->where('product_name', 'like', "%{$term}%")
                    ->orWhere('product_code', 'like', "%{$term}%");
            })
            ->orderBy('product_name')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function updatedSearchCustomer(): void
    {
        $term = trim($this->searchCustomer);
        if ($term === '') {
            $this->customerResults = [];
            return;
        }
        $this->customerResults = Customer::query()
            ->where(function ($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('business_name', 'like', "%{$term}%");
            })
            ->orderBy('name')
            ->limit(20)
            ->get()
            ->toArray();
    }

    public function updatedSearchInvoice(): void
    {
        $term = trim($this->searchInvoice);
        if ($term === '') {
            $this->invoiceResults = [];
            return;
        }
        $query = Sale::with('customer')
            ->where('invoice_number', 'like', "%{$term}%")
            ->orderBy('sales_date', 'desc');
        if ($this->selectedCustomerId) {
            $query->where('customer_id', $this->selectedCustomerId);
        }
        $this->invoiceResults = $query->limit(20)->get()->toArray();
    }

    public function selectCustomer($customerId): void
    {
        $this->selectedCustomerId = $customerId;
        $this->selectedCustomer = Customer::find($customerId);
        // Reset invoice context when customer changes
        $this->selectedInvoiceId = null;
        $this->selectedInvoice = null;
        $this->selectedInvoiceItems = [];
        // Clear both search boxes and results so other details are hidden
        $this->searchCustomer = '';
        $this->customerResults = [];
        $this->searchInvoice = '';
        $this->invoiceResults = [];
    }

    public function selectInvoice($invoiceId): void
    {
        $sale = Sale::with(['customer'])->find($invoiceId);
        if ($sale) {
            $this->selectedInvoiceId = $sale->id;
            $this->selectedInvoice = $sale->toArray();
            // Ensure customer context matches invoice
            $this->selectedCustomerId = $sale->customer_id;
            $this->selectedCustomer = $sale->customer;

            // Load previously returned products for this invoice
            $this->returnedProducts = ReturnProduct::with('product')
                ->where('sale_id', $sale->id)
                ->get()
                ->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'product_id' => $r->product_id,
                        'product_name' => $r->product->product_name ?? 'Unknown',
                        'return_quantity' => $r->return_quantity,
                        'selling_price' => $r->selling_price,
                        'total_amount' => $r->total_amount,
                        'notes' => $r->notes,
                        'created_at' => $r->created_at->format('Y-m-d H:i'),
                    ];
                })
                ->toArray();

            // Load invoice items for display (product name, quantity, amount)
            // Calculate available quantity (sold - returned)
            $items = SalesItem::with('product')
                ->where('sale_id', $sale->id)
                ->orderBy('id')
                ->get();
            $this->selectedInvoiceItems = $items->map(function ($i) use ($sale) {
                $qty = (int)($i->quantity ?? 0);
                $price = (float)($i->price ?? 0);
                $discount = (float)($i->discount ?? 0);
                $amount = max(0, ($price * $qty) - $discount);

                // Calculate already returned quantity for this product
                $returnedQty = ReturnProduct::where('sale_id', $sale->id)
                    ->where('product_id', $i->product_id)
                    ->sum('return_quantity');

                $availableQty = max(0, $qty - $returnedQty);

                return [
                    'id' => $i->id,
                    'product_id' => $i->product_id,
                    'product_name' => $i->product->product_name ?? ($i->product->name ?? 'Product'),
                    'quantity' => $qty,
                    'returned_quantity' => $returnedQty,
                    'available_quantity' => $availableQty,
                    'price' => $price,
                    'discount' => $discount,
                    'amount' => $amount,
                ];
            })->toArray();
            // Clear both search boxes and results so other details are hidden
            $this->searchInvoice = '';
            $this->invoiceResults = [];
            $this->searchCustomer = '';
            $this->customerResults = [];
        }
    }

    public function selectProduct($productId)
    {
        $this->selectedProductId = $productId;
        $this->selectedProduct = ProductDetail::with('category')->find($productId);
        $this->addStock = 0;
        $this->addDamage = 0;
        $this->returnNotes = '';
        // Clear product search/results so the selection is focused
        $this->productSearch = '';
        $this->productResults = [];
    }

    public function updateStock()
    {
        $this->validate([
            'addStock' => 'required|integer|min:0',
            'addDamage' => 'required|integer|min:0',
            'returnNotes' => 'nullable|string|max:500',
        ]);

        if (!$this->selectedProduct) {
            $this->addError('selectedProduct', 'Please select a product.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Product Required',
                'text' => 'Please select a product before processing re-entry.'
            ]);
            return;
        }

        // No-op if both are zero
        if (($this->addStock ?? 0) <= 0 && ($this->addDamage ?? 0) <= 0) {
            $this->addError('addStock', 'Enter at least one quantity.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Quantity Required',
                'text' => 'Please enter at least one quantity (re-entry or damage).'
            ]);
            return;
        }

        // Require selecting an invoice for returns
        if (!$this->selectedInvoiceId) {
            $this->addError('selectedInvoiceId', 'Please select an invoice before processing return.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Invoice Required',
                'text' => 'Please select an invoice before processing return.'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            $reEntryUnits = max(0, (int)$this->addStock);
            $damageUnits = max(0, (int)$this->addDamage);
            $totalReturnUnits = $reEntryUnits + $damageUnits;

            // Check if this product was sold in the selected invoice
            $salesItem = SalesItem::where('sale_id', $this->selectedInvoiceId)
                ->where('product_id', $this->selectedProduct->id)
                ->first();

            if (!$salesItem) {
                throw new \Exception('This product was not sold in the selected invoice.');
            }

            // Calculate already returned quantity
            $alreadyReturned = ReturnProduct::where('sale_id', $this->selectedInvoiceId)
                ->where('product_id', $this->selectedProduct->id)
                ->sum('return_quantity');

            $availableForReturn = $salesItem->quantity - $alreadyReturned;

            if ($totalReturnUnits > $availableForReturn) {
                throw new \Exception("Cannot return {$totalReturnUnits} units. Only {$availableForReturn} units available for return.");
            }

            // 1) Update product stock/damage (only add to stock, don't decrease sale quantities)
            $product = ProductDetail::lockForUpdate()->find($this->selectedProduct->id);
            $product->stock_quantity = ($product->stock_quantity ?? 0) + $reEntryUnits;
            $product->damage_quantity = ($product->damage_quantity ?? 0) + $damageUnits;
            // Decrease sold for all returned units (re-entry + damage)
            $product->sold = max(0, (int)($product->sold ?? 0) - $totalReturnUnits);
            $product->save();

            // 2) Create return product record
            $unitPrice = floatval($salesItem->price ?? 0);
            $returnAmount = $totalReturnUnits * $unitPrice;

            ReturnProduct::create([
                'sale_id' => $this->selectedInvoiceId,
                'product_id' => $this->selectedProduct->id,
                'return_quantity' => $totalReturnUnits,
                'selling_price' => $unitPrice,
                'total_amount' => $returnAmount,
                'notes' => $this->returnNotes,
            ]);

            // 3) Do NOT modify sales or sales_items tables
            // Returns are tracked separately in return_products table

            DB::commit();

            // Reset inputs, refresh selections
            $this->addStock = 0;
            $this->addDamage = 0;
            $this->returnNotes = '';
            $this->selectedProduct = $product->fresh('category');

            // Reload invoice to show updated return data
            $this->selectInvoice($this->selectedInvoiceId);

            // Success alert via SweetAlert
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'Product return processed successfully.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('submit', 'Failed to process return: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to process return: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        // We primarily drive results through updated* handlers; render supplies current selections.
        return view('livewire.admin.product-re-entry');
    }
}
