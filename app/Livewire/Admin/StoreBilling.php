<?php

namespace App\Livewire\Admin;

use Exception;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Cheque;
use Livewire\Component;
use App\Models\Customer;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;
use App\Models\AdminSale;
use App\Models\ProductDetail;
use App\Models\SalesItem;

#[Title("Store Billing")]
#[Layout('components.layouts.admin')]
class StoreBilling extends Component
{
    use WithFileUploads;

    public $search = '';
    public $searchResults = [];
    public $cart = [];
    public $quantities = [];
    public $discounts = [];
    public $prices = [];
    public $productDetails = null;
    public $subtotal = 0;
    public $totalDiscount = 0;
    public $grandTotal = 0;

    public $customers = [];
    public $customerId = null;
    public $customerType = 'retail';

    public $newCustomerName = '';
    public $newCustomerPhone = '';
    public $newCustomerEmail = '';
    public $newCustomerType = 'retail';
    public $newCustomerAddress = '';
    public $newCustomerNotes = '';

    public $saleNotes = '';
    public $paymentType = 'full';
    public $paymentMethod = '';
    public $paymentReceiptImage;
    public $paymentReceiptImagePreview = null;
    public $bankName = '';

    public $initialPaymentAmount = 0;
    public $initialPaymentMethod = '';
    public $initialPaymentReceiptImage;
    public $initialPaymentReceiptImagePreview = null;
    public $initialBankName = '';

    public $balanceAmount = 0;
    public $balancePaymentMethod = '';
    public $balanceDueDate = '';
    public $balancePaymentReceiptImage;
    public $balancePaymentReceiptImagePreview = null;
    public $balanceBankName = '';

    public $lastSaleId = null;
    public $showReceipt = false;
    public $receipt = null;

    public $cashAmount = 0;
    public $cheques = [];
    public $newCheque = [
        'number' => '',
        'bank' => '',
        'date' => '',
        'amount' => '',
    ];
    public $duePaymentMethod = '';
    public $duePaymentAttachment;
    public $duePaymentAttachmentPreview = null;

    protected $listeners = ['quantityUpdated' => 'updateTotals'];

    public function mount()
    {
        $this->loadCustomers();
        $this->updateTotals();
        $this->balanceDueDate = date('Y-m-d', strtotime('+7 days'));
    }

    public function loadCustomers()
    {
        $this->customers = Customer::orderBy('name')->get();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->searchResults = ProductDetail::join('product_categories', 'product_categories.id', '=', 'product_details.category_id')
                ->where('product_details.product_name', 'LIKE', '%' . $this->search . '%')
                ->orWhere('product_details.product_code', 'LIKE', '%' . $this->search . '%')
                ->select('product_details.*') // Select only product_details columns to avoid ambiguity
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function addToCart($productId)
    {
        $product = ProductDetail::where('id', $productId)->first();

        if (!$product || $product->stock_quantity <= 0) {
            $this->dispatch('showToast', [
                'type' => 'warning',
                'message' => 'This product is out of stock.'
            ]);
            return;
        }

        $existingItem = collect($this->cart)->firstWhere('id', $productId);

        if ($existingItem) {
            if (($this->quantities[$productId] + 1) > $product->stock_quantity) {
                $this->dispatch('showToast', [
                    'type' => 'warning',
                    'message' => "Maximum available quantity is {$product->stock_quantity}"
                ]);
                return;
            }
            $this->quantities[$productId]++;
        } else {
            $discountPrice = $product->discount_price ?? 0;

            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->product_name,
                'code' => $product->product_code,
                'brand' => $product->brand,
                'image' => $product->image,
                'price' => $product->selling_price,
                'stock_quantity' => $product->stock_quantity,
            ];

            $this->prices[$productId] = $product->selling_price ?? 0;
            $this->quantities[$productId] = 1;
            $this->discounts[$productId] = $discountPrice;
        }

        $this->search = '';
        $this->searchResults = [];
        $this->updateTotals();
    }

