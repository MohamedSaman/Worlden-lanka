<?php

namespace App\Livewire\Admin;

use Exception;
use App\Models\Sale;
use App\Models\Payment;
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
                ->select(
                    'product_details.*',
                    'product_categories.name as category_name',
                )
                ->where('product_details.stock_quantity', '>', 0)
                ->where(function ($query) {
                    $query->where('product_details.product_name', 'like', '%' . $this->search . '%')
                        ->orWhere('product_details.product_code', 'like', '%' . $this->search . '%')
                        ->orWhere('product_categories.name', 'like', '%' . $this->search . '%');
                })
                ->take(50)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }


    public function addToCart($productId)
    {
        // Fetch product details
        $product = ProductDetail::where('id', $productId)
            ->select(
                'id',
                'product_code',
                'product_name',
                'selling_price',
                'stock_quantity',
                'damage_quantity'
            )
            ->first();

        // Check if product exists and stock is available
        if (!$product || $product->stock_quantity <= 0) {
            $this->dispatch('showToast', [
                'type' => 'danger',
                'message' => 'This product is out of stock.'
            ]);
            return;
        }

        // Check if item is already in cart
        $existingItem = collect($this->cart)->firstWhere('id', $productId);

        if ($existingItem) {
            // Check stock limit
            if (($this->quantities[$productId] + 1) > $product->stock_quantity) {
                $this->dispatch('showToast', [
                    'type' => 'warning',
                    'message' => "Maximum available quantity ({$product->stock_quantity}) reached."
                ]);
                return;
            }
            $this->quantities[$productId]++;
        } else {
            // Add new item to cart
            $discountPrice = $product->discount_price ?? 0;

            $this->cart[$productId] = [
                'id' => $product->id,
                'code' => $product->product_code ?? 'N/A',
                'name' => $product->product_name ?? 'Unnamed',
                'model' => $product->model ?? '-',
                'brand' => $product->brand ?? '-',
                'image' => $product->image ?? null,
                'price' => $product->selling_price ?? 0,
                'discountPrice' => $discountPrice ?? 0,
                'stock_quantity' => $product->stock_quantity,
            ];

            $this->quantities[$productId] = 1;
            $this->discounts[$productId] = $discountPrice;
        }

        // Clear search input and results
        $this->search = '';
        $this->searchResults = [];

        // Update cart totals
        $this->updateTotals();
    }


    public function validateQuantity($productId)
    {
        // Check if the product exists in the cart and quantities array
        if (!isset($this->cart[$productId]) || !isset($this->quantities[$productId])) {
            return;
        }

        // Fetch the latest product stock from database
        $productStock = ProductDetail::find($productId);
        if (!$productStock) {
            return;
        }

        // Calculate maximum available stock (stock_quantity - Sold)
        $customerField = $productStock->customer_field ?? [];
        $soldCount = $productStock->sold ?? 0;
        $maxAvailable = max(0, $productStock->stock_quantity - $soldCount);

        // Get the current quantity user entered/updated
        $currentQuantity = (int)$this->quantities[$productId];

        // Minimum quantity check
        if ($currentQuantity <= 0) {
            $this->quantities[$productId] = 1;
            $this->dispatch('showToast', [
                'type' => 'warning',
                'message' => 'Minimum quantity is 1'
            ]);

            // Maximum quantity check
        } elseif ($currentQuantity > $maxAvailable) {
            $this->quantities[$productId] = $maxAvailable;
            $this->dispatch('showToast', [
                'type' => 'warning',
                'message' => "Maximum available quantity is {$maxAvailable}"
            ]);
        }

        // Update Status dynamically in cart if you want
        $this->cart[$productId]['Status'] = $maxAvailable > 0 ? 'Available' : 'Unavailable';

        // Recalculate totals after adjustment
        $this->updateTotals();
    }

    public function updateQuantity($productId, $quantity)
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $maxAvailable = $this->cart[$productId]['stock_quantity'];

        // Adjust quantity within limits
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

        // Update Status dynamically in cart
        $this->cart[$productId]['Status'] = $maxAvailable > 0 ? 'Available' : 'Unavailable';

        $this->updateTotals();
    }

    public function updateDiscount($productId, $discount)
    {
        if (!isset($this->cart[$productId])) return;

        // Ensure discount is not negative or more than the product price
        $this->discounts[$productId] = max(0, min($discount, $this->cart[$productId]['price'] ?? 0));
        $this->updateTotals();
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
        unset($this->quantities[$productId]);
        unset($this->discounts[$productId]);
        $this->updateTotals();
    }


    public function showDetail($productId)
    {
        $this->productDetails = ProductDetail::select(
            'id',
            'product_name',
            'selling_price',
            'stock_quantity',
            'damage_quantity',
            'sold',
            // Calculate available stock: stock_quantity - sold
            DB::raw("(stock_quantity - sold) as available_stock")
        )
            ->where('id', $productId)
            ->first();


        // Show modal
        $this->js('$("#viewDetailModal").modal("show")');
    }


    public function updateTotals()
    {
        $this->subtotal = 0;
        $this->totalDiscount = 0;

        foreach ($this->cart as $id => $item) {
            $price = $item['price'] ?: $item['price'];
            $this->subtotal += $price * $this->quantities[$id];
            $this->totalDiscount += $this->discounts[$id] * $this->quantities[$id];
        }

        $this->grandTotal = $this->subtotal - $this->totalDiscount;
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->quantities = [];
        $this->discounts = [];
        $this->updateTotals();
    }

    public function saveCustomer()
    {
        $this->validate([
            'newCustomerName' => 'required|min:3',
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
            if ($this->initialPaymentAmount > $this->grandTotal) {
                $this->initialPaymentAmount = $this->grandTotal;
            }

            $this->balanceAmount = $this->grandTotal - $this->initialPaymentAmount;
        } else {
            $this->initialPaymentAmount = 0;
            $this->balanceAmount = 0;
        }
    }

    public function updatedPaymentType($value)
    {
        if ($value == 'partial') {
            $this->initialPaymentAmount = round($this->grandTotal / 2, 2);
            $this->calculateBalanceAmount();
        } else {
            $this->initialPaymentAmount = 0;
            $this->initialPaymentMethod = '';
            $this->initialPaymentReceiptImage = null;
            $this->initialPaymentReceiptImagePreview = null;
            $this->initialBankName = '';

            $this->balanceAmount = 0;
            $this->balancePaymentMethod = '';
            $this->balancePaymentReceiptImage = null;
            $this->balancePaymentReceiptImagePreview = null;
            $this->balanceBankName = '';
        }
    }

    // ...Keep all file upload, validation, and payment logic as in staff billing...

    // (Copy all methods for payment receipt handling, due payment, etc. from staff Billing.php)

    // Only change logic that references staff_products to watch_stocks

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

        // Validate full or partial payments
        if ($this->paymentType === 'full') {
            if ($this->grandTotal <= 0 || !$this->paymentMethod) {
                $this->js('swal.fire("Error", "Please enter a valid amount and select a payment method for full payment.", "error")');
                return;
            }

            if ($this->paymentMethod === 'cheque' && !$this->bankName) {
                $this->js('swal.fire("Error", "Please provide a bank name for the cheque payment.", "error")');
                return;
            }
        } elseif ($this->paymentType === 'partial') {
            if ($this->initialPaymentAmount === null || $this->initialPaymentAmount < 0 || !$this->initialPaymentMethod) {
                $this->js('swal.fire("Error", "Please enter a valid initial payment amount and select a payment method.", "error")');
                return;
            }

            if ($this->initialPaymentMethod === 'cheque' && !$this->initialBankName) {
                $this->js('swal.fire("Error", "Please provide a bank name for the initial cheque payment.", "error")');
                return;
            }

            if ($this->balanceAmount > 0) {
                if (!$this->balancePaymentMethod) {
                    $this->js('swal.fire("Error", "Please select a payment method for the balance amount.", "error")');
                    return;
                }

                // Only require bank name if payment method is cheque, otherwise allow null
                if ($this->balancePaymentMethod === 'cheque' && !$this->balanceBankName) {
                    $this->balanceBankName = null; // Set to null if not provided
                    // Do not show error, just proceed
                }

                if (!$this->balanceDueDate) {
                    $this->js('swal.fire("Error", "Please provide a due date for the balance payment.", "error")');
                    return;
                }
            }
        }

        // dd($this->cart);  Passing Data

        try {
            DB::beginTransaction();

            // 1. Create Sale record
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

            // dd($sale->toArray()); Passing Data

            // 2. Create AdminSale record
            $adminSale = AdminSale::create([
                'sale_id'        => $sale->id,
                'admin_id'       => auth()->id(),
                'total_quantity' => array_sum($this->quantities),
                'total_value'    => $this->grandTotal,
                'sold_quantity'  => 0, // will update below
                'sold_value'     => 0, // will update below
                'status'         => 'partial', // will update below
            ]);



            $totalSoldQty = 0;
            $totalSoldVal = 0;

            foreach ($this->cart as $id => $item) {
                $productStock = ProductDetail::where('id', $item['id'])->first();

                if (!$productStock) {
                    throw new Exception("Product not found: {$item['name']}");
                }

                $availableStock = $productStock->stock_quantity ?? 0;

                // dd($productStock);
                // dd($availableStock);
                // Extract the stock from JSON and convert to integer


                if ($availableStock < $this->quantities[$id]) {
                    throw new Exception("Insufficient stock for item: {$item['name']}. Available: {$availableStock}");
                }

                // Get customer_field as array and update Sold count
                // Get stock from the separate column
                $availableStock = $productStock->stock_quantity ?? 0;

                $soldCount = $productStock->sold ?? 0;

                // Update Sold after this sale
                $productStock->sold = $soldCount + $this->quantities[$id];

                // Calculate total price after discount
                $price = $item['price'] ?? 0;
                $itemDiscount = $this->discounts[$id] ?? 0;
                $total = ($price * $this->quantities[$id]) - ($itemDiscount * $this->quantities[$id]);


                // dd($adminSale->toArray());

                // Insert sale item (linked to sales table)
                SalesItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $item['id'],
                    'quantity'   => $this->quantities[$id],
                    'price' => $price,
                    'discount'   => $itemDiscount,
                    'total'      => $total,
                ]);

                // dd($this->cart);
                // Update stock
                $productStock->stock_quantity -= $this->quantities[$id];
                $productStock->sold = $soldCount + $this->quantities[$id];
                // dd($productStock);
                $productStock->save();

                $totalSoldQty += $this->quantities[$id];
                $totalSoldVal += $total;
            }

            // Update admin sale status and sold values
            $adminSale->sold_quantity = $totalSoldQty;
            $adminSale->sold_value = $totalSoldVal;
            $adminSale->status = $totalSoldQty == $adminSale->total_quantity ? 'completed' : 'partial';
            $adminSale->save();

            // dd($adminSale->toArray());

            // Handle payment (link to sale)
            if ($this->paymentType == 'full') {
                $receiptPath = null;
                if ($this->paymentReceiptImage) {
                    $receiptPath = $this->paymentReceiptImage->store('admin-payment-receipts', 'public');
                }

                Payment::create([
                    'sale_id'         => $sale->id,
                    'admin_sale_id'   => $adminSale->id,
                    'amount'          => $this->grandTotal,
                    'payment_method'  => $this->paymentMethod,
                    'payment_reference' => $receiptPath,
                    'bank_name'       => $this->paymentMethod == 'cheque' ? $this->bankName : null,
                    'is_completed'    => true,
                    'payment_date'    => now(),
                    'status'          => 'Paid',
                ]);
            } else {
                // Initial partial payment
                if ($this->initialPaymentAmount > 0) {
                    $initialReceiptPath = null;
                    if ($this->initialPaymentReceiptImage) {
                        $initialReceiptPath = $this->initialPaymentReceiptImage->store('admin-payment-receipts', 'public');
                    }

                    Payment::create([
                        'sale_id'         => $sale->id,
                        'admin_sale_id'   => $adminSale->id,
                        'amount'          => $this->initialPaymentAmount,
                        'payment_method'  => $this->initialPaymentMethod,
                        'payment_reference' => $initialReceiptPath,
                        'bank_name'       => $this->initialPaymentMethod == 'cheque' ? $this->initialBankName : null,
                        'is_completed'    => true,
                        'payment_date'    => now(),
                        'status'          => 'Paid',
                    ]);
                }

                
                // Balance due payment
                if ($this->balanceAmount > 0) {
                    $balanceReceiptPath = null;
                    if ($this->balancePaymentReceiptImage) {
                        $balanceReceiptPath = $this->balancePaymentReceiptImage->store('admin-payment-receipts', 'public');
                    }

                    Payment::create([
                        'sale_id'         => $sale->id,
                        'admin_sale_id'   => $adminSale->id,
                        'amount'          => $this->balanceAmount,
                        'payment_method'  => $this->balancePaymentMethod,
                        'payment_reference' => $balanceReceiptPath,
                        'bank_name'       => $this->balancePaymentMethod == 'cheque' ? $this->balanceBankName : null,
                        'is_completed'    => false,
                        'due_date'        => $this->balanceDueDate,
                        'due_payment_method' => $this->balancePaymentMethod,
                    ]);
                }
            }

            DB::commit();

            $this->receipt = Sale::with(['customer', 'items.product', 'payments'])->find($sale->id);

            // dd($this->receipt);
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

    // ...rest of the methods (viewReceipt, printReceipt, downloadReceipt, getFilePreviewInfo)...

    public function render()
    {
        return view(
            'livewire.admin.store-billing',
            [
                'receipt' => $this->showReceipt && $this->lastSaleId
                    ? Sale::with(['customer', 'items', 'payments'])->find($this->lastSaleId)
                    : null,
            ]
        );
    }
}
