<?php

namespace App\Livewire\Admin;

use App\Models\Cheque;
use App\Models\Payment;
use App\Models\Sale;
use App\Models\ReturnCheque;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title("Cheque Payments")]
#[Layout('components.layouts.admin')]
class DueCheques extends Component
{
    use WithPagination, WithFileUploads;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedPayment = null;
    public $paymentDetail = null;
    public $duePaymentAttachment;
    public $paymentId;
    public $duePaymentMethod = 'cheque';
    public $paymentNote = '';
    public $duePaymentAttachmentPreview;
    public $receivedAmount = '';
    public $filters = [
        'status' => '',
        'dateRange' => '',
    ];
    public $extendDuePaymentId;
    public $newDueDate;
    public $extensionReason = '';
    // ID of the cheque awaiting return confirmation
    public $returningChequeId = null;
    // ID of the cheque awaiting complete confirmation
    public $completingChequeId = null;
    public $selectedCheque = null;

    // Edit mode properties
    public $isEditingCheque = false;
    public $editChequeNumber = '';
    public $editChequeDate = '';

    // Statistics properties
    public $pendingChequeCount = 0;
    public $completeChequeCount = 0;
    public $returnChequeCount = 0;
    public $totalDueAmount = 0;

    protected $listeners = ['refreshPayments' => '$refresh'];

