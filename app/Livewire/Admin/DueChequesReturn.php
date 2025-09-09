<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Cheque;

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
    public $selectedChequeId;
    public $customerId;

    public function mount()
    {
        // Load cheques with customer relationship
        $this->chequeDetails = Cheque::with('customer')
            ->where('status', 'return')
            ->get();
    }

    // Open modal for re-entry
    public function openReentryModal($chequeId)
    {
        $this->selectedChequeId = $chequeId;
        $this->reset(['chequeNumber', 'bankName', 'chequeAmount', 'chequeDate', 'cheques']);
        $this->dispatch('open-reentry-modal'); // Livewire 3 event
    }

    // Add a new cheque to temporary array
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

    // Save new cheque(s) and update original cheque status
    public function submitNewCheque()
    {
        $originalCheque = Cheque::find($this->selectedChequeId);

        if (!$originalCheque) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Original cheque not found.']);
            return;
        }

        // Calculate total amount of new cheques
        $totalNewChequeAmount = array_sum(array_column($this->cheques, 'amount'));

        // Verify if total new cheque amount matches the original cheque amount
        if ($totalNewChequeAmount != $originalCheque->cheque_amount) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Total amount of new cheques (' . $totalNewChequeAmount . ') does not match the original cheque amount (' . $originalCheque->cheque_amount . ').']);
            return;
        }

        // Save new cheques with payment_id from original cheque
        foreach ($this->cheques as $cheque) {
            Cheque::create([
                'customer_id'     => $originalCheque->customer_id,
                'cheque_number'   => $cheque['number'],
                'bank_name'       => $cheque['bank'],
                'cheque_amount'   => $cheque['amount'],
                'cheque_date'     => $cheque['date'],
                'status'          => 'pending',
                'payment_id'      => $originalCheque->payment_id, // Added payment_id
            ]);
        }

        // Update original cheque status to reflect re-entry
        $originalCheque->update(['status' => 'pending']);

        // Refresh table
        $this->chequeDetails = Cheque::with('customer')->where('status', 'return')->get();

        // Clear modal and array
        $this->cheques = [];
        $this->dispatch('close-reentry-modal');

        $this->dispatch('notify', ['type' => 'success', 'message' => 'New cheque(s) submitted successfully.']);
    }

    public function render()
    {
        return view('livewire.admin.due-cheques-return');
    }
}