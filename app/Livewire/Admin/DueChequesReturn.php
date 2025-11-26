<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Cheque;
use App\Models\Payment;
use App\Models\ReturnCheque;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.admin')]
#[Title('Due Cheques')]

class DueChequesReturn extends Component
{
    public $chequeDetails;
    public $returnCheques;
    public $cheques = []; // Temporary array for new cheques
    public $chequeNumber;
    public $bankName;
    public $chequeAmount;
    public $chequeDate;
    public $cashAmount = 0;
    public $note;
    public $selectedChequeId;
    public $originalCheque; // Store original cheque details for modal
    public $originalReturnCheque;
    public $completeCashAmount;
    public $completeNote;
    public $banks = [];
    public $search = '';

    // Receive modal fields
    public $selectedReturnChequeId;
    public $receiveAmount = 0;
    public $receiveMethod = 'cash';
    public $receiveNote = '';
    public $receiveChequeNumber;
    public $receiveBankName;
    public $receiveChequeDate;
    public $receiveCheques = []; // multiple cheques to be added before submit
    public $receiveCashAmount = 0;

    public function mount()
    {
        $this->loadBanks();
        // Load return_cheques with related cheque and customer (only unresolved)
        $this->loadReturnCheques();
    }

    public function updatedSearch()
    {
        $this->loadReturnCheques();
    }