    public function mount()
    {
        $this->computeStatistics();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'filters.status', 'filters.dateRange'])) {
            $this->resetPage();
            $this->computeStatistics();
        }
    }

    public function computeStatistics()
    {
        $baseQuery = Cheque::query()
            ->where(function ($query) {
                // Include cheques with valid sales for current user
                $query->whereHas('payment.sale', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                    // OR include cheques with pending status (even if sale_id is null)
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->whereHas('payment', function ($paymentQuery) {
                                $paymentQuery->whereNull('sale_id');
                            });
                    });
            });

        $filteredQuery = clone $baseQuery;

        if ($this->search) {
            $filteredQuery->where(function ($q) {
                $q->whereHas('payment.sale', function ($q2) {
                    $q2->where('invoice_number', 'like', "%{$this->search}%");
                })->orWhereHas('customer', function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            });
        }

        if ($this->filters['status'] === 'null') {
            $filteredQuery->whereNull('status');
        } elseif ($this->filters['status']) {
            $filteredQuery->where('status', $this->filters['status']);
        }

        if ($this->filters['dateRange']) {
            [$startDate, $endDate] = explode(' to ', $this->filters['dateRange']);
            $filteredQuery->whereBetween('cheque_date', [$startDate, $endDate]);
        }

        $this->pendingChequeCount = (clone $filteredQuery)->where('status', 'pending')->count();
        $this->completeChequeCount = (clone $filteredQuery)->where('status', 'complete')->count();
        $this->returnChequeCount = (clone $filteredQuery)->where('status', 'return')->count();
        $this->totalDueAmount = (clone $filteredQuery)->where('status', 'pending')->sum('cheque_amount');
    }

    public function completePaymentDetails($chequeId)
    {
        try {
            DB::beginTransaction();

            $cheque = Cheque::findOrFail($chequeId);

            // Ensure the cheque can be marked as complete
            if ($cheque->status !== 'pending' && !is_null($cheque->status)) {
                throw new Exception('This cheque cannot be marked as complete.');
            }

            $cheque->update([
                'status' => 'complete',
            ]);

            // Add a note to the related sale if it exists
            if ($cheque->payment && $cheque->payment->sale) {
                $sale = $cheque->payment->sale;
                $sale->update([
                    'notes' => ($sale->notes ? $sale->notes . "\n" : '') .
                        "Cheque marked as complete on " . now()->format('Y-m-d H:i') . "."
                ]);
            }

            DB::commit();

            $this->computeStatistics();
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Cheque marked as complete successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to mark cheque as complete: ' . $e->getMessage()
            ]);
        }
    }

    public function confirmComplete($chequeId)
    {
        $this->completingChequeId = $chequeId;
        $this->dispatch('openModal', 'confirm-complete-modal');
    }

    public function completePaymentConfirmed()
    {
        if (!$this->completingChequeId) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'No cheque selected to complete.'
            ]);
            return;
        }

        // Call existing completePaymentDetails logic
        $chequeId = $this->completingChequeId;
        $this->completingChequeId = null;

        $this->completePaymentDetails($chequeId);

        // Close confirmation modal
        $this->dispatch('closeModal', 'confirm-complete-modal');
    }

    public function confirmReturn($chequeId)
    {
        $this->returningChequeId = $chequeId;
        $this->dispatch('openModal', 'confirm-return-modal');
    }

    public function returnChequeConfirmed()
    {
        if (!$this->returningChequeId) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'No cheque selected to return.'
            ]);
            return;
        }

        // Call existing returnCheque logic
        $chequeId = $this->returningChequeId;
        $this->returningChequeId = null;

        $this->returnCheque($chequeId);

        // Close confirmation modal
        $this->dispatch('closeModal', 'confirm-return-modal');
    }

    public function returnCheque($chequeId)
    {
        try {
            DB::beginTransaction();

            $cheque = Cheque::findOrFail($chequeId);

            // Ensure the cheque can be marked as returned
            if ($cheque->status !== 'pending' && !is_null($cheque->status)) {
                throw new Exception('This cheque cannot be marked as returned.');
            }

            $cheque->update([
                'status' => 'return',
            ]);

            // Create or update a return_cheques record to record the returned amount
            try {
                ReturnCheque::firstOrCreate(
                    ['cheque_id' => $cheque->id],
                    [
                        'customer_id' => $cheque->customer_id,
                        'cheque_amount' => $cheque->cheque_amount,
                        'balance_amount' => $cheque->cheque_amount,
                        'status' => 'pending',
                    ]
                );
            } catch (Exception $e) {
                // If return record creation fails, log but continue to allow transaction rollback by outer catch
                Log::error('Failed to create return_cheque record: ' . $e->getMessage());
            }

            // Add a note to the related sale if it exists
            if ($cheque->payment && $cheque->payment->sale) {
                $sale = $cheque->payment->sale;
                $sale->update([
                    'notes' => ($sale->notes ? $sale->notes . "\n" : '') .
                        "Cheque marked as returned on " . now()->format('Y-m-d H:i') . "."
                ]);
            }

            DB::commit();

            $this->computeStatistics();
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Cheque marked as returned successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to mark cheque as returned: ' . $e->getMessage()
            ]);
        }
    }

    public function viewCheque($chequeId)
    {
        try {
            $this->selectedCheque = Cheque::with(['customer', 'payment.sale'])->find($chequeId);
            if (!$this->selectedCheque) {
                $this->dispatch('showToast', [
                    'type' => 'error',
                    'message' => 'Cheque not found.'
                ]);
                return;
            }

            $this->isEditingCheque = false;
            $this->dispatch('openModal', 'chequeDetailsModal');
        } catch (Exception $e) {
            Log::error('Failed to load cheque for view: ' . $e->getMessage());
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to load cheque details.'
            ]);
        }
    }

    public function toggleEditCheque()
    {
        if (!$this->isEditingCheque && $this->selectedCheque) {
            $this->editChequeNumber = $this->selectedCheque->cheque_number;
            $this->editChequeDate = $this->selectedCheque->cheque_date?->format('Y-m-d') ?? '';
        }
        $this->isEditingCheque = !$this->isEditingCheque;
    }

    public function saveEditCheque()
    {
        $this->validate([
            'editChequeNumber' => 'required|string|max:50',
            'editChequeDate' => 'required|date',
        ]);

        try {
            DB::beginTransaction();

            if (!$this->selectedCheque) {
                throw new Exception('No cheque selected to edit.');
            }

            $this->selectedCheque->update([
                'cheque_number' => $this->editChequeNumber,
                'cheque_date' => $this->editChequeDate,
            ]);

            DB::commit();

            // Reload the cheque data
            $this->selectedCheque = Cheque::with(['customer', 'payment.sale'])->find($this->selectedCheque->id);
            $this->isEditingCheque = false;

            $this->computeStatistics();
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Cheque details updated successfully.'
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to save cheque edit: ' . $e->getMessage());
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to save cheque details: ' . $e->getMessage()
            ]);
        }
    }

    public function cancelEditCheque()
    {
        $this->isEditingCheque = false;
        $this->editChequeNumber = '';
        $this->editChequeDate = '';
    }

    public function render()
    {
        $baseQuery = Cheque::with(['customer', 'payment.sale'])
            ->where(function ($query) {
                // Include cheques with valid sales for current user
                $query->whereHas('payment.sale', function ($q) {
                    $q->where('user_id', auth()->id());
                })
                    // OR include all cheques with pending status (even if sale_id is null)
                    ->orWhere('status', 'pending');
            });

        $filteredQuery = clone $baseQuery;

        // Search filter
        if ($this->search) {
            $filteredQuery->where(function ($q) {
                $q->whereHas('customer', function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                })
                    ->orWhere('cheque_number', 'like', "%{$this->search}%")
                    ->orWhereHas('payment.sale', function ($q2) {
                        $q2->where('invoice_number', 'like', "%{$this->search}%");
                    });
            });
        }

        // Show cheques based on status filter
        if ($this->filters['status']) {
            if ($this->filters['status'] === 'null') {
                $filteredQuery->whereNull('status');
            } else {
                $filteredQuery->where('status', $this->filters['status']);
            }
        } else {
            // Show all statuses including pending, complete, return, and null
            $filteredQuery->whereIn('status', ['pending', 'complete', 'return'])
                ->orWhereNull('status');
        }

        // Date range filter
        if ($this->filters['dateRange']) {
            [$startDate, $endDate] = explode(' to ', $this->filters['dateRange']);
            $filteredQuery->whereBetween('cheque_date', [$startDate, $endDate]);
        }

        $duePayments = $filteredQuery
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderBy('cheque_date', 'asc')
            ->paginate(10);


        return view('livewire.admin.due-cheques', [
            'duePayments' => $duePayments,
            'pendingChequeCount' => $this->pendingChequeCount,
            'completeChequeCount' => $this->completeChequeCount,
            'returnChequeCount' => $this->returnChequeCount,
            'totalDueAmount' => $this->totalDueAmount,
        ]);
    }
}
