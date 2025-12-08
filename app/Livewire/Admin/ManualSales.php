<?php

namespace App\Livewire\Admin;

use App\Models\ManualSale;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[Title("Manual Sales")]
#[Layout('components.layouts.admin')]
class ManualSales extends Component
{
    use WithPagination;

    public $search = '';
    public $startDate = '';
    public $endDate = '';
    public $paymentStatus = '';
    public $selectedSale = null;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->startDate = date('Y-m-d', strtotime('-30 days'));
        $this->endDate = date('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function viewSale($saleId)
    {
        $this->selectedSale = ManualSale::with(['customer', 'items', 'payments'])->find($saleId);
        $this->dispatch('open-sale-modal');
    }

    public function deleteSale($saleId)
    {
        try {
            $sale = ManualSale::find($saleId);
            if ($sale) {
                $sale->delete();
                $this->dispatch('swal', [
                    'icon' => 'success',
                    'title' => 'Deleted',
                    'text' => 'Manual sale deleted successfully'
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Error',
                'text' => 'Failed to delete sale: ' . $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $query = ManualSale::with(['customer', 'user'])
            ->orderBy('created_at', 'desc');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('customer', function ($customerQuery) {
                        $customerQuery->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('phone', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->startDate) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->paymentStatus) {
            $query->where('payment_status', $this->paymentStatus);
        }

        $sales = $query->paginate(15);

        return view('livewire.admin.manual-sales', [
            'sales' => $sales,
        ]);
    }
}
