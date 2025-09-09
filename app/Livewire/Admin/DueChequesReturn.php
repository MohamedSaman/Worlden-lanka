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
            'chequeAmount' => 'required|numeric',
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
    $original = Cheque::find($this->selectedChequeId);

    foreach ($this->cheques as $cheque) {
        // Update the original cheque record
        $original->update([
            'cheque_number' => $cheque['number'],
            'bank_name' => $cheque['bank'],
            'cheque_amount' => $cheque['amount'],
            'cheque_date' => $cheque['date'],
            'status' => 'pending', // mark as pending after re-entry
        ]);
    }

    // Refresh table
    $this->chequeDetails = Cheque::with('customer')->where('status', 'return')->get();

    // Close modal
    $this->dispatch('close-reentry-modal');
    $this->cheques = [];
}


    public function render()
    {
        return view('livewire.admin.due-cheques-return');
    }
    
}
