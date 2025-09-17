<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Payment;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Support\Facades\Storage;

#[Layout('components.layouts.admin')]
#[Title('View Payments')]
class ViewPayments extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedPayment = null;
    public $filters = [
        'status' => '',
        'paymentMethod' => '',
        'dateRange' => '',
    ];

    public function viewPaymentDetails($paymentId)
    {
        try {
            $this->selectedPayment = Payment::with([
                'sale',
                'sale.customer',
                'sale.user',
                'sale.items',
                'sale.items.product'
            ])->findOrFail($paymentId);

            $this->dispatch('openModal', 'payment-receipt-modal');
        } catch (\Exception $e) {
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Error loading payment: ' . $e->getMessage()
            ]);
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset('filters');
    }

    public function render()
    {
        $query = Payment::query()
            ->with(['sale', 'sale.customer', 'sale.user'])
            ->where('status', 'Paid') // Only Paid status
            ->when($this->search, function ($q) {
                $search = $this->search;
                $q->where(function ($mainQuery) use ($search) {
                    $mainQuery->whereHas('sale', function ($sq) use ($search) {
                        $sq->where('invoice_number', 'like', "%{$search}%");
                    })
                        ->orWhereHas('sale.customer', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%")
                                ->orWhere('phone', 'like', "%{$search}%");
                        });
                });
            })
            ->when($this->filters['paymentMethod'], function ($q) {
                return $q->where('payment_method', $this->filters['paymentMethod']);
            });

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);


        // Get summary stats
        $totalPayments = Payment::where('is_completed', 1)->sum('amount');
        $pendingPayments = Payment::where('is_completed', 0)->sum('amount');
        $todayTotalPayments = Payment::whereDate('created_at', now()->toDateString())->where('is_completed', 1)->sum('amount');
        $todayPendingPayments = Payment::whereDate('created_at', now()->toDateString())->where('is_completed', 0)->sum('amount');

        return view('livewire.admin.view-payments', [
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'pendingPayments' => $pendingPayments,
            'todayTotalPayments' => $todayTotalPayments,
            'todayPendingPayments' => $todayPendingPayments
        ]);
    }
}
