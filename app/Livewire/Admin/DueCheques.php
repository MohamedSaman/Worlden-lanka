<?php

namespace App\Livewire\Admin;

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

#[Title("Due Cheque Payments")]
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
    public $pendingChequeCount;
    public $awaitingApprovalCount;
    public $returnChequeCount;
    public $totalDueAmount;
    public $pendingChequeCounts;

    protected $listeners = ['refreshPayments' => '$refresh'];

    public function mount()
    {
        $this->computeStatistics();
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['search', 'filters.status', 'filters.dateRange'])) {
            $this->computeStatistics();
        }
    }

    public function computeStatistics()
    {
        // Base query for cheque payments
        $baseQuery = Payment::query()
            ->where('is_completed', false)
            ->where('due_payment_method', 'cheque')
            ->whereHas('sale', function ($query) {
                $query->where('user_id', auth()->id());
            });

        // Apply filters to a new query for rendering
        $filteredQuery = Payment::query()
            ->where('is_completed', false)
            ->where('due_payment_method', 'cheque')
            ->whereHas('sale', function ($query) {
                $query->where('user_id', auth()->id());
            });

        if ($this->search) {
            $filteredQuery->whereHas('sale', function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($q2) {
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
            $filteredQuery->whereBetween('due_date', [$startDate, $endDate]);
        }

        
         // Debug logging
        
        Log::info('Cheque Statistics', [
            'pendingChequeCount' => $this->pendingChequeCount,
            'awaitingApprovalCount' => $this->awaitingApprovalCount,
            'returnChequeCount' => $this->returnChequeCount,
            'totalDueAmount' => $this->totalDueAmount,
            'filters' => [
                'search' => $this->search,
                'status' => $this->filters['status'],
                'dateRange' => $this->filters['dateRange'],
            ],
        ]);
    }

    public function updatedDuePaymentAttachment()
    {
        $this->validate([
            'duePaymentAttachment' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ]);

        if ($this->duePaymentAttachment) {
            $previewInfo = $this->getFilePreviewInfo($this->duePaymentAttachment);
            $this->duePaymentAttachmentPreview = $previewInfo;
        }
    }

    public function getPaymentDetails($paymentId)
    {
        $this->paymentId = $paymentId;
        $this->paymentDetail = Payment::with(['sale.customer', 'sale.items'])->find($paymentId);
        $this->duePaymentMethod = 'cheque'; // Fixed to cheque
        $this->paymentNote = '';
        $this->duePaymentAttachment = null;
        $this->duePaymentAttachmentPreview = null;
        $this->receivedAmount = '';

        $this->dispatch('openModal', 'payment-detail-modal');
    }

    public function submitPayment()
    {
        $this->validate([
            'receivedAmount' => 'required|numeric|min:0.01',
            'duePaymentMethod' => 'required|in:cheque',
            'duePaymentAttachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($this->paymentId);

            $attachmentPath = $payment->due_payment_attachment;
            if ($this->duePaymentAttachment) {
                $receiptName = time() . '-payment-' . $payment->id . '.' . $this->duePaymentAttachment->getClientOriginalExtension();
                $this->duePaymentAttachment->storeAs('public/due-receipts', $receiptName);
                $attachmentPath = "due-receipts/{$receiptName}";
            }

            $receivedAmount = floatval($this->receivedAmount);
            $remainingAmount = $payment->amount - $receivedAmount;

            if ($receivedAmount > $payment->amount) {
                DB::rollBack();
                $this->dispatch('showToast', [
                    'type' => 'error',
                    'message' => 'Entered amount is too large. Please enter an amount less than or equal to the due amount.'
                ]);
                return;
            }

            $payment->update([
                'amount' => $receivedAmount,
                'due_payment_method' => $this->duePaymentMethod,
                'due_payment_attachment' => $attachmentPath,
                'status' => 'pending',
                'payment_date' => now(),
            ]);

            if ($this->paymentNote) {
                $payment->sale->update([
                    'notes' => ($payment->sale->notes ? $payment->sale->notes . "\n" : '') .
                        "Cheque payment received on " . now()->format('Y-m-d H:i') . ": " . $this->paymentNote
                ]);
            }

            if ($remainingAmount > 0.01) {
                Payment::create([
                    'sale_id' => $payment->sale_id,
                    'amount' => $remainingAmount,
                    'due_date' => $payment->due_date,
                    'due_payment_method' => 'cheque',
                    'status' => null,
                    'is_completed' => false,
                ]);
            }

            DB::commit();

            $this->computeStatistics();
            $this->dispatch('closeModal', 'payment-detail-modal');
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Cheque payment submitted successfully and sent for admin approval'
            ]);

            $this->reset(['paymentDetail', 'duePaymentMethod', 'duePaymentAttachment', 'paymentNote', 'receivedAmount']);

        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to submit payment: ' . $e->getMessage()
            ]);
        }
    }

    public function openExtendDueModal($paymentId)
    {
        $this->extendDuePaymentId = $paymentId;
        $payment = Payment::findOrFail($paymentId);
        $this->newDueDate = $payment->due_date->addDays(7)->format('Y-m-d');
        $this->extensionReason = '';
        $this->dispatch('openModal', 'extend-due-modal');
    }

    public function returnCheque($paymentId)
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($paymentId);

            // Ensure the payment is a cheque and can be marked as returned
            if ($payment->due_payment_method !== 'cheque' || !in_array($payment->status, [null, 'pending'])) {
                throw new Exception('This payment cannot be marked as returned.');
            }

            $payment->update([
                'status' => 'rejected',
            ]);

            // Add a note to the sale
            $payment->sale->update([
                'notes' => ($payment->sale->notes ? $payment->sale->notes . "\n" : '') .
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

    public function extendDueDate()
    {
        $this->validate([
            'newDueDate' => 'required|date|after:today',
            'extensionReason' => 'required|min:5',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($this->extendDuePaymentId);
            $oldDueDate = $payment->due_date->format('Y-m-d');

            $payment->update([
                'due_date' => $this->newDueDate,
            ]);

            $payment->sale->update([
                'notes' => ($payment->sale->notes ? $payment->sale->notes . "\n" : '') .
                    "Cheque due date extended on " . now()->format('Y-m-d H:i') . " from {$oldDueDate} to {$this->newDueDate}. Reason: {$this->extensionReason}"
            ]);

            DB::commit();

            $this->computeStatistics();
            $this->dispatch('closeModal', 'extend-due-modal');
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Cheque due date extended successfully'
            ]);

            $this->reset(['extendDuePaymentId', 'newDueDate', 'extensionReason']);

        } catch (Exception $e) {
            DB::rollBack();
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to extend due date: ' . $e->getMessage()
            ]);
        }
    }

    private function getFilePreviewInfo($file)
    {
        if (!$file) {
            return null;
        }

        $result = [
            'name' => $file->getClientOriginalName(),
            'type' => 'unknown',
            'icon' => 'bi-file-earmark',
            'color' => 'text-secondary',
            'preview' => null
        ];

        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $result['type'] = 'image';
            $result['icon'] = 'bi-file-earmark-image';
            $result['color'] = 'text-primary';
            try {
                $result['preview'] = $file->temporaryUrl();
            } catch (\Exception $e) {
                $result['preview'] = null;
            }
        } elseif ($extension === 'pdf') {
            $result['type'] = 'pdf';
            $result['icon'] = 'bi-file-earmark-pdf';
            $result['color'] = 'text-danger';
        }

        return $result;
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->filters = ['status' => '', 'dateRange' => ''];
        $this->computeStatistics();
    }

    public function render()
    {
        $query = Payment::query()
            ->where('is_completed', false)
            ->where('due_payment_method', 'cheque')
            ->whereNull('status')
            ->whereHas('sale', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['sale.customer']);

        if ($this->search) {
            $query->whereHas('sale', function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($q2) {
                      $q2->where('name', 'like', "%{$this->search}%")
                         ->orWhere('phone', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filters['status'] === 'null') {
            $query->whereNull('status');
        } elseif ($this->filters['status']) {
            $query->where('status', $this->filters['status']);
        }

        if ($this->filters['dateRange']) {
            [$startDate, $endDate] = explode(' to ', $this->filters['dateRange']);
            $query->whereBetween('due_date', [$startDate, $endDate]);
        }

        $duePayments = $query->orderBy('due_date', 'asc')->paginate(10);

        return view('livewire.admin.due-cheques', [
            'duePayments' => $duePayments,
        ]);
    }
}