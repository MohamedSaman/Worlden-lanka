<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\ProductDetail;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use Exception;
use App\Models\Payment;
use App\Models\CustomerAccount;
use App\Models\ProductStock;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Create Purchase')]
class CreatePurchase extends Component
{
    public $searchCustomer = '';
    public $customerResults = [];
    public $selectedCustomer = null;
    public $pendingNewCustomerName = null;

    public $searchProduct = '';
    public $productResults = [];
    public $cart = [];
    public $quantities = [];
    public $prices = [];

    public $adjustAmount = 0;
    public $notes = '';

    protected $listeners = [];

    public function updatedSearchCustomer()
    {
        $this->searchCustomers();
    }

    public function updatedSearchProduct()
    {
        $this->searchProducts();
    }

    public function searchCustomers()
    {
        $term = trim($this->searchCustomer);
        if (strlen($term) < 1) {
            $this->customerResults = [];
            return;
        }

        $this->customerResults = Customer::where('name', 'like', "%{$term}%")
            ->orWhere('phone', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->limit(10)
            ->get();
    }

    public function createCustomerFromSearch()
    {
        $name = trim($this->searchCustomer);
        if (!$name) return;

        $this->pendingNewCustomerName = $name;
        $this->dispatch('openModal', 'addCustomerModal');
        $this->customerResults = [];
    }

    public function selectCustomer($id)
    {
        $this->selectedCustomer = Customer::find($id);
        $this->searchCustomer = $this->selectedCustomer->name ?? '';
        $this->customerResults = [];

        $this->dispatch('searchSelected', [
            'type' => 'customer',
            'name' => $this->selectedCustomer->name ?? null,
        ]);
    }

    public function clearCustomer()
    {
        $this->selectedCustomer = null;
        $this->searchCustomer = '';
    }

    public function searchProducts()
    {
        $term = trim($this->searchProduct);
        if (strlen($term) < 1) {
            $this->productResults = [];
            return;
        }

        $this->productResults = ProductDetail::where('product_name', 'like', "%{$term}%")
            ->orWhere('product_code', 'like', "%{$term}%")
            ->limit(10)
            ->get();
    }

    public function createProductFromSearch()
    {
        $name = trim($this->searchProduct);
        if (!$name) return;

        $this->dispatch('openModal', 'addProductModal');
        $this->productResults = [];
    }

    public function addProductToCart($productId)
    {
        $product = ProductDetail::find($productId);
        if (!$product) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Product not found']);
            return;
        }

        // Check if product already in cart
        if (isset($this->cart[$productId])) {
            $this->quantities[$productId] += 1;
        } else {
            $this->cart[$productId] = [
                'product_id' => $product->id,
                'product_name' => $product->product_name,
                'product_code' => $product->product_code,
                'image' => $product->image,
                'unit_price' => $product->selling_price,
            ];
            $this->quantities[$productId] = 1;
            $this->prices[$productId] = $product->selling_price;
        }

        $this->searchProduct = '';
        $this->productResults = [];

        $this->dispatch('searchSelected', [
            'type' => 'product',
            'name' => $product->product_name,
        ]);

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Product added to cart']);
    }

    public function updateQuantity($productId, $qty)
    {
        $qty = max(1, intval($qty));
        if (isset($this->quantities[$productId])) {
            $this->quantities[$productId] = $qty;
        }
    }

    public function updatePrice($productId, $price)
    {
        $price = max(0, floatval($price));
        if (isset($this->prices[$productId])) {
            $this->prices[$productId] = $price;
        }
    }

    public function removeItem($productId)
    {
        unset($this->cart[$productId]);
        unset($this->quantities[$productId]);
        unset($this->prices[$productId]);
    }

    public function getTotalAmount()
    {
        $sum = 0;
        foreach ($this->cart as $id => $item) {
            $price = $this->prices[$id] ?? $item['unit_price'];
            $qty = $this->quantities[$id] ?? 1;
            $sum += ($price * $qty);
        }
        return $sum;
    }

    public function getGrandTotal()
    {
        $total = $this->getTotalAmount();
        $adjust = floatval($this->adjustAmount);
        return max(0, $total - $adjust);
    }

    public function completePurchase()
    {
        if (empty($this->cart)) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Cart is empty.']);
            return;
        }

