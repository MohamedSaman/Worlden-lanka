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
use App\Models\CustomerAccount;
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
    public $deliveryNote = '';
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

    public $invoiceNumber = '';
    public $isInvoiceGenerated = false;
    public $invoiceDate = '';

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

    // New properties for edit mode
    public $isEditMode = false;
    public $editingSaleId = null;

    protected $listeners = ['quantityUpdated' => 'updateTotals'];

    public function mount()
    {
        $this->loadCustomers();
        $this->loadBanks();
        $this->loadQuantityTypes();
        $this->updateTotals();
        $this->balanceDueDate = date('Y-m-d', strtotime('+7 days'));
        $this->invoiceDate = date('Y-m-d');
        $this->generateInvoiceNumber();

        // Check if there's an edit parameter from URL
        if (request()->has('edit')) {
            $saleId = request()->get('edit');
            $this->loadSaleForEditing($saleId);
        }
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
            'yards' => 'Yards'
        ];
    }

    public function generateInvoiceNumber($useDate = null)
    {
        $prefix = 'INV-';

        $lastSale = Sale::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'DESC')
            ->first();

        $nextNumber = 1;
        if ($lastSale) {
            $lastNumber = intval(str_replace($prefix, '', $lastSale->invoice_number));
            $nextNumber = $lastNumber + 1;
        }

        $invoiceNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        while (Sale::where('invoice_number', $invoiceNumber)->exists()) {
            $nextNumber++;
            $invoiceNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        $this->invoiceNumber = $invoiceNumber;
        $this->isInvoiceGenerated = true;
    }

    public function updatedInvoiceDate($value)
    {
        // Invoice number does not depend on date anymore
    }

    public function validateInvoiceNumber()
    {
        if (empty($this->invoiceNumber)) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Invoice number cannot be empty'
            ]);
            $this->generateInvoiceNumber();
            return false;
        }

        // Skip validation if in edit mode and invoice number hasn't changed
        if ($this->isEditMode && $this->editingSaleId) {
            $currentSale = Sale::find($this->editingSaleId);
            if ($currentSale && $currentSale->invoice_number === $this->invoiceNumber) {
                return true;
            }
        }

        $existingInvoice = Sale::where('invoice_number', $this->invoiceNumber)->first();

        if ($existingInvoice) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Invoice Already Exists',
                'text' => 'This invoice number already exists in the database. Please use a different number.'
            ]);
            return false;
        }

        return true;
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
                ->select('product_details.*')
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

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

            $this->cart = $newItem + $this->cart;
            $this->prices[$productId] = $product->selling_price ?? 0;
            $this->quantities[$productId] = 1;
            $this->discounts[$productId] = $product->discount_price ?? 0;
            $this->quantityTypes[$productId] = '';
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
        $this->customerId = $customer->id;

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
            $this->initialPaymentAmount = min(floatval($this->initialPaymentAmount), $this->grandTotal);
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

    // NEW METHOD: Edit Sale
    public function editSale()
    {
        if (!$this->receipt || !$this->lastSaleId) {
            $this->js('swal.fire("Error", "No sale to edit", "error")');
            return;
        }

        $this->loadSaleForEditing($this->lastSaleId);

        // Close receipt modal and scroll to top
        $this->js('$("#receiptModal").modal("hide")');
        $this->js('window.scrollTo({ top: 0, behavior: "smooth" })');
    }

    // NEW METHOD: Load Sale for Editing (can be called from receipt or from URL)
    public function loadSaleForEditing($saleId)
    {
        try {
            $sale = Sale::with(['items.product', 'payments'])->find($saleId);

            if (!$sale) {
                $this->js('swal.fire("Error", "Sale not found", "error")');
                return;
            }

            // Set edit mode
            $this->isEditMode = true;
            $this->editingSaleId = $sale->id;

            // Load sale data into form
            $this->invoiceNumber = $sale->invoice_number;
            $this->invoiceDate = $sale->created_at->format('Y-m-d');
            $this->customerId = $sale->customer_id;
            $this->saleNotes = $sale->notes;
            $this->deliveryNote = $sale->delivery_note;
            $this->paymentType = $sale->payment_type;

            // Restore cart items - need to add back to stock first
            $this->cart = [];
            $this->quantities = [];
            $this->prices = [];
            $this->discounts = [];
            $this->quantityTypes = [];

            foreach ($sale->items as $item) {
                $product = $item->product;
                if ($product) {
                    // Restore stock temporarily for editing
                    $product->stock_quantity += $item->quantity;
                    $product->sold -= $item->quantity;
                    $product->save();

                    $productId = $product->id;
                    $this->cart[$productId] = [
                        'id' => $product->id,
                        'name' => $product->product_name,
                        'code' => $product->product_code,
                        'brand' => $product->brand->name ?? 'N/A',
                        'image' => $product->image,
                        'price' => $item->price,
                        'stock_quantity' => $product->stock_quantity,
                    ];
                    $this->quantities[$productId] = $item->quantity;
                    $this->prices[$productId] = $item->price;
                    $this->discounts[$productId] = $item->discount;
                    $this->quantityTypes[$productId] = $item->quantity_type ?? '';
                }
            }

            // Restore payment info
            $this->cashAmount = 0;
            $this->cheques = [];

            foreach ($sale->payments as $payment) {
                if ($payment->payment_method === 'cash') {
                    $this->cashAmount = $payment->amount;
                } elseif ($payment->payment_method === 'cheque') {
                    $cheque = Cheque::where('payment_id', $payment->id)->first();
                    if ($cheque) {
                        $this->cheques[] = [
                            'number' => $cheque->cheque_number,
                            'bank' => $cheque->bank_name,
                            'date' => $cheque->cheque_date,
                            'amount' => $cheque->cheque_amount,
                        ];
                    }
                }
            }

            $this->updateTotals();
            $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Sale loaded for editing. Make your changes and click "Update Sale".']);
        } catch (Exception $e) {
            Log::error('Edit sale error: ' . $e->getMessage());
            $this->js('swal.fire("Error", "Failed to load sale for editing: ' . $e->getMessage() . '", "error")');
        }
    }

    // NEW METHOD: Cancel Edit
    public function cancelEdit()
    {
        $this->isEditMode = false;
        $this->editingSaleId = null;
        $this->clearCart();
        $this->resetPaymentInfo();
        $this->invoiceDate = date('Y-m-d');
        $this->generateInvoiceNumber();
        $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Edit cancelled']);

        // Redirect to clear URL parameter if present
        if (request()->has('edit')) {
            return redirect()->route('admin.store-billing');
        }
    }

    public function completeSale()
    {
        if (empty($this->cart)) {
            $this->js('swal.fire("Error", "Please add items to the cart.", "error")');
            return;
        }

        if (!$this->validateInvoiceNumber()) {
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
        } else {
            $initialPayment = floatval($this->initialPaymentAmount);

            if ($initialPayment < 0) {
                $this->js('swal.fire("Error", "Initial payment cannot be negative.", "error")');
                return;
            }

            if ($initialPayment > $this->grandTotal) {
                $this->js('swal.fire("Error", "Initial payment cannot exceed Grand Total.", "error")');
                return;
            }

            $this->calculateBalanceAmount();
        }

        try {
            DB::beginTransaction();

            // If in edit mode, delete old sale records first
            if ($this->isEditMode && $this->editingSaleId) {
                $oldSale = Sale::find($this->editingSaleId);
                if ($oldSale) {
                    // Get payment IDs before deleting payments
                    $paymentIds = Payment::where('sale_id', $oldSale->id)->pluck('id')->toArray();

                    // Delete related cheques first
                    if (!empty($paymentIds)) {
                        Cheque::whereIn('payment_id', $paymentIds)->delete();
                    }

                    // Delete payments
                    Payment::where('sale_id', $oldSale->id)->delete();

                    // Delete sales items
                    SalesItem::where('sale_id', $oldSale->id)->delete();

                    // Delete admin sales - check if the record exists first
                    try {
                        // Try to find admin_sales by sale_id
                        $adminSale = AdminSale::where('sale_id', $oldSale->id)->first();
                        if ($adminSale) {
                            $adminSale->delete();
                        }
                    } catch (\Exception $e) {
                        // If sale_id column doesn't exist in admin_sales table, 
                        // we'll skip this or handle differently
                        Log::warning('Could not delete admin_sale: ' . $e->getMessage());
                    }

                    // Delete customer accounts
                    CustomerAccount::where('sale_id', $oldSale->id)->delete();

                    // Finally delete the sale
                    $oldSale->delete();
                }
            }

            $actualDueAmount = 0;
            if ($this->paymentType === 'partial') {
                $actualDueAmount = $this->grandTotal - floatval($this->initialPaymentAmount);
            }

            $sale = Sale::create([
                'invoice_number'   => $this->invoiceNumber,
                'customer_id'      => $this->customerId,
                'user_id'          => auth()->id(),
                'customer_type'    => Customer::find($this->customerId)->type ?? 'N/A',
                'subtotal'         => $this->subtotal,
                'discount_amount'  => $this->totalDiscount,
                'total_amount'     => $this->grandTotal,
                'payment_type'     => $this->paymentType,
                'payment_status'   => $this->paymentType === 'full' ? 'paid' : 'partial',
                'notes'            => $this->saleNotes,
                'delivery_note'    => $this->deliveryNote,
                'due_amount'       => $actualDueAmount,
                'created_at'       => $this->invoiceDate . ' ' . date('H:i:s'),
                'updated_at'       => now(),
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

                SalesItem::create([
                    'sale_id'       => $sale->id,
                    'product_id'    => $item['id'],
                    'quantity'      => $quantityToSell,
                    'quantity_type' => $qtyType,
                    'price'         => $price,
                    'discount'      => $itemDiscount,
                    'total'         => $total,
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
                        'sale_id'           => $sale->id,
                        'admin_sale_id'     => $adminSale->id,
                        'amount'            => floatval($cheque['amount']),
                        'payment_method'    => 'cheque',
                        'payment_reference' => $cheque['number'],
                        'bank_name'         => $cheque['bank'],
                        'is_completed'      => true,
                        'payment_date'      => $cheque['date'],
                        'status'            => 'Paid',
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
                    'amount'          => $actualDueAmount,
                    'payment_method'  => 'credit',
                    'is_completed'    => false,
                    'status'          => null,
                    'due_date'        => $this->balanceDueDate ?? now()->addDays(7),
                ]);

                $account = CustomerAccount::where('customer_id', $this->customerId)->first();

                if ($account) {
                    $oldCurrentDue = floatval($account->current_due_amount ?? 0);
                    $account->current_due_amount = $oldCurrentDue + $actualDueAmount;
                    $account->total_due = floatval($account->back_forward_amount ?? 0) + $account->current_due_amount;
                    $account->sale_id = $sale->id;
                    $account->save();
                } else {
                    CustomerAccount::create([
                        'customer_id'         => $this->customerId,
                        'sale_id'             => $sale->id,
                        'back_forward_amount' => 0,
                        'current_due_amount'  => $actualDueAmount,
                        'paid_due'            => 0,
                        'total_due'           => $actualDueAmount,
                    ]);
                }
            }

            DB::commit();

            $this->receipt = Sale::with(['customer', 'items.product', 'payments'])->find($sale->id);
            $this->lastSaleId = $sale->id;
            $this->showReceipt = true;

            $message = $this->isEditMode ? 'Sale updated successfully!' : 'Sale completed successfully!';
            $this->js('swal.fire("Success", "' . $message . ' Invoice #' . $sale->invoice_number . '", "success")');

            // Reset edit mode
            $this->isEditMode = false;
            $this->editingSaleId = null;

            $this->clearCart();
            $this->resetPaymentInfo();
            $this->invoiceDate = date('Y-m-d');
            $this->generateInvoiceNumber();
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
        $this->saleNotes = '';
        $this->deliveryNote = '';
        $this->customerId = null;
    }

    public function render()
    {
        return view('livewire.admin.store-billing');
    }
}
