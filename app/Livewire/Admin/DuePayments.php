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

    // Cheque input fields and list
    public $chequeNumber = '';
    public $bankName = '';
    public $chequeAmount = '';
    public $chequeDate = '';
    public $cheques = [];

    public $duePayment;
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
        $this->receivedAmount = '';
        $this->chequeNumber = '';
        $this->bankName = '';
        $this->chequeAmount = '';
        $this->chequeDate = '';
        $this->cheques = [];

        $this->dispatch('openModal', 'payment-detail-modal');
    }

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

            $cashAmount = floatval($this->receivedAmount) ?: 0;
            $chequeTotal = collect($this->cheques)->sum('amount');
            $totalPaid = $cashAmount + $chequeTotal;

            if ($totalPaid <= 0) {
                DB::rollBack();
                $this->dispatch('showToast', [
                    'type' => 'error',
                    'message' => 'Please enter a cash amount, add cheque(s), or both.'
                ]);
                return;
            }

            if ($totalPaid > $payment->amount) {
                DB::rollBack();
                $this->dispatch('showToast', [
                    'type' => 'error',
                    'message' => 'Total payment exceeds due amount.'
                ]);
                return;
            }
            // Update payment record
            $payment->update([
                'amount' => $totalPaid,
                'due_payment_method' => $cashAmount > 0 && $chequeTotal > 0 ? 'cash+cheque' : ($chequeTotal > 0 ? 'cheque' : 'cash'),
                'due_payment_attachment' => $attachmentPath,
                'status' => 'Paid',
                'is_completed' => true, 
                'payment_date' => now(),
            ]);

            // Save cheques if any
            foreach ($this->cheques as $cheque) {
                Cheque::create([
                    'cheque_number' => $cheque['number'],
                    'cheque_date'   => $cheque['date'],
                    'bank_name'     => $cheque['bank'],
                    'cheque_amount' => $cheque['amount'],
                    'status'        => 'pending',
                    'customer_id'   => $payment->sale->customer_id,
                    'payment_id'    => $payment->id,
                ]);
            }

            // Add a note to track this payment submission
            if ($this->paymentNote) {
                $payment->sale->update([
                    'notes' => ($payment->sale->notes ? $payment->sale->notes . "\n" : '') .
                        "Payment received on " . now()->format('Y-m-d H:i') . ": " . $this->paymentNote
                ]);
            }
            $sale = $payment->sale; // using the relationship you already used above
            $remainingAmount = $sale->due_amount - $totalPaid;

            $sale->update([
                'due_amount' => $remainingAmount
            ]);
            // dd($sale->due_amount);
            // dd($remainingAmount);
            if ($remainingAmount > 0) {
                Payment::create([
                    'sale_id' => $payment->sale_id,
                    'amount' => $remainingAmount,
                    'due_date' => $payment->due_date,
                    'status' => null,
                    'is_completed' => false,
                ]);
            }

            // dd($remainingAmount);

            DB::commit();

            $this->dispatch('closeModal', 'payment-detail-modal');
            $this->dispatch('showToast', [
                'type' => 'success',
                'message' => 'Payment submitted successfully and sent for admin approval'
            ]);

            $this->reset([
                'paymentDetail',
                'duePaymentMethod',
                'duePaymentAttachment',
                'paymentNote',
                'receivedAmount',
                'chequeNumber',
                'bankName',
                'chequeAmount',
                'chequeDate',
                'cheques'
            ]);
        } catch (Exception $e) {
            DB::rollback();
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Failed to submit payment: ' . $e->getMessage()
            ]);
        }
    }

    // public function openExtendDueModal($paymentId)
    // {
    //     $this->extendDuePaymentId = $paymentId;
    //     $payment = Payment::findOrFail($paymentId);

    //     $this->newDueDate = $payment->due_date->addDays(7)->format('Y-m-d');
    //     $this->extensionReason = '';

    //     $this->dispatch('openModal', 'extend-due-modal');
    // }

    // public function extendDueDate()
    // {
    //     $this->validate([
    //         'extensionReason' => 'required|min:5',
    //         'newDueDate' => 'required|date|after_or_equal:' . date('Y-m-d'),
    //     ]);

    //     try {
    //         $payment = Payment::findOrFail($this->extendDuePaymentId);
    //         $oldDueDate = $payment->due_date->format('Y-m-d');
    //         $payment->update([
    //             'due_date' => $this->newDueDate,
    //         ]);
    //         $payment->sale->update([
    //             'notes' => ($payment->sale->notes ? $payment->sale->notes . "\n" : '') .
    //                 "Due date extended from {$oldDueDate} to {$this->newDueDate}: {$this->extensionReason}"
    //         ]);
    //         $this->dispatch('closeModal', 'extend-due-modal');
    //         $this->dispatch('showToast', [
    //             'type' => 'success',
    //             'message' => 'Due date extended successfully'
    //         ]);
    //         $this->reset(['extendDuePaymentId', 'newDueDate', 'extensionReason']);
    //     } catch (Exception $e) {
    //         $this->dispatch('showToast', [
    //             'type' => 'error',
    //             'message' => 'Failed to extend due date: ' . $e->getMessage()
    //         ]);
    //     }
    // }

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
                $query->whereHas('sale', function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', function ($c) {
                            $c->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                        });
                });
            }

            if ($this->filters['status'] !== '') {
                if ($this->filters['status'] === 'null') {
                    $query->whereNull('status');
                } else {
                    $query->where('status', $this->filters['status']);
                }
            }
        })->get();


        $duePayments = Payment::where(function ($query) {
            if ($this->search) {
                $query->whereHas('sale', function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                        ->orWhereHas('customer', function ($c) {
                            $c->where('name', 'like', '%' . $this->search . '%')
                                ->orWhere('phone', 'like', '%' . $this->search . '%');
                        });
                });
            }

            if ($this->filters['status'] !== '') {
                if ($this->filters['status'] === 'null') {
                    $query->whereNull('status');
                } else {
                    $query->where('status', $this->filters['status']);
                }
            }
        })
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        $duePaymentsCount = $allPayments->where('status', null)->count();
        $totalDue = Payment::whereNull('status')->sum('amount');
        $todayDuePayments = Payment::whereNull('status')->whereDate('created_at', today())->sum('amount');
        $todayDuePaymentsCount = Payment::whereNull('status')->whereDate('created_at', today())->count();

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
            'todayDuePayments' => $todayDuePayments,
            'todayDuePaymentsCount' => $todayDuePaymentsCount,
            'totalDue' => $totalDue,
            'cheques' => $this->cheques,
            'chequeNumber' => $this->chequeNumber,
            'bankName' => $this->bankName,
            'chequeAmount' => $this->chequeAmount,
            'chequeDate' => $this->chequeDate,
        ]);
    }
}