    public function validateQuantity($productId)
    {
        if (!isset($this->cart[$productId]) || !isset($this->quantities[$productId])) {
            return;
        }

        $productStock = ProductDetail::find($productId);
        if (!$productStock) {
            return;
        }

        $soldCount = $productStock->sold ?? 0;
        $maxAvailable = max(0, $productStock->stock_quantity - $soldCount);

        $currentQuantity = (int)$this->quantities[$productId];

        if ($currentQuantity <= 0) {
            $this->quantities[$productId] = 1;
            $this->dispatch('showToast', [
                'type' => 'warning',
                'message' => 'Minimum quantity is 1'
            ]);
        } elseif ($currentQuantity > $maxAvailable) {
            $this->quantities[$productId] = $maxAvailable;
            $this->dispatch('showToast', [
                'type' => 'warning',
                'message' => "Maximum available quantity is {$maxAvailable}"
            ]);
        }

        $this->cart[$productId]['Status'] = $maxAvailable > 0 ? 'Available' : 'Unavailable';
        $this->updateTotals();
    }

    public function updateQuantity($productId, $quantity)
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $maxAvailable = $this->cart[$productId]['stock_quantity'];

        if ($quantity <= 0) {
            $quantity = 1;
        } elseif ($quantity > $maxAvailable) {
            $quantity = $maxAvailable;
            $this->dispatch('showToast', [
                'type' => 'warning',
                'message' => "Maximum available quantity is {$maxAvailable}"
            ]);
        }

