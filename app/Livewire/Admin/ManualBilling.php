<?php

namespace App\Livewire\Admin;

use Exception;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\ManualSale;
use App\Models\ManualSalesItem;
use App\Models\ManualSalePayment;
use App\Models\Payment;
use App\Models\ProductCategory;
use App\Models\QuantityType;
use App\Models\Cheque;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

#[Title("Manual Billing")]
#[Layout('components.layouts.admin')]
class ManualBilling extends Component
{
    use WithFileUploads;

    public $cart = [];
    public $quantities = [];
    public $discounts = [];
    public $prices = [];
    public $quantityTypes = [];
    public $categories = [];
    public $subtotal = 0;
    public $totalDiscount = 0;
    public $grandTotal = 0;

    public $customers = [];
    public $customerName = '';
    public $customerPhone = '';

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
    public $bankName = '';

    public $initialPaymentAmount = 0;
    public $initialPaymentMethod = '';
    public $initialBankName = '';

    public $balanceAmount = 0;
    public $balancePaymentMethod = '';
    public $balanceDueDate = '';
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

    public $banks = [];
    public $availableQuantityTypes = [];
    
    // Edit mode properties
    public $isEditMode = false;
    public $editingSaleId = null;
    
    // Manual entry properties
    public $manualProductName = '';
    public $manualProductCode = '';
    public $manualProductCategory = '';
    public $manualProductPrice = '';
    public $manualProductQuantity = 1;
    public $tempProductIdCounter = 999999;

    protected $listeners = ['quantityUpdated' => 'updateTotals'];

