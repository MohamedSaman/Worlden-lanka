<?php
// File: app/Livewire/Admin/DuePayments.php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\CustomerAccount;
use App\Models\Sale;
use App\Models\Payment;
use App\Models\Cheque;
use App\Models\ReturnProduct;
use Exception;
use Illuminate\Support\Collection;

#[Title("Due Payments")]
#[Layout('components.layouts.admin')]
class DuePayments extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedPayment = null;
    public $paymentDetail = null;
    public $duePaymentAttachment;
    public $paymentId;
    public $duePaymentMethod = '';
    public $paymentNote = '';
    public $duePaymentAttachmentPreview;
    public $receivedAmount = '';
    public $totalDueAmount = 0;
    public $currentDueAmount = 0;
    public $backForwardAmount = 0;
    public $applyToCurrent = true;
    public $applyToBackForward = false;
    public $applyTarget = 'current'; // 'current' or 'back_forward'
    public $filters = [
        'status' => '',
        'dateFrom' => '',
        'dateTo' => '',
    ];

    // Cheque input fields and list
    public $chequeNumber = '';
    public $bankName = '';
    public $chequeAmount = '';
    public $chequeDate = '';
    public $cheques = [];
    public $banks = [];

    public $duePayment;
    protected $listeners = ['refreshPayments' => '$refresh'];

    public function mount()
    {
        $this->loadBanks();
    }

    public function loadBanks()
    {
        $this->banks = [
            'Bank of Ceylon (BOC)' => 'Bank of Ceylon (BOC)',
            'Commercial Bank of Ceylon (ComBank)' => 'Commercial Bank of Ceylon (ComBank)',
            'Hatton National Bank (HNB)' => 'Hatton National Bank (HNB)',
            'People\'s Bank' => 'People\'s Bank',
            'Sampath Bank' => 'Sampath Bank',
            'National Development Bank (NDB)' => 'National Development Bank (NDB)',
            'DFCC Bank' => 'DFCC Bank',
            'Nations Trust Bank (NTB)' => 'Nations Trust Bank (NTB)',
            'Seylan Bank' => 'Seylan Bank',
            'Amana Bank' => 'Amana Bank',
            'Cargills Bank' => 'Cargills Bank',
            'Pan Asia Banking Corporation' => 'Pan Asia Banking Corporation',
            'Union Bank of Colombo' => 'Union Bank of Colombo',
            'Bank of China Ltd' => 'Bank of China Ltd',
            'Citibank, N.A.' => 'Citibank, N.A.',
            'Habib Bank Ltd' => 'Habib Bank Ltd',
            'Indian Bank' => 'Indian Bank',
            'Indian Overseas Bank' => 'Indian Overseas Bank',
            'MCB Bank Ltd' => 'MCB Bank Ltd',
            'Public Bank Berhad' => 'Public Bank Berhad',
            'Standard Chartered Bank' => 'Standard Chartered Bank',
        ];
    }

    public function updatedDuePaymentAttachment()
    {
        $this->validate([
            'duePaymentAttachment' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ]);

        if ($this->duePaymentAttachment) {
            $this->duePaymentAttachmentPreview = $this->getFilePreviewInfo($this->duePaymentAttachment);
        }
    }

    public function getPaymentDetails($customerId)
    {

        $this->applyToCurrent = true;
        $this->applyToBackForward = false;
        $this->paymentId = $customerId;
        $customer = Customer::withSum(['customerAccounts' => function ($query) {
            $query->where('total_due', '>', 0);
        }], 'total_due')
            ->withSum('customerAccounts', 'current_due_amount')
            ->withSum('customerAccounts', 'back_forward_amount')
            ->with(['customerAccounts' => function ($query) {
                $query->where('total_due', '>', 0)->latest()->limit(1);
            }, 'sales' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->find($customerId);

        // Calculate total return amount for this customer
        $totalReturnAmount = ReturnProduct::whereHas('sale', function ($query) use ($customerId) {
            $query->where('customer_id', $customerId);
        })->sum('total_amount');

        $this->paymentDetail = $customer;
        $rawCurrentDue = $customer->customer_accounts_sum_current_due_amount ?? 0;
        $rawTotalDue = $customer->customer_accounts_sum_total_due ?? 0;

        // Calculate actual current due after subtracting returns
        $actualCurrentDue = max(0, $rawCurrentDue - $totalReturnAmount);

        // If actual current due is 0 or negative, set all dues to 0
        if ($actualCurrentDue <= 0) {
            $this->currentDueAmount = 0;
            $this->totalDueAmount = 0;
            $this->backForwardAmount = $customer->customer_accounts_sum_back_forward_amount ?? 0;
        } else {
            $this->currentDueAmount = $actualCurrentDue;
            $this->totalDueAmount = max(0, $rawTotalDue - $totalReturnAmount);
            $this->backForwardAmount = $customer->customer_accounts_sum_back_forward_amount ?? 0;
        }

        $this->duePaymentMethod = '';
        $this->paymentNote = '';
        $this->duePaymentAttachment = null;
        $this->duePaymentAttachmentPreview = null;
        $this->receivedAmount = '';
        $this->chequeNumber = '';
        $this->bankName = '';
        $this->chequeAmount = '';
        $this->chequeDate = '';
        $this->cheques = [];
        $this->applyToCurrent = true;
        $this->applyToBackForward = false;
        $this->applyTarget = 'current';

        $this->dispatch('openModal', 'payment-detail-modal');
    }

    // ...existing code...

    public function addCheque()
    {
        $this->validate([
            'chequeNumber' => 'required',
            'bankName' => 'required',
            'chequeAmount' => 'required|numeric|min:0.01',
            'chequeDate' => 'required|date',
        ]);

        $this->cheques[] = [
            'number' => $this->chequeNumber,
            'bank' => $this->bankName,
            'amount' => floatval($this->chequeAmount),
            'date' => $this->chequeDate,
        ];

        $this->chequeNumber = '';
        $this->bankName = '';
        $this->chequeAmount = '';
        $this->chequeDate = '';
    }

    public function removeCheque($index)
    {
        if (isset($this->cheques[$index])) {
            array_splice($this->cheques, $index, 1);
        }
    }

    public function submitPayment()
    {
        $this->validate([
            'receivedAmount' => 'nullable|numeric|min:0',
            'duePaymentAttachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ]);

        // Check if there are any dues to pay against
        if ($this->currentDueAmount <= 0 && $this->backForwardAmount <= 0) {
            $this->js("Swal.fire('Error', 'No outstanding dues found for this customer.', 'error')");
            return;
        }

        // Validate that at least one target is selected
        if (!$this->applyToCurrent && !$this->applyToBackForward) {
            $this->js("Swal.fire('Error', 'Please select at least one payment target: Current Due or Brought-Forward.', 'error')");
            return;
        }

        // Validate that selected targets have due amounts (only when single option selected)
        if ($this->applyToCurrent && !$this->applyToBackForward && $this->currentDueAmount <= 0) {
            $this->js("Swal.fire('Error', 'Current due amount is zero. Cannot apply payment to current dues.', 'error')");
            return;
        }

        if (!$this->applyToCurrent && $this->applyToBackForward && $this->backForwardAmount <= 0) {
            $this->js("Swal.fire('Error', 'Brought-forward amount is zero. Cannot apply payment to Brought-forward dues.', 'error')");
            return;
        }

        // When both are selected, ensure at least one has due amount
        if ($this->applyToCurrent && $this->applyToBackForward && $this->currentDueAmount <= 0 && $this->backForwardAmount <= 0) {
            $this->js("Swal.fire('Error', 'No outstanding dues found to apply payment.', 'error')");
            return;
        }

        try {
            DB::beginTransaction();

            // Store attachment if provided
            $attachmentPath = null;
            if ($this->duePaymentAttachment) {
                $receiptName = time() . '-payment-' . $this->paymentId . '.' . $this->duePaymentAttachment->getClientOriginalExtension();
                $this->duePaymentAttachment->storeAs('public/due-receipts', $receiptName);
                $attachmentPath = "due-receipts/{$receiptName}";
            }

            $cashAmount = floatval($this->receivedAmount) ?: 0;
            $chequeTotal = collect($this->cheques)->sum('amount');
            $totalPaid = $cashAmount + $chequeTotal;

            if ($totalPaid <= 0) {
                DB::rollBack();
                $this->js("Swal.fire('Error', 'Please enter a cash amount, add cheque(s), or both.', 'error')");
                return;
            }

            $paymentMethod = $cashAmount > 0 && $chequeTotal > 0 ? 'cash and cheque' : ($chequeTotal > 0 ? 'cheque' : 'cash');

            // Get a valid sale_id
            $saleId = null;
            $customerAccountWithSale = CustomerAccount::where('customer_id', $this->paymentId)
                ->where('total_due', '>', 0)
                ->whereNotNull('sale_id')
                ->latest()
                ->first();

            if ($customerAccountWithSale && $customerAccountWithSale->sale_id) {
                $saleId = $customerAccountWithSale->sale_id;
            } else {
                $recentSale = Sale::where('customer_id', $this->paymentId)->latest()->first();
                if ($recentSale) {
                    $saleId = $recentSale->id;
                }
            }

            // Determine payment status based on selections
            $paymentStatus = 'mixed'; // Default for both
            $appliedTo = 'both';

            if ($this->applyToCurrent && !$this->applyToBackForward) {
                $paymentStatus = 'current';
                $appliedTo = 'current';
            } elseif (!$this->applyToCurrent && $this->applyToBackForward) {
                $paymentStatus = 'forward';
                $appliedTo = 'back_forward';
            }

            // Create Payment record
            $payment = Payment::create([
                'sale_id' => null,
                'customer_id' => $this->paymentId,
                'amount' => $totalPaid,
                'payment_method' => $paymentMethod,
                'due_date' => now(),
                'status' => $paymentStatus,
                'payment_reference' => $this->paymentNote,
                'is_completed' => true,
                'payment_date' => now(),
                'due_payment_method' => $paymentMethod,
                'due_payment_attachment' => $attachmentPath,
                'applied_to' => $appliedTo,
            ]);

            // Get all due accounts for the customer, ordered by creation date (oldest first)
            $customerAccounts = CustomerAccount::where('customer_id', $this->paymentId)
                ->where('total_due', '>', 0)
                ->orderBy('created_at')
                ->get();

            // If no due accounts exist but there's a back forward amount, create one
            if ($customerAccounts->isEmpty() && $this->backForwardAmount > 0) {
                $customerAccount = CustomerAccount::create([
                    'customer_id' => $this->paymentId,
                    'sale_id' => null,
                    'current_due_amount' => 0,
                    'back_forward_amount' => $this->backForwardAmount,
                    'total_due' => $this->backForwardAmount,
                    'paid_due' => 0,
                    'advance_amount' => 0,
                ]);
                $customerAccounts = collect([$customerAccount]);
            }

            // If still no accounts, create a general account
            if ($customerAccounts->isEmpty()) {
                $customerAccount = CustomerAccount::create([
                    'customer_id' => $this->paymentId,
                    'sale_id' => null,
                    'current_due_amount' => $this->currentDueAmount,
                    'back_forward_amount' => $this->backForwardAmount,
                    'total_due' => $this->currentDueAmount + $this->backForwardAmount,
                    'paid_due' => 0,
                    'advance_amount' => 0,
                ]);
                $customerAccounts = collect([$customerAccount]);
            }

            $remainingPayment = $totalPaid;

            // PRIORITY LOGIC: When both are selected, always pay Brought-Forward FIRST, then Current Due
            // When only one is selected, pay that specific type

            // Step 1: Apply to Brought-Forward FIRST (if selected or when both are selected)
            if ($this->applyToBackForward || ($this->applyToCurrent && $this->applyToBackForward)) {
                foreach ($customerAccounts as $acc) {
                    if ($remainingPayment <= 0) {
                        break;
                    }

                    $backForwardDue = max(0, floatval($acc->back_forward_amount));
                    if ($backForwardDue > 0) {
                        $payForBackForward = min($backForwardDue, $remainingPayment);
                        $newBackForward = max(0, $backForwardDue - $payForBackForward);
                        $newTotalDue = max(0, floatval($acc->current_due_amount) + $newBackForward);
                        $status = $newTotalDue == 0 ? 'Paid' : 'Partial';

                        $acc->update([
                            'paid_due' => DB::raw("paid_due + {$payForBackForward}"),
                            'back_forward_amount' => $newBackForward,
                            'total_due' => $newTotalDue,
                            'due_payment_method' => $paymentMethod,
                            'status' => $status,
                            'payment_date' => now(),
                        ]);

                        $remainingPayment -= $payForBackForward;
                    }
                }
            }

            // Step 2: Apply to Current Due (if selected and there's remaining payment)
            // This runs when: only current is selected, OR both are selected and there's remaining payment after paying back-forward
            if (($this->applyToCurrent && !$this->applyToBackForward) ||
                ($this->applyToCurrent && $this->applyToBackForward && $remainingPayment > 0)
            ) {
                foreach ($customerAccounts as $acc) {
                    if ($remainingPayment <= 0) {
                        break;
                    }

                    $currentDue = $acc->current_due_amount;
                    if ($currentDue > 0) {
                        $payForCurrent = min($currentDue, $remainingPayment);
                        $newCurrentDue = $currentDue - $payForCurrent;
                        $newTotalDue = max(0, $newCurrentDue + $acc->back_forward_amount);
                        $status = $newTotalDue == 0 ? 'Paid' : 'Partial';

                        $acc->update([
                            'paid_due' => DB::raw("paid_due + {$payForCurrent}"),
                            'current_due_amount' => $newCurrentDue,
                            'total_due' => $newTotalDue,
                            'due_payment_method' => $paymentMethod,
                            'status' => $status,
                            'payment_date' => now(),
                        ]);

                    

                        $remainingPayment -= $payForCurrent;
                    }
                }
            }

            // Step 3: Handle overpayment as advance amount
            if ($remainingPayment > 0) {
                $lastAccount = CustomerAccount::where('customer_id', $this->paymentId)
                    ->orderBy('updated_at', 'desc')
                    ->first();
                
                if ($lastAccount) {
                    // Add to existing advance amount
                    $newAdvanceAmount = ($lastAccount->advance_amount ?? 0) + $remainingPayment;
                    $lastAccount->update([
                        'advance_amount' => $newAdvanceAmount,
                        'notes' => ($lastAccount->notes ? $lastAccount->notes . "\n" : '') .
                            "Advance payment of Rs." . number_format($remainingPayment, 2) . " received on " . now()->format('Y-m-d H:i:s'),
                    ]);
                } else {
                    // Create new account for advance
                    CustomerAccount::create([
                        'customer_id' => $this->paymentId,
                        'sale_id' => null,
                        'back_forward_amount' => 0,
                        'current_due_amount' => 0,
                        'paid_due' => 0,
                        'total_due' => 0,
                        'advance_amount' => $remainingPayment,
                        'notes' => 'Advance payment of Rs.' . number_format($remainingPayment, 2) . ' received on ' . now()->format('Y-m-d H:i:s'),
                    ]);
                }

                // Show success message with advance amount info
                $this->dispatch('showAdvancePayment', [
                    'amount' => $remainingPayment,
                    'customerName' => $this->paymentDetail->name
                ]);
            }

            // Save cheques with payment_id
            foreach ($this->cheques as $cheque) {
                Cheque::create([
                    'cheque_number' => $cheque['number'],
                    'cheque_date' => $cheque['date'],
                    'bank_name' => $cheque['bank'],
                    'cheque_amount' => $cheque['amount'],
                    'status' => 'pending',
                    'customer_id' => $this->paymentId,
                    'payment_id' => $payment->id,
                ]);
            }

            // Add notes to the last updated account (excluding advance notes already added)
            if ($this->paymentNote) {
                $lastUpdatedAccount = CustomerAccount::where('customer_id', $this->paymentId)
                    ->orderBy('updated_at', 'desc')
                    ->first();
                if ($lastUpdatedAccount) {
                    $notes = ($lastUpdatedAccount->notes ? $lastUpdatedAccount->notes . "\n" : '') .
                        "Payment received on " . now()->format('Y-m-d H:i') . ": " . $this->paymentNote;
                    $lastUpdatedAccount->update(['notes' => $notes]);
                }
            }

            // Set attachment to the last updated account
            if ($attachmentPath) {
                $lastUpdatedAccount = CustomerAccount::where('customer_id', $this->paymentId)
                    ->orderBy('updated_at', 'desc')
                    ->first();
                if ($lastUpdatedAccount) {
                    $lastUpdatedAccount->update(['due_payment_attachment' => $attachmentPath]);
                }
            }

            DB::commit();

            $this->dispatch('closeModal', 'payment-detail-modal');
            
            // Show different success message if there was an advance payment
            if ($remainingPayment > 0) {
                $this->js("Swal.fire('Success', 'Payment submitted successfully. Advance amount of Rs." . number_format($remainingPayment, 2) . " has been recorded.', 'success')");
            } else {
                $this->js("Swal.fire('Success', 'Payment submitted successfully.', 'success')");
            }

            $this->reset([
                'paymentDetail',
                'duePaymentMethod',
                'duePaymentAttachment',
                'paymentNote',
                'receivedAmount',
                'totalDueAmount',
                'currentDueAmount',
                'backForwardAmount',
                'chequeNumber',
                'bankName',
                'chequeAmount',
                'chequeDate',
                'cheques',
                'applyToCurrent',
                'applyToBackForward'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $this->js("Swal.fire('Error', 'Failed to submit payment: " . addslashes($e->getMessage()) . "', 'error')");
        }
    }

    public function resetFilters()
    {
        $this->filters = [
            'status' => '',
            'dateFrom' => '',
            'dateTo' => '',
        ];
    }

    public function printDuePayments()
    {
        $this->dispatch('print-due-payments');
    }

    public function render()
    {
        $query = Customer::withSum(['customerAccounts' => function ($subQuery) {
            $subQuery->where('total_due', '>', 0);
        }], 'total_due')
            ->withSum('customerAccounts', 'current_due_amount')
            ->withSum('customerAccounts', 'back_forward_amount')
            ->withMin('customerAccounts', 'created_at')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%');
                })->orWhereHas('sales', function ($s) {
                    $s->where('invoice_number', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filters['status'] !== '', function ($query) {
                if ($this->filters['status'] === 'pending') {
                    $query->whereRelation('customerAccounts', 'total_due', '>', 0);
                } elseif ($this->filters['status'] === 'paid') {
                    $query->whereDoesntHave('customerAccounts', function ($q) {
                        $q->where('total_due', '>', 0);
                    });
                }
            })
            ->when($this->filters['dateFrom'], function ($query) {
                $query->whereHas('customerAccounts', function ($q) {
                    $q->whereDate('created_at', '>=', $this->filters['dateFrom']);
                });
            })
            ->when($this->filters['dateTo'], function ($query) {
                $query->whereHas('customerAccounts', function ($q) {
                    $q->whereDate('created_at', '<=', $this->filters['dateTo']);
                });
            })
            ->whereHas('customerAccounts', function ($q) {
                $q->where('total_due', '>', 0);
            });

        $duePayments = $query->orderBy('id')->paginate(10);

        // Calculate adjusted due amounts after returns
        foreach ($duePayments as $customer) {
            // Calculate total return amount for this customer
            $totalReturnAmount = ReturnProduct::whereHas('sale', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            })->sum('total_amount');

            // Store original values
            $customer->original_current_due = $customer->customer_accounts_sum_current_due_amount ?? 0;
            $customer->original_total_due = $customer->customer_accounts_sum_total_due ?? 0;
            $customer->total_return_amount = $totalReturnAmount;

            // Calculate adjusted current due (current due - returns)
            $adjustedCurrentDue = max(0, $customer->original_current_due - $totalReturnAmount);

            // If adjusted current due is 0 or negative, set all current dues to 0
            if ($adjustedCurrentDue <= 0) {
                $customer->adjusted_current_due = 0;
                $customer->adjusted_total_due = $customer->customer_accounts_sum_back_forward_amount ?? 0;
            } else {
                $customer->adjusted_current_due = $adjustedCurrentDue;
                $customer->adjusted_total_due = max(0, $customer->original_total_due - $totalReturnAmount);
            }
        }

        // Filter out customers with 0 adjusted current due if they have no brought forward
        $duePayments->getCollection()->transform(function ($customer) {
            $hasRealDue = $customer->adjusted_current_due > 0 ||
                ($customer->customer_accounts_sum_back_forward_amount ?? 0) > 0;
            return $hasRealDue ? $customer : null;
        })->filter();

        $duePaymentsCount = Customer::whereHas('customerAccounts', function ($q) {
            $q->where('total_due', '>', 0);
        })->count();

        $totalDue = CustomerAccount::where('total_due', '>', 0)->sum('total_due');

        // Subtract total returns from total due
        $totalReturns = ReturnProduct::sum('total_amount');
        $adjustedTotalDue = max(0, $totalDue - $totalReturns);

        $todayDuePayments = CustomerAccount::where('total_due', '>', 0)->whereDate('created_at', today())->sum('total_due');

        $todayDuePaymentsCount = Customer::whereHas('customerAccounts', function ($q) {
            $q->where('total_due', '>', 0)->whereDate('created_at', today());
        })->count();

        return view('livewire.admin.due-payments', [
            'duePayments' => $duePayments,
            'duePaymentsCount' => $duePaymentsCount,
            'todayDuePayments' => $todayDuePayments,
            'todayDuePaymentsCount' => $todayDuePaymentsCount,
            'totalDue' => $adjustedTotalDue,
            'cheques' => $this->cheques,
            'chequeNumber' => $this->chequeNumber,
            'bankName' => $this->bankName,
            'chequeAmount' => $this->chequeAmount,
            'chequeDate' => $this->chequeDate,
        ]);
    }

    private function getFilePreviewInfo($file)
    {
        if (!$file) return null;

        $result = [
            'type' => 'file',
            'name' => $file->getClientOriginalName(),
            'preview' => null,
        ];

        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $result['type'] = 'image';
            $result['preview'] = $file->temporaryUrl();
        } elseif ($extension === 'pdf') {
            $result['type'] = 'pdf';
        } else {
            $result['icon'] = 'bi-file-earmark';
            $result['color'] = 'text-gray-600';
        }

        return $result;
    }
}