        $this->quantities[$productId] = $quantity;
        $this->cart[$productId]['Status'] = $maxAvailable > 0 ? 'Available' : 'Unavailable';
        $this->updateTotals();
    }

    public function updatePrice($productId, $price)
    {
        if (!isset($this->cart[$productId])) return;

        $price = floatval($price);
        if ($price < 0) $price = 0;

        $this->cart[$productId]['price'] = $price;
        $this->prices[$productId] = $price;
        $this->updateTotals();
    }

    public function updateDiscount($productId, $discount)
    {
        if (!isset($this->cart[$productId])) return;

        $this->discounts[$productId] = max(0, min($discount, $this->cart[$productId]['price'] ?? 0));
        $this->updateTotals();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        unset($this->quantities[$productId]);
        unset($this->discounts[$productId]);
        unset($this->prices[$productId]);
        $this->updateTotals();
    }

    public function showDetail($productId)
    {
        $this->productDetails = ProductDetail::select(
            'id',
            'product_name',
            'product_code',
            'selling_price',
            'stock_quantity',
            'damage_quantity',
            'sold',
            DB::raw("(stock_quantity) as available_stock")
        )
            ->where('id', $productId)
            ->first();

        $this->js('$("#viewDetailModal").modal("show")');
    }

    public function updateTotals()
    {
        $this->subtotal = 0;
        $this->totalDiscount = 0;

        foreach ($this->cart as $id => $item) {
            $price = $this->prices[$id] ?? $item['price'] ?? 0;
            $this->subtotal += $price * $this->quantities[$id];
            $this->totalDiscount += ($this->discounts[$id] ?? 0) * $this->quantities[$id];
        }

        $this->grandTotal = $this->subtotal - $this->totalDiscount;
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->quantities = [];
        $this->discounts = [];
        $this->prices = [];
        $this->updateTotals();
    }

    public function saveCustomer()
    {
        $this->validate([
            'newCustomerPhone' => 'required',
        ]);

        $customer = Customer::create([
            'name' => $this->newCustomerName,
            'phone' => $this->newCustomerPhone,
            'email' => $this->newCustomerEmail,
            'type' => $this->newCustomerType,
            'address' => $this->newCustomerAddress,
            'notes' => $this->newCustomerNotes,
        ]);

        $this->loadCustomers();

        $this->newCustomerName = '';
        $this->newCustomerPhone = '';
        $this->newCustomerEmail = '';
        $this->newCustomerAddress = '';
        $this->newCustomerNotes = '';

        $this->js('$("#addCustomerModal").modal("hide")');
        $this->js('swal.fire("Success", "Customer added successfully!", "success")');
    }

    public function calculateBalanceAmount()
    {
        if ($this->paymentType == 'partial') {
            $this->balanceAmount = $this->grandTotal - $this->initialPaymentAmount;
        } else {
            $this->balanceAmount = 0;
        }
    }

    public function updatedPaymentType($value)
    {
        if ($value == 'partial') {
            $this->calculateBalanceAmount();
        } else {
            $this->balanceAmount = 0;
        }
    }

    public function addCheque()
    {
        if (
            empty($this->newCheque['number']) ||
            empty($this->newCheque['bank']) ||
            empty($this->newCheque['date']) ||
            floatval($this->newCheque['amount']) <= 0
        ) {
            $this->js('swal.fire("Error", "Please fill all cheque details.", "error")');
            return;
        }

        $this->cheques[] = [
            'number' => $this->newCheque['number'],
            'bank' => $this->newCheque['bank'],
            'date' => $this->newCheque['date'],
            'amount' => floatval($this->newCheque['amount']),
        ];

        $this->newCheque = [
            'number' => '',
            'bank' => '',
            'date' => '',
            'amount' => '',
        ];
    }

    public function removeCheque($index)
    {
        if (isset($this->cheques[$index])) {
            array_splice($this->cheques, $index, 1);
        }
    }

    public function completeSale()
    {
        if (empty($this->cart)) {
            $this->js('swal.fire("Error", "Please add items to the cart.", "error")');
            return;
        }

        $this->validate([
            'customerId' => 'required',
            'paymentType' => 'required|in:full,partial',
        ]);

        if ($this->paymentType === 'full') {
            $totalChequeAmount = collect($this->cheques)->sum('amount');
            $totalPaid = floatval($this->cashAmount) + floatval($totalChequeAmount);

            if ($totalPaid != $this->grandTotal) {
                $this->js('swal.fire("Error", "Cash + Cheque total must equal Grand Total.", "error")');
                return;
            }

            foreach ($this->cheques as $cheque) {
                if (empty($cheque['number']) || empty($cheque['bank']) || empty($cheque['date']) || floatval($cheque['amount']) <= 0) {
                    $this->js('swal.fire("Error", "Please fill all cheque details.", "error")');
                    return;
                }
            }
        }

        try {
            DB::beginTransaction();

            $sale = Sale::create([
                'invoice_number'   => Sale::generateInvoiceNumber(),
                'customer_id'      => $this->customerId,
                'user_id'          => auth()->id(),
                'customer_type'    => Customer::find($this->customerId)->type,
                'subtotal'         => $this->subtotal,
                'discount_amount'  => $this->totalDiscount,
                'total_amount'     => $this->grandTotal,
                'payment_type'     => $this->paymentType,
                'payment_status'   => $this->paymentType === 'full' ? 'paid' : 'partial',
                'notes'            => $this->saleNotes,
                'due_amount'       => $this->balanceAmount,
            ]);

            $adminSale = AdminSale::create([
                'sale_id'        => $sale->id,
                'admin_id'       => auth()->id(),
                'total_quantity' => array_sum($this->quantities),
                'total_value'    => $this->grandTotal,
                'sold_quantity'  => 0,
                'sold_value'     => 0,
                'status'         => 'partial',
            ]);

            $totalSoldQty = 0;
            $totalSoldVal = 0;

            foreach ($this->cart as $id => $item) {
                $productStock = ProductDetail::where('id', $item['id'])->first();

                if (!$productStock) {
                    throw new Exception("Product not found: {$item['name']}");
                }

                $availableStock = $productStock->stock_quantity ?? 0;

                if ($availableStock < $this->quantities[$id]) {
                    throw new Exception("Insufficient stock for item: {$item['name']}. Available: {$availableStock}");
                }

                $soldCount = $productStock->sold ?? 0;
                $productStock->sold = $soldCount + $this->quantities[$id];

                $price = $this->prices[$id] ?? $item['price'] ?? 0;
                $itemDiscount = $this->discounts[$id] ?? 0;
                $total = ($price * $this->quantities[$id]) - ($itemDiscount * $this->quantities[$id]);

                SalesItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'quantity'   => $this->quantities[$id],
                    'price'      => $price,
                    'discount'   => $itemDiscount,
                    'total'      => $total,
                ]);

                $productStock->stock_quantity -= $this->quantities[$id];
                $productStock->sold = $soldCount + $this->quantities[$id];
                $productStock->save();

                $totalSoldQty += $this->quantities[$id];
                $totalSoldVal += $total;
            }

            $adminSale->sold_quantity = $totalSoldQty;
            $adminSale->sold_value = $totalSoldVal;
            $adminSale->status = $totalSoldQty == $adminSale->total_quantity ? 'completed' : 'partial';
            $adminSale->save();

            if ($this->paymentType == 'full') {
                if (floatval($this->cashAmount) > 0) {
                    $payment = Payment::create([
                        'sale_id'         => $sale->id,
                        'admin_sale_id'   => $adminSale->id,
                        'amount'          => floatval($this->cashAmount),
                        'payment_method'  => 'cash',
                        'is_completed'    => true,
                        'payment_date'    => now(),
                        'status'          => 'Paid',
                    ]);
                }
                foreach ($this->cheques as $cheque) {
                    $payment = Payment::create([
                        'sale_id'         => $sale->id,
                        'admin_sale_id'   => $adminSale->id,
                        'amount'          => floatval($cheque['amount']),
                        'payment_method'  => 'cheque',
                        'payment_reference' => $cheque['number'],
                        'bank_name'       => $cheque['bank'],
                        'is_completed'    => true,
                        'payment_date'    => $cheque['date'],
                        'status'          => 'Paid',
                    ]);

                    Cheque::create([
                        'cheque_number' => $cheque['number'],
                        'cheque_date'   => $cheque['date'],
                        'bank_name'     => $cheque['bank'],
                        'cheque_amount' => $cheque['amount'],
                        'status'        => 'pending',
                        'customer_id'   => $this->customerId,
                        'payment_id'    => $payment->id,
                    ]);
                }
            } else {
                // Save payment as credit (due) in payments table
                Payment::create([
                    'sale_id'         => $sale->id,
                    'admin_sale_id'   => $adminSale->id,
                    'amount'          => $this->grandTotal,
                    'payment_method'  => 'credit',
                    'payment_reference' => null,
                    'bank_name'       => null,
                    'is_completed'    => false,
                    'payment_date'    => null,
                    'status'          => null,
                    'due_date'        => $this->balanceDueDate ?? null,
                ]);
            }

            DB::commit();

            $this->receipt = Sale::with(['customer', 'items.product', 'payments'])->find($sale->id);

            $this->lastSaleId = $sale->id;
            $this->showReceipt = true;
            $this->js('swal.fire("Success", "Sale completed successfully! Invoice #' . $sale->invoice_number . '", "success")');
            $this->clearCart();
            $this->resetPaymentInfo();
            $this->js('$("#receiptModal").modal("show")');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Admin sale error: ' . $e->getMessage());
            $this->js('swal.fire("Error", "An error occurred: ' . $e->getMessage() . '", "error")');
        }
    }

    public function resetPaymentInfo()
    {
        $this->paymentType = 'full';
        $this->paymentMethod = '';
        $this->paymentReceiptImage = null;
        $this->paymentReceiptImagePreview = null;
        $this->bankName = '';

        $this->cashAmount = 0;
        $this->cheques = [];
        $this->newCheque = [
            'number' => '',
            'bank' => '',
            'date' => '',
            'amount' => '',
        ];

        $this->initialPaymentAmount = 0;
        $this->initialPaymentMethod = '';
        $this->initialPaymentReceiptImage = null;
        $this->initialPaymentReceiptImagePreview = null;
        $this->initialBankName = '';

        $this->balanceAmount = 0;
        $this->balancePaymentMethod = '';
        $this->balanceDueDate = '';
        $this->balancePaymentReceiptImage = null;
        $this->balancePaymentReceiptImagePreview = null;
        $this->balanceBankName = '';
    }

    public function render()
    {
        return view('livewire.admin.store-billing');
    }
}
