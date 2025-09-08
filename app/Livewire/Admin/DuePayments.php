<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\Cheque;
use Exception;

#[Title("Due Payments")]
#[Layout('components.layouts.admin')]
class DuePayments extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $selectedPayment = null;
    public $paymentDetail = null;
    public $duePaymentAttachment;
    public $paymentId;
    public $duePaymentMethod = '';
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

    protected $listeners = ['refreshPayments' => '$refresh'];

    public function mount() {}

    public function updatedDuePaymentAttachment()
    {
        $this->validate([
            'duePaymentAttachment' => 'file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ]);

        if ($this->duePaymentAttachment) {
            $this->duePaymentAttachmentPreview = $this->getFilePreviewInfo($this->duePaymentAttachment);
        }
    }

    public function getPaymentDetails($paymentId)
    {
        $this->paymentId = $paymentId;
        $this->paymentDetail = Payment::with(['sale.customer', 'sale.items'])->find($paymentId);
        $this->duePaymentMethod = $this->paymentDetail->due_payment_method ?? '';
        $this->paymentNote = '';
        $this->duePaymentAttachment = null;
        $this->duePaymentAttachmentPreview = null;

        $this->dispatch('openModal', 'payment-detail-modal');
    }

    public function submitPayment()
    {
        $this->validate([
            'receivedAmount' => 'required|numeric|min:0.01',
            'duePaymentMethod' => 'required',
            'duePaymentAttachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($this->paymentId);

            // Store attachment if provided
            $attachmentPath = $payment->due_payment_attachment;
            if ($this->duePaymentAttachment) {
                $receiptName = time() . '-payment-' . $payment->id . '.' . $this->duePaymentAttachment->getClientOriginalExtension();
                $this->duePaymentAttachment->storeAs('public/due-receipts', $receiptName);
                $attachmentPath = "due-receipts/{$receiptName}";
            }
            $receivedAmount = floatval($this->receivedAmount);
            $remainingAmount = $payment->amount - $receivedAmount;
            if ($payment->amount >= $receivedAmount) {
                $payment->update([
                    'amount' => $receivedAmount,
                    'due_payment_method' => $this->duePaymentMethod,
                    'due_payment_attachment' => $attachmentPath,
                    'status' => 'pending',  // Change status to pending for admin approval
                    'payment_date' => now(),
                ]);

                // If payment method is cheque, add to Cheque table
                if ($this->duePaymentMethod === 'cheque') {
                    Cheque::create([
                        'cheque_number' => $this->paymentNote ?? '', // You may want to add cheque number input
                        'cheque_date'   => now(),
                        'bank_name'     => '', // Add bank name input if needed
                        'cheque_amount' => $receivedAmount,
                        'status'        => 'pending',
                        'customer_id'   => $payment->sale->customer_id,
                        'payment_id'    => $payment->id,
                    ]);
                }

            } else {
                DB::rollBack();
                $this->dispatch('showToast', [
                    'type' => 'error',
                    'message' => 'Entered amount is too large. Please enter an amount less than or equal to the due amount.'
                ]);
                return;
            }

            // Add a note to track this payment submission
            if ($this->paymentNote) {
                $payment->sale->update([
                    'notes' => ($payment->sale->notes ? $payment->sale->notes . "\n" : '') .
                        "Payment received on " . now()->format('Y-m-d H:i') . ": " . $this->paymentNote
                ]);
            }

            // If there is a remaining amount, create a new Payment record with status null
            if ($remainingAmount > 0.01) {
                Payment::create([
                    'sale_id' => $payment->sale_id,
                    'amount' => $remainingAmount,
                    'due_date' => $payment->due_date,
                    'status' => null,
                    'is_completed' => false,
                ]);
            }

            DB::commit();

            $this->dispatch('closeModal', 'payment-detail-modal');
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Payment submitted successfully and sent for admin approval'
            ]);

            $this->reset(['paymentDetail', 'duePaymentMethod', 'duePaymentAttachment', 'paymentNote', 'receivedAmount']);
        } catch (Exception $e) {
            DB::rollback();
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

    public function extendDueDate()
    {
        $this->validate([
            'extensionReason' => 'required|min:5',
            'newDueDate' => 'required|date|after_or_equal:' . date('Y-m-d'),
        ]);

        try {
            $payment = Payment::findOrFail($this->extendDuePaymentId);
            $oldDueDate = $payment->due_date->format('Y-m-d');
            $payment->update([
                'due_date' => $this->newDueDate,
            ]);
            $payment->sale->update([
                'notes' => ($payment->sale->notes ? $payment->sale->notes . "\n" : '') .
                    "Due date extended from {$oldDueDate} to {$this->newDueDate}: {$this->extensionReason}"
            ]);
            $this->dispatch('closeModal', 'extend-due-modal');
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Due date extended successfully'
            ]);
            $this->reset(['extendDuePaymentId', 'newDueDate', 'extensionReason']);
        } catch (Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to extend due date: ' . $e->getMessage()
            ]);
        }
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

    public function resetFilters()
    {
        $this->filters = [
            'status' => '',
            'dateRange' => '',
        ];
    }

    public function printDuePayments()
    {
        $this->dispatch('print-due-payments');
    }

    public function render()
    {
        $allPayments = Payment::where(function ($query) {
            if ($this->search) {
                $query->whereHas('sale.customer', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                })
                ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            }
            if ($this->filters['status'] !== '') {
                if ($this->filters['status'] === 'null') {
                    $query->whereNull('status');
                } else {
                    $query->where('status', $this->filters['status']);
                }
            }
            // Add date range filter if needed
        })->get();

        $duePayments = Payment::where(function ($query) {
            if ($this->search) {
                $query->whereHas('sale.customer', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                })
                ->orWhere('invoice_number', 'like', '%' . $this->search . '%');
            }
            if ($this->filters['status'] !== '') {
                if ($this->filters['status'] === 'null') {
                    $query->whereNull('status');
                } else {
                    $query->where('status', $this->filters['status']);
                }
            }
            // Add date range filter if needed
        })
        ->orderBy('due_date')
        ->paginate(10);

        $duePaymentsCount = $allPayments->where('status', null)->count();
        $awaitingApprovalCount = $allPayments->where('status', 'pending')->count();
        $overdueCount = $allPayments->where(function ($q) {
            $q->whereNull('status')->where('due_date', '<', now());
        })->count();
        $totalDue = $allPayments->sum('amount');

        // Add status badge for each payment
        foreach ($duePayments as $payment) {
            if ($payment->status === null) {
                $payment->status_badge = '<span class="badge bg-info">Pending</span>';
            } elseif ($payment->status === 'pending') {
                $payment->status_badge = '<span class="badge bg-warning">Awaiting Approval</span>';
            } elseif ($payment->status === 'approved') {
                $payment->status_badge = '<span class="badge bg-success">Approved</span>';
            } elseif ($payment->status === 'rejected') {
                $payment->status_badge = '<span class="badge bg-danger">Rejected</span>';
            } else {
                $payment->status_badge = '<span class="badge bg-secondary">Unknown</span>';
            }
        }

        return view('livewire.admin.due-payments', [
            'duePayments' => $duePayments,
            'duePaymentsCount' => $duePaymentsCount,
            'awaitingApprovalCount' => $awaitingApprovalCount,
            'overdueCount' => $overdueCount,
            'totalDue' => $totalDue,
        ]);
    }
}