        if (!$this->selectedCustomer) {
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Please select a customer.']);
            return;
        }

        DB::beginTransaction();
        try {
            $total = $this->getTotalAmount();
            $grand = $this->getGrandTotal();

            $purchase = Purchase::create([
                'customer_id' => $this->selectedCustomer->id,
                'total_amount' => $total,
                'discount' => floatval($this->adjustAmount),
                'grand_total' => $grand,
                'notes' => $this->notes,
                'created_by' => auth()->id() ?? null,
            ]);

            foreach ($this->cart as $id => $item) {
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $this->quantities[$id] ?? 1,
                    'unit_price' => $this->prices[$id] ?? $item['unit_price'],
                    'subtotal' => ($this->prices[$id] ?? $item['unit_price']) * ($this->quantities[$id] ?? 1),
                ]);

                // Increment product stock in ProductDetail (if present) and ProductStock table
                try {
                    $product = ProductDetail::find($item['product_id']);
                    $qty = intval($this->quantities[$id] ?? 1);
                    if ($product) {
                        if (isset($product->stock_quantity)) {
                            $product->increment('stock_quantity', $qty);
                        }

                        // Update ProductStock.total_stock (create record if missing)
                        $productStock = ProductStock::firstOrCreate(
                            ['product_id' => $product->id],
                            ['total_stock' => 0, 'damage_stock' => 0]
                        );
                        $productStock->increment('total_stock', $qty);
                    }
                } catch (Exception $e) {
                    Log::error('Failed to update product stock records: ' . $e->getMessage());
                }
            }

            // Create payment record for this purchase
            Payment::create([
                'sale_id' => null,
                'customer_id' => $this->selectedCustomer->id,
                'amount' => $grand,
                'payment_method' => 'purchase_order',
                'payment_reference' => null,
                'card_number' => null,
                'bank_name' => null,
                'is_completed' => 1,
                'payment_date' => now(),
                'due_date' => null,
                'due_payment_method' => null,
                'due_payment_attachment' => null,
                'status' => 'paid',
                'applied_to' => null,
            ]);

            // Update or create customer account and adjust balances
            $account = CustomerAccount::firstOrCreate(
                ['customer_id' => $this->selectedCustomer->id],
                [
                    'back_forward_amount' => 0,
                    'current_due_amount' => 0,
                    'paid_due' => 0,
                    'total_due' => 0,
                    'advance_amount' => 0,
                ]
            );

            $remaining = $grand;

            // Reduce back_forward_amount first
            if (($account->back_forward_amount ?? 0) >= $remaining) {
                $account->back_forward_amount = ($account->back_forward_amount ?? 0) - $remaining;
                $remaining = 0;
            } else {
                $remaining -= ($account->back_forward_amount ?? 0);
                $account->back_forward_amount = 0;
            }

            // Then reduce current_due_amount
            if ($remaining > 0) {
                if (($account->current_due_amount ?? 0) >= $remaining) {
                    $account->current_due_amount = ($account->current_due_amount ?? 0) - $remaining;
                    $remaining = 0;
                } else {
                    $remaining -= ($account->current_due_amount ?? 0);
                    $account->current_due_amount = 0;
                }
            }

            // Any remaining becomes advance
            if ($remaining > 0) {
                $account->advance_amount = ($account->advance_amount ?? 0) + $remaining;
                $remaining = 0;
            }

            // Recalculate total_due
            $account->total_due = ($account->back_forward_amount ?? 0) + ($account->current_due_amount ?? 0);

            // Increase paid_due by grand (optional tracking)
            $account->paid_due = ($account->paid_due ?? 0) + $grand;

            $account->save();

            DB::commit();

            // Notify other components to refresh customer account/modal data
            $this->dispatch('refreshCustomerAccounts', $this->selectedCustomer->id);

            // Reset form
            $this->cart = [];
            $this->quantities = [];
            $this->prices = [];
            $this->adjustAmount = 0;
            $this->notes = '';
            $this->selectedCustomer = null;
            $this->searchCustomer = '';

            $this->dispatch('toast', ['type' => 'success', 'message' => 'Purchase completed successfully.']);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Purchase failed: ' . $e->getMessage());
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Failed to complete purchase: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.admin.create-purchase');
    }
}
