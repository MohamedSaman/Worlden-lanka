<?php
// File: app/Livewire/Admin/BackForward.php

namespace App\Livewire\Admin;

use App\Models\Customer;
use App\Models\CustomerAccount;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;
use Exception;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;

#[Title("Back-Forward Management")]
#[Layout('components.layouts.admin')]
class BackForward extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $editCustomerId;
    public $adjustmentAmount;
    public $adjustmentNotes = '';
    public $isEditing = false;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function adjustBackForward($customerId)
    {
        $this->editCustomerId = $customerId;
        $this->adjustmentAmount = 0;
        $this->adjustmentNotes = '';
        $this->isEditing = false;
        $this->dispatch('open-adjust-modal');
    }

    public function editBackForward($customerId)
    {
        $this->editCustomerId = $customerId;
        $this->adjustmentNotes = '';
        $this->isEditing = true;

        $account = CustomerAccount::where('customer_id', $customerId)->first();
        $this->adjustmentAmount = $account ? floatval($account->back_forward_amount ?? 0) : 0;

        $this->dispatch('open-adjust-modal');
    }

    public function saveAdjustment()
    {
        $this->validate([
            'adjustmentAmount' => 'required|numeric',
            'adjustmentNotes' => 'nullable|string|max:500',
        ]);

        try {
            // Ensure single row per customer in customer_accounts
            $account = CustomerAccount::where('customer_id', $this->editCustomerId)->first();

            if ($this->isEditing) {
                // Overwrite the back_forward_amount with the provided value
                if ($account) {
                    $account->back_forward_amount = floatval($this->adjustmentAmount);
                    $account->total_due = floatval($account->back_forward_amount ?? 0) + floatval($account->current_due_amount ?? 0);
                    $account->save();
                } else {
                    // If no account exists, create one with the provided value
                    CustomerAccount::create([
                        'customer_id'         => $this->editCustomerId,
                        'sale_id'             => null,
                        'back_forward_amount' => floatval($this->adjustmentAmount),
                        'current_due_amount'  => 0,
                        'paid_due'            => 0,
                        'total_due'           => floatval($this->adjustmentAmount),
                    ]);
                }
            } else {
                if ($account) {
                    // Add adjustment to back_forward_amount only; do not change current_due_amount
                    $account->back_forward_amount = floatval($account->back_forward_amount ?? 0) + floatval($this->adjustmentAmount);
                    // Recalculate total due as back_forward + current_due
                    $account->total_due = floatval($account->back_forward_amount ?? 0) + floatval($account->current_due_amount ?? 0);
                    // Do not modify paid_due here
                    $account->save();
                } else {
                    // Create a new account row for this customer with only back-forward amount
                    CustomerAccount::create([
                        'customer_id'         => $this->editCustomerId,
                        'sale_id'             => null, // Adjustment without sale
                        'back_forward_amount' => floatval($this->adjustmentAmount),
                        'current_due_amount'  => 0,
                        'paid_due'            => 0,
                        'total_due'           => floatval($this->adjustmentAmount),
                    ]);
                }
            }

            $this->js("Swal.fire('Success!', 'Back-forward adjustment saved successfully.', 'success')");
            $this->dispatch('hide-adjust-modal');
            $this->reset(['editCustomerId', 'adjustmentAmount', 'adjustmentNotes', 'isEditing']);
        } catch (Exception $e) {
            Log::error('Error saving adjustment: ' . $e->getMessage());
            $this->js("Swal.fire('Error!', 'Failed to save adjustment: " . addslashes($e->getMessage()) . "', 'error')");
        }
    }

    public function render()
    {
        $customers = Customer::query()
            ->withSum('customerAccounts', 'back_forward_amount')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('phone', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('business_name', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('id')
            ->paginate(10);

        return view('livewire.admin.back-forward', [
            'customers' => $customers,
        ]);
    }
}
