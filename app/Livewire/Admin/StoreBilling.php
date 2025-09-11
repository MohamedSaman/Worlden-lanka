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
    public $quantityTypes = [];
    public $productDetails = null;
    public $subtotal = 0;
    public $totalDiscount = 0;
    public $grandTotal = 0;

    public $customers = [];
    public $customerId = null;
    public $customerType = '';

    public $newCustomerName = '';
    public $newCustomerPhone = '';
    public $newCustomerEmail = '';
    public $newCustomerType = '';
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

    public $banks = [];
    public $availableQuantityTypes = [];

    protected $listeners = ['quantityUpdated' => 'updateTotals'];

    public function mount()
    {
        $this->loadCustomers();
        $this->loadBanks();
        $this->loadQuantityTypes();
        $this->updateTotals();
        $this->balanceDueDate = date('Y-m-d', strtotime('+7 days'));
    }
    public function loadBanks()
    {
        $this->banks = [
            'Bank of Ceylon (BOC)',
            'Commercial Bank of Ceylon (ComBank)',
            'Hatton National Bank (HNB)',
            'People\'s Bank',
            'Sampath Bank',
            'National Development Bank (NDB)',
            'DFCC Bank',
            'Nations Trust Bank (NTB)',
            'Seylan Bank',
            'Amana Bank',
            'Cargills Bank',
            'Pan Asia Banking Corporation',
            'Union Bank of Colombo',
            'Bank of China Ltd',
            'Citibank, N.A.',
            'Habib Bank Ltd',
            'Indian Bank',
            'Indian Overseas Bank',
            'MCB Bank Ltd',
            'Public Bank Berhad',
            'Standard Chartered Bank',
        ];
    }
    public function loadQuantityTypes()
    {
        $this->availableQuantityTypes = [
            'pcs' => 'Pieces',
            'box' => 'Box',
            'pack' => 'Pack',
            'set' => 'Set',
            'doz' => 'Dozen',
            'roll' => 'Roll',
        ];
    }

    public function loadCustomers()
    {
        $this->customers = Customer::orderBy('name')->get();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 1) {
            $this->searchResults = ProductDetail::join('product_categories', 'product_categories.id', '=', 'product_details.category_id')
                ->where('product_details.product_name', 'LIKE', '%' . $this->search . '%')
                ->orWhere('product_details.product_code', 'LIKE', '%' . $this->search . '%')
                ->select('product_details.*') // Select only product_details columns to avoid ambiguity
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    /**
     * MODIFIED: This method now adds new items to the top of the cart.
     */
    public function addToCart($productId)
    {
        $product = ProductDetail::find($productId);

        if (!$product || $product->stock_quantity <= 0) {
            $this->dispatch('show-toast', ['type' => 'warning', 'message' => 'This product is out of stock.']);
            return;
        }

        if (isset($this->cart[$productId])) {
            if (($this->quantities[$productId] + 1) > $product->stock_quantity) {
                $this->dispatch('show-toast', ['type' => 'warning', 'message' => "Maximum available stock is {$product->stock_quantity}"]);
                return;
            }
            $this->quantities[$productId]++;
        } else {
            $newItem = [
                $productId => [
                    'id' => $product->id,
                    'name' => $product->product_name,
                    'code' => $product->product_code,
                    'brand' => $product->brand->name ?? 'N/A',
                    'image' => $product->image,
                    'price' => $product->selling_price,
                    'stock_quantity' => $product->stock_quantity,
                ]
            ];

            $this->cart = $newItem + $this->cart; // Prepend new item
            $this->prices[$productId] = $product->selling_price ?? 0;
            $this->quantities[$productId] = 1;
            $this->discounts[$productId] = $product->discount_price ?? 0;
            $this->quantityTypes[$productId] = ''; // Initialize quantity type
        }

        $this->search = '';
        $this->searchResults = [];
        $this->updateTotals();
    }

    public function updatedQuantities($value, $key)
    {
        $this->validateQuantity((int)$key);
    }

    public function updatedPrices($value, $key)
    {
        $value = max(0, floatval($value));
        $this->prices[$key] = $value;

        // Update max discount if needed
        if (isset($this->discounts[$key]) && $this->discounts[$key] > $value) {
            $this->discounts[$key] = $value;
        }

        $this->updateTotals();
    }

    public function updatedDiscounts($value, $key)
    {
        $price = $this->prices[$key] ?? $this->cart[$key]['price'] ?? 0;
        $this->discounts[$key] = max(0, min(floatval($value), $price));
        $this->updateTotals();
    }

    public function validateQuantity($productId)
    {
        if (!isset($this->cart[$productId]) || !isset($this->quantities[$productId])) {
            return;
        }

        $maxAvailable = $this->cart[$productId]['stock_quantity'];
        $currentQuantity = filter_var($this->quantities[$productId], FILTER_VALIDATE_INT);

        if ($currentQuantity === false || $currentQuantity < 1) {
            $this->quantities[$productId] = 1;
            $this->dispatch('show-toast', ['type' => 'warning', 'message' => 'Minimum quantity is 1']);
        } elseif ($currentQuantity > $maxAvailable) {
            $this->quantities[$productId] = $maxAvailable;
            $this->dispatch('show-toast', ['type' => 'warning', 'message' => "Maximum stock is {$maxAvailable}"]);
        }
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
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'message' => "Maximum available quantity is {$maxAvailable}"
            ]);
        }

        $this->quantities[$productId] = $quantity;
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
        unset($this->quantityTypes[$productId]);
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
            $qty = $this->quantities[$id] ?? 1;
            $discount = $this->discounts[$id] ?? 0;
            $this->subtotal += $price * $qty;
            $this->totalDiscount += $discount * $qty;
        }

        $this->grandTotal = $this->subtotal - $this->totalDiscount;
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->quantities = [];
        $this->discounts = [];
        $this->prices = [];
        $this->quantityTypes = [];
        $this->updateTotals();
    }

    public function saveCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required',
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
        $this->customerId = $customer->id; // Auto-select the newly created customer

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
                'customer_type'    => Customer::find($this->customerId)->type ?? 'N/A',
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

                $quantityToSell = $this->quantities[$id];

                if ($productStock->stock_quantity < $quantityToSell) {
                    throw new Exception("Insufficient stock for item: {$item['name']}. Available: {$productStock->stock_quantity}");
                }

                $price = $this->prices[$id] ?? $item['price'] ?? 0;
                $itemDiscount = $this->discounts[$id] ?? 0;
                $qtyType = $this->quantityTypes[$id] ?? '';
                $total = ($price * $quantityToSell) - ($itemDiscount * $quantityToSell);

                // dd($qtyType);
                SalesItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'quantity'   => $quantityToSell,
                    'quantity_type' => $qtyType,
                    'price'      => $price,
                    'discount'   => $itemDiscount,
                    'total'      => $total,
                ]);

                $productStock->stock_quantity -= $quantityToSell;
                $productStock->sold += $quantityToSell;
                $productStock->save();

                $totalSoldQty += $quantityToSell;
                $totalSoldVal += $total;
            }

            $adminSale->sold_quantity = $totalSoldQty;
            $adminSale->sold_value = $totalSoldVal;
            $adminSale->status = $totalSoldQty == $adminSale->total_quantity ? 'completed' : 'partial';
            $adminSale->save();

            if ($this->paymentType == 'full') {
                if (floatval($this->cashAmount) > 0) {
                    Payment::create([
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
                Payment::create([
                    'sale_id'         => $sale->id,
                    'admin_sale_id'   => $adminSale->id,
                    'amount'          => $this->grandTotal,
                    'payment_method'  => 'credit',
                    'is_completed'    => false,
                    'status'          => null,
                    'due_date'        => $this->balanceDueDate ?? now()->addDays(7),
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
        $this->balanceDueDate = date('Y-m-d', strtotime('+7 days'));
        $this->balancePaymentReceiptImage = null;
        $this->balancePaymentReceiptImagePreview = null;
        $this->balanceBankName = '';
    }

    public function render()
    {
        return view('livewire.admin.store-billing');
    }
}