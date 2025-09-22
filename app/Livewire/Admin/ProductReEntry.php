<?php

namespace App\Livewire\Admin;

use App\Models\ProductDetail;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\CustomerAccount;
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

    // Product search/selection on right
    public $productSearch = '';
    public $productResults = [];
    public $selectedProductId = null;
    public $selectedProduct = null;

    // Re-entry inputs
    public $addStock = 0;      // re-entry into available stock
    public $addDamage = 0;     // increases damage only

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
            ->orderBy('created_at', 'desc');
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
            // Load invoice items for display (product name, quantity, amount)
            $items = SalesItem::with('product')
                ->where('sale_id', $sale->id)
                ->orderBy('id')
                ->get();
            $this->selectedInvoiceItems = $items->map(function ($i) {
                $qty = (int)($i->quantity ?? 0);
                $price = (float)($i->price ?? 0);
                $discount = (float)($i->discount ?? 0);
                $amount = max(0, ($price * $qty) - $discount);
                return [
                    'id' => $i->id,
                    'product_id' => $i->product_id,
                    'product_name' => $i->product->product_name ?? ($i->product->name ?? 'Product'),
                    'quantity' => $qty,
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
        // Clear product search/results so the selection is focused
        $this->productSearch = '';
        $this->productResults = [];
    }

    public function updateStock()
    {
        $this->validate([
            'addStock' => 'required|integer|min:0',
            'addDamage' => 'required|integer|min:0',
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

        // Require selecting a customer or an invoice context for re-entry
        if (!$this->selectedCustomerId && !$this->selectedInvoiceId) {
            $this->addError('selectedCustomerId', 'Please select a customer or an invoice before processing re-entry.');
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Missing Selection',
                'text' => 'Please select a customer or an invoice before processing re-entry.'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            // 1) Update product stock/damage and sold
            $product = ProductDetail::lockForUpdate()->find($this->selectedProduct->id);
            $reEntryUnits = max(0, (int)$this->addStock);
            $damageUnits = max(0, (int)$this->addDamage);
            $totalReturnUnits = $reEntryUnits + $damageUnits;

            $product->stock_quantity = ($product->stock_quantity ?? 0) + $reEntryUnits;
            $product->damage_quantity = ($product->damage_quantity ?? 0) + $damageUnits;
            // Decrease sold for all returned units (re-entry + damage)
            $product->sold = max(0, (int)($product->sold ?? 0) - $totalReturnUnits);
            $product->save();

            // 2) Compute financials
            $unitPrice = floatval($product->selling_price ?? 0);
            $creditUnits = $reEntryUnits; // used for account current-due reductions only
            $creditAmount = $creditUnits * $unitPrice;
            // Total return value (re-entry + damage) affects sale total and payments
            $returnValue = ($reEntryUnits + $damageUnits) * $unitPrice;

            // 3) Reduce dues if applicable (only re-entry portion)
            if ($creditAmount > 0 && $this->selectedCustomerId) {
                $remaining = $creditAmount;

                // Check if customer actually has dues > 0
                $hasDue = CustomerAccount::where('customer_id', $this->selectedCustomerId)
                    ->where('total_due', '>', 0)
                    ->exists();

                if (!$hasDue) {
                    // Fully paid scenario: update sale total and adjust payments/cheques; don't touch accounts
                    if ($this->selectedInvoiceId) {
                        $sale = Sale::find($this->selectedInvoiceId);
                        if ($sale) {
                            // Adjust SalesItem quantities for this sale and product
                            $remainingUnitsToRevert = $totalReturnUnits;
                            $items = SalesItem::where('sale_id', $sale->id)
                                ->where('product_id', $this->selectedProduct->id)
                                ->orderBy('id', 'asc')
                                ->get();
                            foreach ($items as $item) {
                                if ($remainingUnitsToRevert <= 0) break;
                                $reduce = min($remainingUnitsToRevert, (int)$item->quantity);
                                $item->quantity = max(0, (int)$item->quantity - $reduce);
                                // Recalculate total based on new quantity
                                $newTotal = ($item->price ?? 0) * $item->quantity;
                                $item->total = max(0, $newTotal);
                                $item->save();
                                if ((int)$item->quantity === 0) {
                                    // optionally delete empty rows
                                    // $item->delete();
                                }
                                $remainingUnitsToRevert -= $reduce;
                            }
                            // Reduce sale total by the full return value (re-entry + damage)
                            $sale->total_amount = max(0, floatval($sale->total_amount) - $returnValue);
                            $sale->save();

                            // Reduce payments amounts by the return value (latest first)
                            $remainingPay = $returnValue;
                            $payments = \App\Models\Payment::where('sale_id', $sale->id)
                                ->orderBy('created_at', 'desc')
                                ->get();
                            foreach ($payments as $p) {
                                if ($remainingPay <= 0) break;
                                $deduct = min($remainingPay, floatval($p->amount));
                                $p->amount = max(0, floatval($p->amount) - $deduct);
                                $p->save();

                                // If this payment is cheque-backed and now zero, mark cheques as returned
                                if ($p->amount <= 0) {
                                    $cheques = \App\Models\Cheque::where('payment_id', $p->id)->get();
                                    foreach ($cheques as $ch) {
                                        $ch->status = 'cancel';
                                        $ch->save();
                                    }
                                }
                                $remainingPay -= $deduct;
                            }

                            // If still remaining after reducing payments (rare), add as advance credit to accounts
                            if ($remainingPay > 0) {
                                $lastAccount = CustomerAccount::where('customer_id', $this->selectedCustomerId)->latest()->first();
                                if ($lastAccount) {
                                    $lastAccount->back_forward_amount = floatval($lastAccount->back_forward_amount) - $remainingPay; // negative for advance
                                    $lastAccount->total_due = max(0, floatval($lastAccount->current_due_amount) + max(0, floatval($lastAccount->back_forward_amount)));
                                    $lastAccount->save();
                                } else {
                                    CustomerAccount::create([
                                        'customer_id' => $this->selectedCustomerId,
                                        'sale_id' => null,
                                        'back_forward_amount' => -$remainingPay,
                                        'current_due_amount' => 0,
                                        'paid_due' => 0,
                                        'total_due' => 0,
                                        'notes' => 'Advance from return re-entry on ' . now()->format('Y-m-d H:i:s'),
                                    ]);
                                }
                            }
                        }
                    }
                } else {
                    // Has dues: apply to selected invoice current due first
                    if ($this->selectedInvoiceId) {
                        $acc = CustomerAccount::where('customer_id', $this->selectedCustomerId)
                            ->where('sale_id', $this->selectedInvoiceId)
                            ->orderBy('created_at')
                            ->first();
                        if ($acc) {
                            $payForCurrent = min($remaining, floatval($acc->current_due_amount));
                            $acc->current_due_amount = floatval($acc->current_due_amount) - $payForCurrent;
                            $remaining -= $payForCurrent;

                            $acc->total_due = max(0, floatval($acc->current_due_amount) + max(0, floatval($acc->back_forward_amount)));
                            $acc->save();

                            if ($acc->sale_id) {
                                $sale = Sale::find($acc->sale_id);
                                if ($sale) {
                                    // Adjust SalesItem quantities for this sale and product
                                    $remainingUnitsToRevert = $totalReturnUnits;
                                    $items = SalesItem::where('sale_id', $sale->id)
                                        ->where('product_id', $this->selectedProduct->id)
                                        ->orderBy('id', 'asc')
                                        ->get();
                                    foreach ($items as $item) {
                                        if ($remainingUnitsToRevert <= 0) break;
                                        $reduce = min($remainingUnitsToRevert, (int)$item->quantity);
                                        $item->quantity = max(0, (int)$item->quantity - $reduce);
                                        // Recalculate total based on new quantity
                                        $newTotal = ($item->price ?? 0) * $item->quantity;
                                        $item->total = max(0, $newTotal);
                                        $item->save();
                                        if ((int)$item->quantity === 0) {
                                            // optionally delete empty rows
                                            // $item->delete();
                                        }
                                        $remainingUnitsToRevert -= $reduce;
                                    }
                                    $sale->due_amount = $acc->total_due;
                                    // Reduce sale total by full return value (re-entry + damage)
                                    $sale->total_amount = max(0, floatval($sale->total_amount) - $returnValue);
                                    $sale->save();

                                    // Reduce payments by full return value
                                    $remainingPay = $returnValue;
                                    $payments = \App\Models\Payment::where('sale_id', $sale->id)
                                        ->orderBy('created_at', 'desc')
                                        ->get();
                                    foreach ($payments as $p) {
                                        if ($remainingPay <= 0) break;
                                        $deduct = min($remainingPay, floatval($p->amount));
                                        $p->amount = max(0, floatval($p->amount) - $deduct);
                                        $p->save();
                                        if ($p->amount <= 0) {
                                            $cheques = \App\Models\Cheque::where('payment_id', $p->id)->get();
                                            foreach ($cheques as $ch) {
                                                $ch->status = 'cancel';
                                                $ch->save();
                                            }
                                        }
                                        $remainingPay -= $deduct;
                                    }

                                    // If leftover after reducing payments, add to accounts as advance
                                    if ($remainingPay > 0) {
                                        $lastAccount = CustomerAccount::where('customer_id', $this->selectedCustomerId)->latest()->first();
                                        if ($lastAccount) {
                                            $lastAccount->back_forward_amount = floatval($lastAccount->back_forward_amount) - $remainingPay;
                                            $lastAccount->total_due = max(0, floatval($lastAccount->current_due_amount) + max(0, floatval($lastAccount->back_forward_amount)));
                                            $lastAccount->save();
                                        } else {
                                            CustomerAccount::create([
                                                'customer_id' => $this->selectedCustomerId,
                                                'sale_id' => null,
                                                'back_forward_amount' => -$remainingPay,
                                                'current_due_amount' => 0,
                                                'paid_due' => 0,
                                                'total_due' => 0,
                                                'notes' => 'Advance from return re-entry on ' . now()->format('Y-m-d H:i:s'),
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                        // IMPORTANT: When an invoice is selected, do NOT reduce other accounts; if any credit remains, store as advance
                        if ($remaining > 0) {
                            $lastAccount = CustomerAccount::where('customer_id', $this->selectedCustomerId)->latest()->first();
                            if ($lastAccount) {
                                $lastAccount->back_forward_amount = floatval($lastAccount->back_forward_amount) - $remaining;
                                $lastAccount->total_due = max(0, floatval($lastAccount->current_due_amount) + max(0, floatval($lastAccount->back_forward_amount)));
                                $lastAccount->save();
                            } else {
                                CustomerAccount::create([
                                    'customer_id' => $this->selectedCustomerId,
                                    'sale_id' => null,
                                    'back_forward_amount' => -$remaining,
                                    'current_due_amount' => 0,
                                    'paid_due' => 0,
                                    'total_due' => 0,
                                    'notes' => 'Advance from return re-entry on ' . now()->format('Y-m-d H:i:s'),
                                ]);
                            }
                            $remaining = 0;
                        }
                    }

                    // If no invoice selected, and remaining credit, reduce across other dues (current only)
                    if (!$this->selectedInvoiceId && $remaining > 0) {
                        $accounts = CustomerAccount::where('customer_id', $this->selectedCustomerId)
                            ->where('total_due', '>', 0)
                            ->orderBy('created_at')
                            ->get();
                        foreach ($accounts as $acc) {
                            if ($remaining <= 0) break;
                            $dueForCurrent = floatval($acc->current_due_amount);
                            $payForCurrent = min($remaining, $dueForCurrent);
                            $acc->current_due_amount = $dueForCurrent - $payForCurrent;
                            $remaining -= $payForCurrent;

                            $acc->total_due = max(0, floatval($acc->current_due_amount) + max(0, floatval($acc->back_forward_amount)));
                            $acc->save();

                            if ($acc->sale_id) {
                                $sale = Sale::find($acc->sale_id);
                                if ($sale) {
                                    $sale->due_amount = $acc->total_due;
                                    $sale->save();
                                }
                            }
                        }
                    }
                    // Note: No creation of advance/negative back_forward; leftover credit is ignored per requirement
                }
            }

            DB::commit();

            // Reset inputs, refresh selections
            $this->addStock = 0;
            $this->addDamage = 0;
            $this->selectedProduct = $product->fresh('category');

            // Success alert via SweetAlert
            $this->dispatch('swal', [
                'icon' => 'success',
                'title' => 'Success',
                'text' => 'Re-entry processed successfully.'
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->addError('submit', 'Failed to process re-entry: ' . $e->getMessage());
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to process re-entry: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        // We primarily drive results through updated* handlers; render supplies current selections.
        return view('livewire.admin.product-re-entry');
    }
}