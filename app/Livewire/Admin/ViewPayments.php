<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Payment;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;

#[Layout('components.layouts.admin')]
#[Title('View Payments')]
class ViewPayments extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $selectedPayment = null;
    public $filters = [
        'status' => '',
        'paymentMethod' => '',
        'dateFrom' => '',
        'dateTo' => '',
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

            // Log for debugging
            \Log::info('Selected Payment:', [
                'payment_id' => $paymentId,
                'sale_id' => $this->selectedPayment->sale_id,
                'sale_exists' => !is_null($this->selectedPayment->sale),
                'items_count' => $this->selectedPayment->sale ? $this->selectedPayment->sale->items->count() : 0,
                'invoice_number' => $this->selectedPayment->sale ? $this->selectedPayment->sale->invoice_number : 'N/A',
            ]);

            if (!$this->selectedPayment->sale) {
                $this->dispatch('showToast', [
                    'type' => 'warning',
                    'message' => 'No sale associated with this payment.'
                ]);
                return;
            }

            if ($this->selectedPayment->sale->items->isEmpty()) {
                \Log::warning('No items found for sale ID: ' . $this->selectedPayment->sale_id);
            }

            $this->dispatch('openModal', 'payment-receipt-modal');
        } catch (\Exception $e) {
            \Log::error('Error loading payment: ' . $e->getMessage());
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => 'Error loading payment: ' . $e->getMessage()
            ]);
        }
    }

    public function setDatePreset(string $preset): void
    {
        $today = Carbon::today();
        switch ($preset) {
            case 'today':
                $this->filters['dateFrom'] = $today->toDateString();
                $this->filters['dateTo'] = $today->toDateString();
                break;
            case 'this_week':
                $this->filters['dateFrom'] = $today->copy()->startOfWeek()->toDateString();
                $this->filters['dateTo'] = $today->copy()->endOfWeek()->toDateString();
                break;
            case 'this_month':
                $this->filters['dateFrom'] = $today->copy()->startOfMonth()->toDateString();
                $this->filters['dateTo'] = $today->copy()->endOfMonth()->toDateString();
                break;
            default:
                $this->filters['dateFrom'] = '';
                $this->filters['dateTo'] = '';
        }
        $this->filters['dateRange'] = '';
        $this->resetPage();
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
            ->where(function ($q) {
                $q->where('is_completed', true)
                  ->orWhereIn('status', ['Paid', 'paid', 'forward', 'current']);
            })
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
            ->when($this->filters['status'], function ($q) {
                $status = strtolower($this->filters['status']);
                if ($status === 'paid') {
                    $q->where(function ($qq) {
                        $qq->where('is_completed', true)
                           ->orWhereIn('status', ['Paid', 'paid']);
                    });
                } elseif ($status === 'current') {
                    $q->where(function ($qq) {
                        $qq->where('applied_to', 'current')
                           ->orWhere('status', 'current');
                    });
                } elseif ($status === 'forward') {
                    $q->where(function ($qq) {
                        $qq->where('applied_to', 'back_forward')
                           ->orWhere('status', 'forward');
                    });
                }
            })
            ->when($this->filters['paymentMethod'], function ($q) {
                $method = $this->filters['paymentMethod'];
                return $q->where(function ($qq) use ($method) {
                    $qq->where('payment_method', $method)
                       ->orWhere('due_payment_method', $method);
                });
            })
            ->when($this->filters['dateFrom'] || $this->filters['dateTo'], function ($q) {
                try {
                    $from = $this->filters['dateFrom'] ? Carbon::parse($this->filters['dateFrom'])->startOfDay() : null;
                    $to = $this->filters['dateTo'] ? Carbon::parse($this->filters['dateTo'])->endOfDay() : null;
                    if ($from && $to) {
                        $q->where(function ($qq) use ($from, $to) {
                            $qq->whereBetween('payment_date', [$from, $to])
                               ->orWhere(function ($qq2) use ($from, $to) {
                                   $qq2->whereNull('payment_date')
                                       ->whereBetween('due_date', [$from, $to]);
                               });
                        });
                    } elseif ($from) {
                        $q->where(function ($qq) use ($from) {
                            $qq->whereDate('payment_date', '>=', $from->toDateString())
                               ->orWhere(function ($qq2) use ($from) {
                                   $qq2->whereNull('payment_date')
                                       ->whereDate('due_date', '>=', $from->toDateString());
                               });
                        });
                    } elseif ($to) {
                        $q->where(function ($qq) use ($to) {
                            $qq->whereDate('payment_date', '<=', $to->toDateString())
                               ->orWhere(function ($qq2) use ($to) {
                                   $qq2->whereNull('payment_date')
                                       ->whereDate('due_date', '<=', $to->toDateString());
                               });
                        });
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Date filter parse error: ' . $e->getMessage());
                }
            })
            ->when($this->filters['dateRange'], function ($q) {
                $range = trim($this->filters['dateRange']);
                $dates = preg_split('/\s*-\s*/', $range);
                try {
                    if (count($dates) === 2) {
                        $from = Carbon::parse($dates[0])->startOfDay();
                        $to = Carbon::parse($dates[1])->endOfDay();
                        $q->where(function ($qq) use ($from, $to) {
                            $qq->whereBetween('payment_date', [$from, $to])
                               ->orWhere(function ($qq2) use ($from, $to) {
                                   $qq2->whereNull('payment_date')
                                       ->whereBetween('due_date', [$from, $to]);
                               });
                        });
                    } else {
                        $on = Carbon::parse($range);
                        $q->where(function ($qq) use ($on) {
                            $qq->whereDate('payment_date', $on->toDateString())
                               ->orWhere(function ($qq2) use ($on) {
                                   $qq2->whereNull('payment_date')
                                       ->whereDate('due_date', $on->toDateString());
                               });
                        });
                    }
                } catch (\Throwable $e) {
                    \Log::warning('Date range parse error: ' . $e->getMessage());
                }
            });

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        $totalPayments = Payment::where('is_completed', 1)->sum('amount');
        $pendingPayments = Payment::where('is_completed', 0)->sum('amount');
        $todayTotalPayments = Payment::whereDate('created_at', now()->toDateString())->where('is_completed', 1)->sum('amount');
        $todayPendingPayments = Payment::whereDate('created_at', now()->toDateString())->where('is_completed', 0)->sum('amount');
        $totalPayCount = Payment::where('is_completed', 1)->count();
        $todayPayCount = Payment::whereDate('created_at', now()->toDateString())->where('is_completed', 1)->count();

        return view('livewire.admin.view-payments', [
            'payments' => $payments,
            'totalPayments' => $totalPayments,
            'pendingPayments' => $pendingPayments,
            'todayTotalPayments' => $todayTotalPayments,
            'todayPendingPayments' => $todayPendingPayments,
            'totalPayCount' => $totalPayCount,
            'todayPayCount' => $todayPayCount
        ]);
    }
}