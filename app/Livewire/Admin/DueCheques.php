<?php

namespace App\Livewire\Admin;

use App\Models\Cheque;
use App\Models\Payment;
use App\Models\Sale;
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
            ->whereHas('payment.sale', function ($query) {
                $query->where('user_id', auth()->id());
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

            // Add a note to the related sale
            $sale = $cheque->payment->sale;
            $sale->update([
                'notes' => ($sale->notes ? $sale->notes . "\n" : '') .
                    "Cheque marked as complete on " . now()->format('Y-m-d H:i') . "."
            ]);

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

            // Add a note to the related sale
            $sale = $cheque->payment->sale;
            $sale->update([
                'notes' => ($sale->notes ? $sale->notes . "\n" : '') .
                    "Cheque marked as returned on " . now()->format('Y-m-d H:i') . "."
            ]);

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

    public function render()
    {
        $baseQuery = Cheque::with(['customer', 'payment.sale'])
            ->whereHas('payment.sale', function ($query) {
                $query->where('user_id', auth()->id());
            });

        $filteredQuery = clone $baseQuery;

        // Search filter
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

        // ❗️ Only include cheques with status 'pending'
        $filteredQuery->whereIn('status', ['pending','complete']);

        // Date range filter
        if ($this->filters['dateRange']) {
            [$startDate, $endDate] = explode(' to ', $this->filters['dateRange']);
            $filteredQuery->whereBetween('cheque_date', [$startDate, $endDate]);
        }

        $duePayments = $filteredQuery->orderBy('cheque_date', 'asc')->paginate(10);

        return view('livewire.admin.due-cheques', [
            'duePayments' => $duePayments,
            'pendingChequeCount' => $this->pendingChequeCount,
            'completeChequeCount' => $this->completeChequeCount,
            'returnChequeCount' => $this->returnChequeCount,
            'totalDueAmount' => $this->totalDueAmount,
        ]);
    }
}