    public function mount()
    {
        $this->loadCustomers();
        $this->loadBanks();
        $this->loadQuantityTypes();
        $this->loadCategories();
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
        $this->availableQuantityTypes = QuantityType::where('is_active', true)
            ->orderBy('name')
            ->pluck('name', 'code')
            ->toArray();

        if (empty($this->availableQuantityTypes)) {
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
    }

    public function loadCategories()
    {
        $this->categories = ProductCategory::orderBy('name')->get();
    }

    public function generateInvoiceNumber()
    {
        $prefix = 'INV-';

        // // Check both Sale and ManualSale tables to get the highest invoice number
        // $lastStoreSale = Sale::where('invoice_number', 'like', "{$prefix}%")
        //     ->orderBy('id', 'DESC')
        //     ->first();

        $lastManualSale = ManualSale::where('invoice_number', 'like', "{$prefix}%")
            ->orderBy('id', 'DESC')
            ->first();

        $nextNumber = 1;
        
        // Get the highest number from both tables
        // if ($lastStoreSale) {
        //     $storeNumber = intval(str_replace($prefix, '', $lastStoreSale->invoice_number));
        //     $nextNumber = max($nextNumber, $storeNumber + 1);
        // }
        
        if ($lastManualSale) {
            $manualNumber = intval(str_replace($prefix, '', $lastManualSale->invoice_number));
            $nextNumber = max($nextNumber, $manualNumber + 1);
        }

        $invoiceNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        // Ensure the number doesn't exist in either table
        while (
               ManualSale::where('invoice_number', $invoiceNumber)->exists()) {
            $nextNumber++;
            $invoiceNumber = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        }

        $this->invoiceNumber = $invoiceNumber;
        $this->isInvoiceGenerated = true;
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
            $currentSale = ManualSale::find($this->editingSaleId);
            if ($currentSale && $currentSale->invoice_number === $this->invoiceNumber) {
                return true;
            }
        }

        // Check both Sale and ManualSale tables for existing invoice
        $existingManualSale = ManualSale::where('invoice_number', $this->invoiceNumber)->first();

        if ($existingManualSale) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Invoice Already Exists',
                'text' => 'This invoice number already exists in the system. Please use a different number.'
            ]);
            return false;
        }

        return true;
    }

    public function loadCustomers()
    {
        $this->customers = Customer::orderBy('name')->get();
    }

    public function addManualProduct()
    {
        if (empty($this->manualProductName)) {
            $this->dispatch('show-toast', ['type' => 'warning', 'message' => 'Please enter product name']);
            return;
        }

        if (empty($this->manualProductPrice) || floatval($this->manualProductPrice) <= 0) {
            $this->dispatch('show-toast', ['type' => 'warning', 'message' => 'Please enter valid price']);
            return;
        }

        if (empty($this->manualProductQuantity) || intval($this->manualProductQuantity) <= 0) {
            $this->dispatch('show-toast', ['type' => 'warning', 'message' => 'Please enter valid quantity']);
            return;
        }

        $tempId = 'manual_' . $this->tempProductIdCounter++;

        $this->cart[$tempId] = [
            'id' => $tempId,
            'name' => $this->manualProductName,
            'code' => $this->manualProductCode ?: 'N/A',
            'category' => $this->manualProductCategory,
            'price' => floatval($this->manualProductPrice),
            'stock_quantity' => 999999,
        ];

        $this->prices[$tempId] = floatval($this->manualProductPrice);
        $this->quantities[$tempId] = intval($this->manualProductQuantity);
        $this->discounts[$tempId] = 0;
        $this->quantityTypes[$tempId] = '';

        $this->updateTotals();

        $this->manualProductName = '';
        $this->manualProductCode = '';
        $this->manualProductCategory = '';
        $this->manualProductPrice = '';
        $this->manualProductQuantity = 1;

        $this->dispatch('show-toast', ['type' => 'success', 'message' => 'Product added to cart']);
    }

    public function updatedQuantities($value, $key)
    {
        $this->validateQuantity($key);
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

        $currentQuantity = filter_var($this->quantities[$productId], FILTER_VALIDATE_INT);

        if ($currentQuantity === false || $currentQuantity < 1) {
            $this->quantities[$productId] = 1;
            $this->dispatch('show-toast', ['type' => 'warning', 'message' => 'Minimum quantity is 1']);
        }
        
        $this->updateTotals();
    }

    public function updateQuantity($productId, $quantity)
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        if ($quantity <= 0) {
            $quantity = 1;
        }

        $this->quantities[$productId] = $quantity;
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
        $this->customerName = $customer->name;
        $this->customerPhone = $customer->phone;

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

    // Load Sale for Editing
    public function loadSaleForEditing($saleId)
    {
        try {
            $sale = ManualSale::with(['customer', 'items', 'payments'])->find($saleId);

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
            $this->customerName = $sale->customer->name ?? '';
            $this->customerPhone = $sale->customer->phone ?? '';
            $this->saleNotes = $sale->notes;
            $this->deliveryNote = $sale->delivery_note;
            $this->paymentType = $sale->payment_type;

            // Restore cart items
            $this->cart = [];
            $this->quantities = [];
            $this->prices = [];
            $this->discounts = [];
            $this->quantityTypes = [];

            foreach ($sale->items as $item) {
                $tempId = 'manual_' . $this->tempProductIdCounter++;
                
                $this->cart[$tempId] = [
                    'id' => $tempId,
                    'name' => $item->product_name,
                    'code' => $item->product_code ?? 'N/A',
                    'category' => $item->category,
                    'price' => $item->price,
                    'stock_quantity' => 999999,
                ];
                
                $this->quantities[$tempId] = $item->quantity;
                $this->prices[$tempId] = $item->price;
                $this->discounts[$tempId] = $item->discount;
                $this->quantityTypes[$tempId] = $item->quantity_type ?? '';
            }

            // Restore payment info
            $this->cashAmount = 0;
            $this->cheques = [];

            foreach ($sale->payments as $payment) {
                if ($payment->payment_method === 'cash') {
                    $this->cashAmount = $payment->amount;
                } elseif ($payment->payment_method === 'cheque') {
                    // Find cheque by payment reference
                    $cheque = Cheque::where('cheque_number', $payment->payment_reference)->first();
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
            $this->dispatch('show-toast', ['type' => 'info', 'message' => 'Sale loaded for editing. Make your changes and click "Complete Sale" to update.']);
        } catch (Exception $e) {
            Log::error('Edit manual sale error: ' . $e->getMessage());
            $this->js('swal.fire("Error", "Failed to load sale for editing: ' . $e->getMessage() . '", "error")');
        }
    }

    // Cancel Edit
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
            return redirect()->route('admin.manual-billing');
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
            'customerName' => 'required|string',
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
                $oldSale = ManualSale::find($this->editingSaleId);
                if ($oldSale) {
                    // Get payment IDs before deleting payments
                    $paymentIds = ManualSalePayment::where('manual_sale_id', $oldSale->id)->pluck('id')->toArray();

                    // Delete related cheques first (linked through Payment table)
                    if (!empty($paymentIds)) {
                        $linkedPayments = Payment::where('applied_to', 'manual_sale')
                            ->whereIn('payment_reference', function($query) use ($paymentIds) {
                                $query->select('payment_reference')
                                    ->from('manual_sale_payments')
                                    ->whereIn('id', $paymentIds);
                            })->pluck('id');
                        
                        if ($linkedPayments->isNotEmpty()) {
                            Cheque::whereIn('payment_id', $linkedPayments)->delete();
                            Payment::whereIn('id', $linkedPayments)->delete();
                        }
                    }

                    // Delete manual sale payments
                    ManualSalePayment::where('manual_sale_id', $oldSale->id)->delete();

                    // Delete manual sales items
                    ManualSalesItem::where('manual_sale_id', $oldSale->id)->delete();

                    // Finally delete the sale
                    $oldSale->delete();
                }
            }

            // Find or create customer
            $customer = Customer::where('name', $this->customerName)->first();
            if (!$customer) {
                $customer = Customer::create([
                    'name' => $this->customerName,
                    'phone' => $this->customerPhone,
                    'type' => 'walk_in',
                ]);
            }

            $actualDueAmount = 0;
            if ($this->paymentType === 'partial') {
                $actualDueAmount = $this->grandTotal - floatval($this->initialPaymentAmount);
            }

            $sale = ManualSale::create([
                'invoice_number'   => $this->invoiceNumber,
                'customer_id'      => $customer->id,
                'user_id'          => auth()->id(),
                'customer_type'    => $customer->type ?? 'walk_in',
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

            foreach ($this->cart as $id => $item) {
                $quantityToSell = $this->quantities[$id];
                $price = $this->prices[$id] ?? $item['price'] ?? 0;
                $itemDiscount = $this->discounts[$id] ?? 0;
                $qtyType = $this->quantityTypes[$id] ?? '';
                $total = ($price * $quantityToSell) - ($itemDiscount * $quantityToSell);

                ManualSalesItem::create([
                    'manual_sale_id' => $sale->id,
                    'product_name'   => $item['name'],
                    'product_code'   => $item['code'] ?? null,
                    'category'       => $item['category'] ?? null,
                    'quantity'       => $quantityToSell,
                    'quantity_type'  => $qtyType,
                    'price'          => $price,
                    'discount'       => $itemDiscount,
                    'total'          => $total,
                ]);
            }

            if ($this->paymentType == 'full') {
                // Record cash payment in manual_sale_payments
                if (floatval($this->cashAmount) > 0) {
                    ManualSalePayment::create([
                        'manual_sale_id'  => $sale->id,
                        'amount'          => floatval($this->cashAmount),
                        'payment_method'  => 'cash',
                        'is_completed'    => true,
                        'payment_date'    => now(),
                        'status'          => 'Paid',
                    ]);
                }
                
                // Record cheque payments in both manual_sale_payments and payments (for cheques table)
                foreach ($this->cheques as $cheque) {
                    // Create manual sale payment record
                    ManualSalePayment::create([
                        'manual_sale_id'    => $sale->id,
                        'amount'            => floatval($cheque['amount']),
                        'payment_method'    => 'cheque',
                        'payment_reference' => $cheque['number'],
                        'bank_name'         => $cheque['bank'],
                        'is_completed'      => true,
                        'payment_date'      => $cheque['date'],
                        'status'            => 'Paid',
                    ]);

                    // Also create payment record for cheque table linkage
                    $payment = Payment::create([
                        'sale_id'           => null,
                        'customer_id'       => $customer->id,
                        'amount'            => floatval($cheque['amount']),
                        'payment_method'    => 'cheque',
                        'payment_reference' => $cheque['number'],
                        'bank_name'         => $cheque['bank'],
                        'is_completed'      => true,
                        'payment_date'      => $cheque['date'],
                        'status'            => 'Paid',
                        'applied_to'        => 'manual_sale',
                    ]);

                    // Create cheque record linked to payment
                    Cheque::create([
                        'cheque_number' => $cheque['number'],
                        'cheque_date'   => $cheque['date'],
                        'bank_name'     => $cheque['bank'],
                        'cheque_amount' => $cheque['amount'],
                        'status'        => 'pending',
                        'customer_id'   => $customer->id,
                        'payment_id'    => $payment->id,
                    ]);
                }
            } else {
                // Partial payment - record due amount
                if ($actualDueAmount > 0) {
                    ManualSalePayment::create([
                        'manual_sale_id'  => $sale->id,
                        'amount'          => $actualDueAmount,
                        'payment_method'  => 'credit',
                        'is_completed'    => false,
                        'status'          => null,
                        'due_date'        => $this->balanceDueDate ?? now()->addDays(7),
                    ]);
                }
            }

            DB::commit();

            // Load receipt data BEFORE resetting
            $this->receipt = ManualSale::with(['customer', 'items', 'payments'])->find($sale->id);
            $this->lastSaleId = $sale->id;
            $this->showReceipt = true;

            $message = $this->isEditMode ? 'Sale updated successfully!' : 'Sale completed successfully!';

            // Reset edit mode
            $this->isEditMode = false;
            $this->editingSaleId = null;

            // Clear form and reset BEFORE showing modal
            $this->clearCart();
            $this->resetPaymentInfo();
            $this->invoiceDate = date('Y-m-d');
            $this->generateInvoiceNumber();

            // Dispatch event to show modal with sale info
            $this->dispatch('sale-completed', saleId: $sale->id, invoiceNumber: $sale->invoice_number, message: $message);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Manual sale error: ' . $e->getMessage());
            $this->js('swal.fire("Error", "An error occurred: ' . $e->getMessage() . '", "error")');
        }
    }

    public function resetPaymentInfo()
    {
        $this->paymentType = 'full';
        $this->paymentMethod = '';
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
        $this->initialBankName = '';
        $this->balanceAmount = 0;
        $this->balancePaymentMethod = '';
        $this->balanceDueDate = date('Y-m-d', strtotime('+7 days'));
        $this->balanceBankName = '';
        $this->saleNotes = '';
        $this->deliveryNote = '';
        $this->customerName = '';
        $this->customerPhone = '';
    }

    public function render()
    {
        return view('livewire.admin.manual-billing');
    }
}