<?php

namespace App\Livewire\Admin;

use Exception;
use App\Models\User;
use Livewire\Component;
use App\Models\ProductDetail;
use App\Models\StaffSale;
use App\Models\StaffProduct;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.admin')]
#[Title('Product Billing Page')]
class BillingPage extends Component
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
    public $stock = 0;

    public $selectedStaffId = null;

    protected $listeners = ['quantityUpdated' => 'updateTotals'];

    protected function rules()
    {
        return [
            'selectedStaffId' => 'required',
        ];
    }

    protected $messages = [
        'selectedStaffId.required' => 'Please select a staff member to assign this sale.',
    ];

    public function mount()
    {
        $this->updateTotals();
    }

    public function updatedSearch()
    {
        if (strlen($this->search) >= 2) {
            $this->searchResults = ProductDetail::join('product_categories', 'product_categories.id', '=', 'product_details.category_id')
                ->select(
                    'product_details.*',
                    'product_categories.name as category_name',
                    DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(product_details.customer_field, '$.Stock')) AS UNSIGNED) as stock")
                )
                ->whereRaw("LOWER(JSON_UNQUOTE(JSON_EXTRACT(product_details.customer_field, '$.Status'))) = 'active'")
                ->whereRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(product_details.customer_field, '$.Stock')) AS UNSIGNED) > 0")
                ->where(function ($query) {
                    $query->where('product_details.product_name', 'like', '%' . $this->search . '%');
                })
                ->take(50)
                ->get();
        } else {
            $this->searchResults = [];
        }
    }

    public function addToCart($productId)
    {
        $product = ProductDetail::where('product_details.id', $productId)
            ->select(
                'product_details.id',
                'product_details.product_name',
                'product_details.selling_price',
                DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT(product_details.customer_field, '$.Stock')) AS UNSIGNED) as Stock")
            )
            ->first();

        if (!$product || $product->Stock <= 0) {
            $this->js('swal.fire("Error", "This product is out of stock.", "error")');
            return;
        }

        $existingItem = collect($this->cart)->firstWhere('id', $productId);

        if ($existingItem) {
            if (($this->quantities[$productId] + 1) > $product->Stock) {
                $this->js('swal.fire("Warning", "Maximum available quantity reached.", "warning")');
                return;
            }
            $this->quantities[$productId]++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->product_name,
                'price' => $product->selling_price ?? 0,
                'discountPrice' => null, // <-- Add this line
                'inStock' => $product->Stock ?? 0,
            ];

            $this->quantities[$productId] = 1;
            $this->discounts[$productId] = 0;
        }

        $this->search = '';
        $this->searchResults = [];
        $this->updateTotals();
    }



    public function updateQuantity($productId, $quantity)
    {
        if (!isset($this->cart[$productId])) {
            return;
        }

        $maxAvailable = $this->cart[$productId]['inStock'];

        $quantity = (int)$quantity;
        if ($quantity < 1) {
            $quantity = 1;
        } elseif ($quantity > $maxAvailable) {
            $quantity = $maxAvailable;
            $this->js('swal.fire("Warning", "Quantity limited to maximum available (' . $maxAvailable . ')", "warning")');
        }

        $this->quantities[$productId] = $quantity;
        $this->updateTotals();
    }

    public function updateDiscount($productId, $discount)
    {
        $this->discounts[$productId] = max(0, min($discount, $this->cart[$productId]['price']));
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
        $this->productDetails = ProductDetail::join('product_categories', 'product_categories.id', '=', 'product_details.category_id')
            ->select('product_details.*', 'product_categories.name as category_name')
            ->where('product_details.id', $productId)
            ->first();

        // Access stock as integer
        $this->stock = (int) ($this->productDetails->customer_field['Stock'] ?? 0);

        $this->js('$("#viewDetailModal").modal("show")');
    }
    public function updateTotals()
    {
        $this->subtotal = 0;
        $this->totalDiscount = 0;

        foreach ($this->cart as $id => $item) {
            // If you have no discount price, just use price
            $price = $item['price'] ?? 0;

            $this->subtotal += $price * ($this->quantities[$id] ?? 1);

            // If you are not using discounts, set this to 0 or remove it entirely
            $this->totalDiscount += ($this->discounts[$id] ?? 0) * ($this->quantities[$id] ?? 1);
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

    public function completeSale()
    {
        if (empty($this->cart)) {
            $this->js("swal.fire('Error', 'Please add items to the cart.', 'error')");
            return;
        }

        $invalidItems = [];

        // Validate stock for each cart item
        foreach ($this->cart as $id => $item) {
            $product = ProductDetail::find($id);

            if (!$product) {
                $invalidItems[] = $item['name'] . " (Product not found)";
                continue;
            }

            // Get stock from JSON
            $currentStock = (int) ($product->customer_field['Stock'] ?? 0);

            if ($currentStock < $this->quantities[$id]) {
                $invalidItems[] = $item['name'] . " (Requested: {$this->quantities[$id]}, Available: {$currentStock})";
            }
        }

        if (!empty($invalidItems)) {
            $errorMessage = "Cannot complete sale due to insufficient stock:<br><ul>";
            foreach ($invalidItems as $item) {
                $errorMessage .= "<li>{$item}</li>";
            }
            $errorMessage .= "</ul>";

            $this->js("swal.fire({title: 'Stock Error', html: '{$errorMessage}', icon: 'error'})");
            return;
        }

        $this->validate([
            'selectedStaffId' => 'required|exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            // Create StaffSale
            $staffSale = StaffSale::create([
                'staff_id' => $this->selectedStaffId,
                'admin_id' => auth()->id(),
                'total_quantity' => array_sum($this->quantities),
                'total_value' => $this->grandTotal,
                'sold_quantity' => 0,
                'sold_value' => 0,
                'status' => 'assigned',
            ]);

            foreach ($this->cart as $productId => $item) {
                $product = ProductDetail::find($productId);

                $unitPrice = $item['discountPrice'] ?? $item['price'];
                $discountPerUnit = $this->discounts[$productId] ?? 0;
                $totalDiscount = $discountPerUnit * $this->quantities[$productId];
                $totalValue = ($unitPrice * $this->quantities[$productId]) - $totalDiscount;

                // Save StaffProduct
                StaffProduct::create([
                    'staff_sale_id' => $staffSale->id,
                    'product_id' => $productId,
                    'staff_id' => $this->selectedStaffId,
                    'quantity' => $this->quantities[$productId],
                    'unit_price' => $unitPrice,
                    'discount_per_unit' => $discountPerUnit,
                    'total_discount' => $totalDiscount,
                    'total_value' => $totalValue,
                    'sold_quantity' => 0,
                    'sold_value' => 0,
                    'status' => 'assigned',
                ]);

                // Deduct stock in JSON
                $fields = $product->customer_field;
                $fields['Stock'] = max(0, ($fields['Stock'] ?? 0) - $this->quantities[$productId]);
                $product->customer_field = $fields;
                $product->save();
            }

            DB::commit();

            $this->js("swal.fire('Success', 'Products successfully assigned to staff.', 'success')");

            $this->clearCart();
            $this->selectedStaffId = null;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning products to staff: ' . $e->getMessage());
            // Show actual error in SweetAlert for debugging
            $this->js("swal.fire('Error', '" . addslashes($e->getMessage()) . "', 'error')");
        }
    }




    public function render()
    {
        $staffs = User::where('role', 'staff')->get();
        return view('livewire.admin.billing-page', [
            'staffs' => $staffs,
        ]);
    }
}
