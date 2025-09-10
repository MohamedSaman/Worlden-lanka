<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Cheque;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

#[Layout('components.layouts.admin')]
#[Title('Due Cheques')]

class DueChequesReturn extends Component
{
    public $chequeDetails;
    public $cheques = []; // Temporary array for new cheques
    public $chequeNumber;
    public $bankName;
    public $chequeAmount;
    public $chequeDate;
    public $cashAmount = 0;
    public $note;
    public $selectedChequeId;
    public $originalCheque; // Store original cheque details for modal

    // For Complete with Cash
    public $completeCashAmount;
    public $completeNote;
    public $banks = [];

    public function mount()
    {

        $this->loadBanks();
        // Load cheques with customer relationship
        $this->chequeDetails = Cheque::with('customer')
            ->where('status', 'return')
            ->get();
    }

    public function loadBanks()
    {
        $this->banks = [
            'Bank of Ceylon (BOC)'=>'Bank of Ceylon (BOC)',
            'Commercial Bank of Ceylon (ComBank)'=>'Commercial Bank of Ceylon (ComBank)',
            'Hatton National Bank (HNB)'=>'Hatton National Bank (HNB)',
            'People\'s Bank'=>'People\'s Bank',
            'Sampath Bank'=>'Sampath Bank',
            'National Development Bank (NDB)'=>'National Development Bank (NDB)',
            'DFCC Bank'=>'DFCC Bank',
            'Nations Trust Bank (NTB)'=>'Nations Trust Bank (NTB)',
            'Seylan Bank'=>'Seylan Bank',
            'Amana Bank'=>'Amana Bank',
            'Cargills Bank'=>'Cargills Bank',
            'Pan Asia Banking Corporation'=>'Pan Asia Banking Corporation',
            'Union Bank of Colombo'=>'Union Bank of Colombo',
            'Bank of China Ltd'=>'Bank of China Ltd',
            'Citibank, N.A.'=>'Citibank, N.A.',
            'Habib Bank Ltd'=>'Habib Bank Ltd',
            'Indian Bank'=>'Indian Bank',
            'Indian Overseas Bank'=>'Indian Overseas Bank',
            'MCB Bank Ltd'=>'MCB Bank Ltd',
            'Public Bank Berhad'=>'Public Bank Berhad',
            'Standard Chartered Bank'=>'Standard Chartered Bank',
        ];
    }
    // Open modal for re-entry
    public function openReentryModal($chequeId)
    {
        $this->selectedChequeId = $chequeId;
        $this->originalCheque = Cheque::with('customer')->find($chequeId);
        $this->reset(['chequeNumber', 'bankName', 'chequeAmount', 'chequeDate', 'cashAmount', 'note', 'cheques']);
        $this->dispatch('open-reentry-modal'); // Livewire 3 event
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
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Original cheque not found.']);
            return;
        }

        // Calculate total amount of new cheques + cash
        $totalNewChequeAmount = array_sum(array_column($this->cheques, 'amount'));
        $totalAmount = $totalNewChequeAmount + ($this->cashAmount ?: 0);

        // Validate inputs
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

        // Custom validation for total amount
        if ($totalAmount != $originalCheque->cheque_amount) {
            $this->addError('total', 'The total amount of cheques and cash (' . number_format($totalAmount, 2) . ') must equal the original cheque amount (' . number_format($originalCheque->cheque_amount, 2) . ').');
            return;
        }

        // Ensure at least one payment method (cheque or cash) is provided
        if (empty($this->cheques) && $this->cashAmount <= 0) {
            $this->addError('total', 'Please add at least one cheque or a cash amount.');
            return;
        }

        DB::beginTransaction();

        try {
            // Save new cheques with payment_id from original cheque
            foreach ($this->cheques as $cheque) {
                // dd($cheque['bank']);
                Cheque::create([
                    'customer_id'     => $originalCheque->customer_id,
                    'cheque_number'   => $cheque['number'],
                    'bank_name'       => $cheque['bank'],
                    'cheque_amount'   => $cheque['amount'],
                    'cheque_date'     => $cheque['date'],
                    'status'          => 'pending',
                    'payment_id'      => $originalCheque->payment_id,
                    'notes'           => $this->note,
                ]);
            }

            // If cash amount is provided, create a new cash payment
            // if ($this->cashAmount > 0) {
            //     $originalPayment = Payment::find($originalCheque->payment_id);
            //     Payment::create([
            //         'sale_id'         => $originalPayment->sale_id,
            //         'admin_sale_id'   => $originalPayment->admin_sale_id,
            //         'amount'          => $this->cashAmount,
            //         'payment_method'  => 'cash',
            //         'is_completed'    => true,
            //         'payment_date'    => now(),
            //         'status'          => 'Paid',
            //         'notes'           => $this->note,
            //     ]);
            // }

            // Update original cheque status to 'cancel'
            $originalCheque->update([
                'status' => 'cancel',
                'note'  => $this->note,
        ]);

            DB::commit();

            // Refresh table
            $this->chequeDetails = Cheque::with('customer')->where('status', 'return')->get();

            // Clear modal and array
            $this->cheques = [];
            $this->originalCheque = null;
            $this->reset(['cashAmount', 'note', 'chequeNumber', 'bankName', 'chequeAmount', 'chequeDate']);
            $this->dispatch('close-reentry-modal');

            $this->dispatch('notify', ['type' => 'success', 'message' => 'New cheque(s) and/or cash submitted successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error submitting new cheque/cash: ' . $e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    // Open modal for complete with cash
    public function openCompleteModal($chequeId)
    {
        $this->selectedChequeId = $chequeId;
        $this->originalCheque = Cheque::with('customer')->find($chequeId);
        $this->completeCashAmount = $this->originalCheque->cheque_amount;
        $this->reset(['completeNote']);
        $this->dispatch('open-complete-modal');
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
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Original cheque not found.']);
            return;
        }

        // Verify if cash amount matches the original cheque amount
        if ($this->completeCashAmount != $originalCheque->cheque_amount) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Cash amount (' . number_format($this->completeCashAmount, 2) . ') must match the original cheque amount (' . number_format($originalCheque->cheque_amount, 2) . ').']);
            return;
        }

        DB::beginTransaction();

        try {
            $originalPayment = Payment::find($originalCheque->payment_id);

            // Create new cash payment
            Payment::create([
                'sale_id'         => $originalPayment->sale_id,
                'admin_sale_id'   => $originalPayment->admin_sale_id,
                'amount'          => $this->completeCashAmount,
                'payment_method'  => 'cash',
                'is_completed'    => true,
                'payment_date'    => now(),
                'status'          => 'Paid',
                'notes'           => $this->completeNote,
            ]);

            // Update original cheque status to 'cancel'
            $originalCheque->update([
                'status' => 'cancel',
                'note'  => $this->completeNote,
            ]);

            DB::commit();

            // Refresh table
            $this->chequeDetails = Cheque::with('customer')->where('status', 'return')->get();

            $this->originalCheque = null;
            $this->reset(['completeCashAmount', 'completeNote']);
            $this->dispatch('close-complete-modal');

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Cheque completed with cash successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error completing cheque with cash: ' . $e->getMessage());
            $this->dispatch('notify', ['type' => 'error', 'message' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.admin.due-cheques-return');
    }
}
