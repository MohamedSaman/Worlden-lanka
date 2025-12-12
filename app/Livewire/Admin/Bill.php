<?php
// Bill.php - Livewire Component (Updated)

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use App\Models\SalesItem;
use App\Models\ReturnProduct;
use App\Models\Payment;
use App\Models\Cheque;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\ProductDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Title("Bills & Invoices")]
#[Layout('components.layouts.admin')]
class Bill extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedSale = null;
    public $saleDetails = null;
    public $saleToDelete = null;
    public $filters = [
        'dateFrom' => '',
        'dateTo' => '',
        'paymentType' => '',
        'customerType' => '',
    ];

    public function mount()
    {
        // Initialize filters if needed
    }

    public function viewInvoice($saleId)
    {
        $this->selectedSale = Sale::with([
            'customer',
            'items.product',
            'payments',
            'user'
        ])->find($saleId);

        if ($this->selectedSale) {
            // Get return items for this sale
            $returnItems = ReturnProduct::with('product')
                ->where('sale_id', $saleId)
                ->get();

            // Calculate totals
            $totalReturnAmount = $returnItems->sum('total_amount');
            $adjustedGrandTotal = max(0, $this->selectedSale->total_amount - $totalReturnAmount);

            $this->saleDetails = [
                'sale' => $this->selectedSale,
                'items' => $this->selectedSale->items()->get(),
                'returnItems' => $returnItems,
                'totalReturnAmount' => $totalReturnAmount,
                'adjustedGrandTotal' => $adjustedGrandTotal,
                'payments' => $this->selectedSale->payments,
            ];

            // Open receipt modal directly
            $this->dispatch('openInvoiceModal');
        }
    }

    public function confirmDelete($saleId)
    {
        $this->saleToDelete = $saleId;
        $this->dispatch('showDeleteConfirmation');
    }

    public function deleteSale()
    {
        if (!$this->saleToDelete) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'No sale selected for deletion'
            ]);
            return;
        }

        try {
            DB::beginTransaction();

            $sale = Sale::with(['customer', 'items', 'payments'])->find($this->saleToDelete);

            if (!$sale) {
                throw new \Exception('Sale not found');
            }

            // Store customer ID and sale amount for balance adjustment
            $customerId = $sale->customer_id;
            $saleTotal = $sale->total_amount;
            $paidAmount = $sale->payments->where('is_completed', true)->sum('amount');
            $dueAmount = $saleTotal - $paidAmount;

            // 1. Delete cheques linked to payments for this sale (if any)
            $paymentIds = $sale->payments->pluck('id')->toArray();
            if (!empty($paymentIds)) {
                Cheque::whereIn('payment_id', $paymentIds)->delete();
            }

            // 2. Delete all payments associated with this sale
            Payment::where('sale_id', $sale->id)->delete();

            // 3. Restore product stock in ProductDetail (stock_quantity) and adjust sold count, then delete sales items
            $saleItems = $sale->items()->get();
            foreach ($saleItems as $item) {
                try {
                    $product = ProductDetail::find($item->product_id);
                    $qty = floatval($item->quantity);
                    if ($product) {
                        $product->stock_quantity = max(0, ($product->stock_quantity ?? 0) + $qty);
                        if (array_key_exists('sold', $product->getAttributes()) || property_exists($product, 'sold')) {
                            $product->sold = max(0, ($product->sold ?? 0) - $qty);
                        }
                        $product->save();
                    } else {
                        Log::warning('ProductDetail not found for id ' . $item->product_id . ' when restoring stock.');
                    }
                } catch (\Throwable $e) {
                    Log::warning('Failed to restore product stock in ProductDetail for product_id ' . $item->product_id . ': ' . $e->getMessage());
                }
            }
            SalesItem::where('sale_id', $sale->id)->delete();

            // 4. Delete return products if any
            ReturnProduct::where('sale_id', $sale->id)->delete();

            // 5. Update customer account rows (by customer_id): subtract the sale's due amount
            if ($customerId && $dueAmount > 0) {
                $remaining = floatval($dueAmount);

                // Get customer's accounts with outstanding due, oldest first
                $accounts = CustomerAccount::where('customer_id', $customerId)
                    ->where('total_due', '>', 0)
                    ->orderBy('created_at')
                    ->get();

                foreach ($accounts as $acc) {
                    if ($remaining <= 0) break;

                    // Deduct from current_due_amount first
                    $current = floatval($acc->current_due_amount ?? 0);
                    if ($current > 0) {
                        $deduct = min($current, $remaining);
                        $acc->current_due_amount = max(0, $current - $deduct);
                        $acc->total_due = max(0, floatval($acc->total_due ?? 0) - $deduct);
                        $acc->save();
                        $remaining -= $deduct;
                        continue;
                    }
                }

                // If still remaining, deduct from back_forward_amount across accounts
                if ($remaining > 0) {
                    foreach ($accounts as $acc) {
                        if ($remaining <= 0) break;
                        $bf = floatval($acc->back_forward_amount ?? 0);
                        if ($bf > 0) {
                            $deduct = min($bf, $remaining);
                            $acc->back_forward_amount = max(0, $bf - $deduct);
                            $acc->total_due = max(0, floatval($acc->total_due ?? 0) - $deduct);
                            $acc->save();
                            $remaining -= $deduct;
                        }
                    }
                }
            }

            // 6. Prevent cascade removal of customer_account rows: clear sale_id on related customer_accounts
            try {
                CustomerAccount::where('sale_id', $sale->id)->update(['sale_id' => null]);
            } catch (\Throwable $e) {
                Log::warning('Failed to clear sale_id on customer_accounts for sale ' . $sale->id . ': ' . $e->getMessage());
            }

            // 7. Delete the sale record
            $sale->delete();

            DB::commit();

            $this->saleToDelete = null;

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Sale deleted successfully!'
            ]);

            // Reset pagination to first page
            $this->resetPage();
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Sale deletion failed: ' . $e->getMessage());

            $this->dispatch('show-toast', [
                'type' => 'error',
                'message' => 'Failed to delete sale: ' . $e->getMessage()
            ]);
        }
    }

    public function editInvoice($saleId)
    {
        // This method is called from the blade view via wire:click
        // No redirect needed here, the redirect is handled in the blade with href
    }

    public function resetFilters()
    {
        $this->filters = [
            'dateFrom' => '',
            'dateTo' => '',
            'paymentType' => '',
            'customerType' => '',
        ];
    }

    public function render()
    {
        $query = Sale::with(['customer', 'items', 'payments', 'user'])
            ->when($this->search, function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->filters['dateFrom'], function ($q) {
                $q->whereDate('sales_date', '>=', $this->filters['dateFrom']);
            })
            ->when($this->filters['dateTo'], function ($q) {
                $q->whereDate('sales_date', '<=', $this->filters['dateTo']);
            })
            ->when($this->filters['paymentType'], function ($q) {
                $q->where('payment_type', $this->filters['paymentType']);
            })
            ->when($this->filters['customerType'], function ($q) {
                $q->where('customer_type', $this->filters['customerType']);
            })
            ->orderBy('sales_date', 'desc');

        $sales = $query->paginate(10);

        // Calculate stats
        $totalSales = Sale::count();
        $todaySales = Sale::whereDate('sales_date', today())->count();
        $totalRevenue = Sale::sum('total_amount');
        $todayRevenue = Sale::whereDate('sales_date', today())->sum('total_amount');

        return view('livewire.admin.bill', [
            'sales' => $sales,
            'totalSales' => $totalSales,
            'todaySales' => $todaySales,
            'totalRevenue' => $totalRevenue,
            'todayRevenue' => $todayRevenue,
        ]);
    }
}