    protected function loadReturnCheques()
    {
        $query = ReturnCheque::with(['cheque', 'customer'])
            ->where(function ($q) {
                $q->where('status', '<>', 'complete')
                    ->where('balance_amount', '>', 0);
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('customer', function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%");
                })->orWhereHas('cheque', function ($q2) {
                    $q2->where('cheque_number', 'like', "%{$this->search}%");
                });
            });
        }

        $this->returnCheques = $query->orderBy('created_at', 'desc')->get();
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

    // Open modal for viewing cheque details
    public function openViewModal($chequeId)
    {
        $this->selectedChequeId = $chequeId;

        // Try to load as a cheque id first
        $cheque = Cheque::with('customer')->find($chequeId);
        if ($cheque) {
            $this->originalCheque = $cheque;
            $this->originalReturnCheque = null;
            $this->dispatch('open-view-modal');
            return;
        }

        // If not found, try to load as a return_cheque id
        $return = ReturnCheque::with(['cheque', 'customer'])->find($chequeId);
        if ($return) {
            $this->originalReturnCheque = $return;
            $this->originalCheque = $return->cheque ?? null;
            $this->dispatch('open-view-modal');
            return;
        }

        $this->dispatch('notify', ['type' => 'error', 'message' => 'Cheque not found.']);
    }

    // Open modal for re-entry
    public function openReentryModal($chequeId)
    {
        // Re-entry removed in favor of receive flow; keep method for compatibility but do nothing
        return;
    }

    // Add a new cheque to temporary array
    public function addCheque()
    {
        $this->validate([
            'chequeNumber' => 'required_if:cashAmount,0|string|max:255',
            'bankName' => 'required_if:cashAmount,0|string|max:255',
            'chequeAmount' => 'required_if:cashAmount,0|numeric|min:0.01',
            'chequeDate' => 'required_if:cashAmount,0|date',
        ]);

        $this->cheques[] = [
            'number' => $this->chequeNumber,
            'bank' => $this->bankName,
            'amount' => $this->chequeAmount,
            'date' => $this->chequeDate,
        ];

        $this->reset(['chequeNumber', 'bankName', 'chequeAmount', 'chequeDate']);
    }

    // Remove cheque from temporary array
    public function removeCheque($index)
    {
        unset($this->cheques[$index]);
        $this->cheques = array_values($this->cheques);
    }

    // Save new cheque(s) and/or cash, update original cheque status
    public function submitNewCheque()
    {
        $originalCheque = Cheque::find($this->selectedChequeId);

        if (!$originalCheque) {
            $this->js('swal.fire("Error", "Original cheque not found.", "error")');
            return;
        }

        $totalNewChequeAmount = array_sum(array_column($this->cheques, 'amount'));
        $totalAmount = $totalNewChequeAmount + ($this->cashAmount ?: 0);

        $this->validate([
            'cashAmount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
            'chequeNumber' => 'required_without:cashAmount|string|max:255|nullable',
            'bankName' => 'required_without:cashAmount|string|max:255|nullable',
            'chequeAmount' => 'required_without:cashAmount|numeric|min:0.01|nullable',
            'chequeDate' => 'required_without:cashAmount|date|nullable',
        ], [
            'chequeNumber.required_without' => 'Cheque Number is required if no cash amount is provided.',
            'bankName.required_without' => 'Bank Name is required if no cash amount is provided.',
            'chequeAmount.required_without' => 'Cheque Amount is required if no cash amount is provided.',
            'chequeDate.required_without' => 'Cheque Date is required if no cash amount is provided.',
        ]);

        if ($totalAmount != $originalCheque->cheque_amount) {
            $this->addError('total', 'The total amount of cheques and cash (' . number_format($totalAmount, 2) . ') must equal the original cheque amount (' . number_format($originalCheque->cheque_amount, 2) . ').');
            return;
        }

        if (empty($this->cheques) && $this->cashAmount <= 0) {
            $this->addError('total', 'Please add at least one cheque or a cash amount.');
            return;
        }

        DB::beginTransaction();

        try {
            foreach ($this->cheques as $cheque) {
                Cheque::create([
                    'customer_id' => $originalCheque->customer_id,
                    'cheque_number' => $cheque['number'],
                    'bank_name' => $cheque['bank'],
                    'cheque_amount' => $cheque['amount'],
                    'cheque_date' => $cheque['date'],
                    'status' => 'pending',
                    'payment_id' => $originalCheque->payment_id,
                    'notes' => $this->note,
                ]);
            }

            $originalCheque->update([
                'status' => 'cancel',
                'note' => $this->note,
            ]);

            DB::commit();

            $this->chequeDetails = Cheque::with('customer')->where('status', 'return')->get();

            $this->cheques = [];
            $this->originalCheque = null;
            $this->reset(['cashAmount', 'note', 'chequeNumber', 'bankName', 'chequeAmount', 'chequeDate']);
            $this->dispatch('close-reentry-modal');
            $this->js('swal.fire("Success", "New cheque(s) and/or cash submitted successfully.", "success")');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting new cheque/cash: ' . $e->getMessage());
            $this->js('swal.fire("Error", "An error occurred: ' . addslashes($e->getMessage()) . '", "error")');
        }
    }

    // Open modal for complete with cash
    public function openCompleteModal($chequeId)
    {
        // Replaced by receive modal
        $this->openReceiveModal($chequeId);
    }

    public function openReceiveModal($returnChequeId)
    {
        $this->selectedReturnChequeId = $returnChequeId;
        $return = ReturnCheque::with(['cheque', 'customer'])->find($returnChequeId);
        if (!$return) {
            $this->js('swal.fire("Error", "Return cheque not found.", "error")');
            return;
        }

        $this->originalCheque = $return->cheque;
        $this->originalReturnCheque = $return;
        $this->receiveAmount = 0; // temp for adding a cheque amount
        $this->receiveMethod = 'cash';
        $this->receiveNote = '';
        $this->receiveChequeNumber = null;
        $this->receiveBankName = null;
        $this->receiveChequeDate = null;
        $this->receiveCheques = [];
        $this->receiveCashAmount = 0;

        $this->dispatch('open-receive-modal');
    }
    public function submitReceivePayment()
    {
        $return = ReturnCheque::find($this->selectedReturnChequeId);
        if (!$return) {
            $this->js('swal.fire("Error", "Return cheque not found.", "error")');
            return;
        }

        $this->validate([
            'receiveCashAmount' => 'nullable|numeric|min:0',
            'receiveCheques' => 'array',
            'receiveCheques.*.number' => 'required_with:receiveCheques|string|max:255',
            'receiveCheques.*.bank' => 'required_with:receiveCheques|string|max:255',
            'receiveCheques.*.amount' => 'required_with:receiveCheques|numeric|min:0.01',
            'receiveCheques.*.date' => 'required_with:receiveCheques|date',
        ]);

        $totalChequesAmount = !empty($this->receiveCheques) ? array_sum(array_column($this->receiveCheques, 'amount')) : 0;
        $totalReceived = $totalChequesAmount + ($this->receiveCashAmount ?: 0);

        if ($totalReceived <= 0) {
            $this->js('swal.fire("Error", "Please enter at least one payment (cash or cheque).", "error")');
            return;
        }

        if ($totalReceived > $return->balance_amount) {
            $this->js('swal.fire("Error", "Total received amount cannot exceed the balance amount.", "error")');
            return;
        }

        DB::beginTransaction();
        try {
            // Create Cheque records for each submitted cheque
            foreach ($this->receiveCheques as $c) {
                Cheque::create([
                    'customer_id' => $return->customer_id,
                    'cheque_number' => $c['number'],
                    'bank_name' => $c['bank'],
                    'cheque_amount' => $c['amount'],
                    'cheque_date' => $c['date'],
                    'status' => 'pending',
                    'payment_id' => $return->cheque_id ? Cheque::find($return->cheque_id)->payment_id ?? null : null,
                    'notes' => $this->receiveNote,
                ]);
            }

            // Update return cheque paid and balance
            $return->paid_amount = $return->paid_amount + $totalReceived;
            $return->balance_amount = $return->balance_amount - $totalReceived;
            $return->notes = trim(($return->notes ? $return->notes . "\n" : '') . 'Received: Rs. ' . number_format($totalReceived, 2) . ' - ' . ($this->receiveNote ?: '-'));
            if ($return->balance_amount <= 0) {
                $return->balance_amount = 0;
                $return->status = 'complete';
            } else {
                $return->status = 'partial';
            }
            $return->save();

            DB::commit();

            // reset form
            $this->receiveCheques = [];
            $this->receiveCashAmount = 0;
            $this->receiveNote = '';

            $this->loadReturnCheques();
            $this->dispatch('close-receive-modal');
            $this->js('swal.fire("Success", "Payment received successfully.", "success")');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error receiving return cheque payment: ' . $e->getMessage());
            $this->js('swal.fire("Error", "An error occurred: ' . addslashes($e->getMessage()) . '", "error")');
        }
    }

    public function addReceiveCheque()
    {
        $this->validate([
            'receiveChequeNumber' => 'required|string|max:255',
            'receiveBankName' => 'required|string|max:255',
            'receiveChequeDate' => 'required|date',
            'receiveAmount' => 'required|numeric|min:0.01',
        ]);

        $this->receiveCheques[] = [
            'number' => $this->receiveChequeNumber,
            'bank' => $this->receiveBankName,
            'amount' => $this->receiveAmount,
            'date' => $this->receiveChequeDate,
        ];

        $this->receiveChequeNumber = null;
        $this->receiveBankName = null;
        $this->receiveChequeDate = null;
        $this->receiveAmount = 0;
    }

    public function removeReceiveCheque($index)
    {
        unset($this->receiveCheques[$index]);
        $this->receiveCheques = array_values($this->receiveCheques);
    }

    // Submit complete with cash
    public function submitCompleteWithCash()
    {
        $this->validate([
            'completeCashAmount' => 'required|numeric|min:0.01',
            'completeNote' => 'required|string|max:500',
        ]);

        $originalCheque = Cheque::find($this->selectedChequeId);

        if (!$originalCheque) {
            $this->js('swal.fire("Error", "Original cheque not found.", "error")');
            return;
        }

        if ($this->completeCashAmount != $originalCheque->cheque_amount) {
            $this->js('swal.fire("Error", "Cash amount (' . number_format($this->completeCashAmount, 2) . ') must match the original cheque amount (' . number_format($originalCheque->cheque_amount, 2) . ').", "error")');
            return;
        }

        DB::beginTransaction();

        try {
            $originalPayment = Payment::find($originalCheque->payment_id);

            $originalPayment->update([
                'amount' => $this->completeCashAmount,
                'payment_method' => 'cash',
                'is_completed' => true,
                'payment_date' => now(),
                'status' => 'Paid',
                'notes' => $this->completeNote,
            ]);

            $originalCheque->update([
                'status' => 'cancel',
                'note' => $this->completeNote,
            ]);

            DB::commit();

            $this->chequeDetails = Cheque::with('customer')->where('status', 'return')->get();

            $this->originalCheque = null;
            $this->reset(['completeCashAmount', 'completeNote']);
            $this->dispatch('close-complete-modal');

            $this->js('swal.fire("Success", "Cheque completed with cash successfully.", "success")');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error completing cheque with cash: ' . $e->getMessage());
            $this->js('swal.fire("Error", "An error occurred: ' . addslashes($e->getMessage()) . '", "error")');
        }
    }

    public function render()
    {
        return view('livewire.admin.due-cheques-return', [
            'returnCheques' => $this->returnCheques ?? []
        ]);
    }
}
