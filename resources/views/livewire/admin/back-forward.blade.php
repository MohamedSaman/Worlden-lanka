<!-- File: resources/views/livewire/admin/back-forward.blade.php -->
<div class="container-fluid py-2">
    <div class="card border-0">
        <!-- Header -->
        <div class="card-header text-white p-2 d-flex align-items-center"
            style="background: linear-gradient(90deg, #9d1c20 0%, #d34d51ff 100%); border-radius: 20px 20px 0 0;">
            <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center me-3">
                <i class="bi bi-currency-exchange fs-4 text-white" aria-hidden="true"></i>
            </div>
            <div>
                <h3 class="mb-1 fw-bold tracking-tight text-white">Back-Forward Management</h3>
                <p class="text-white opacity-80 mb-0 text-sm">Monitor and adjust customer back-forward amounts</p>
            </div>
        </div>
        <div class="card-header bg-transparent pb-4 mt-2 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">
            <!-- Search Bar -->
            <div class="flex-grow-1 d-flex justify-content-lg-center">
                <div class="input-group" style="max-width: 600px;">
                    <span class="input-group-text bg-gray-100 border-0 px-3">
                        <i class="bi bi-search text-danger"></i>
                    </span>
                    <input type="text"
                        class="form-control"
                        placeholder="Search customers..."
                        wire:model.live.debounce.300ms="search"
                        autocomplete="off">
                </div>
            </div>
        </div>

        <div class="card-body p-1 pt-5 bg-transparent">
            <div class="table-responsive shadow-sm rounded-2 overflow-auto">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th class="text-center ps-4 py-3">No</th>
                            <th class=" py-3">Customer Name</th>
                            <th class="text-center py-3">Contact Number</th>
                            <th class="text-center py-3">Total Back-Forward Amount</th>
                            <th class="text-center py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($customers->count() > 0)
                            @foreach ($customers as $customer)
                                <tr class="transition-all hover:bg-gray-50">
                                    <td class="text-sm text-center ps-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="text-sm py-3">{{ $customer->name ?? '-' }}</td>
                                    <td class="text-sm text-center py-3">{{ $customer->phone ?? 'N/A' }}</td>
                                    <td class="text-sm text-center py-3 font-bold {{ $customer->customer_accounts_sum_back_forward_amount >= 0 ? 'text-success' : 'text-danger' }}">
                                        {{ number_format($customer->customer_accounts_sum_back_forward_amount ?? 0, 2) }}
                                    </td>
                                    <td class="text-sm text-center">
                                        <div class="btn-group btn-group-sm gap-2" role="group">
                                            <button class="btn text-primary bg-outline-primary border border-1 border-primary rounded-pill px-3" wire:click="adjustBackForward({{ $customer->id }})" wire:loading.attr="disabled" title="Add Amount">
                                                <i class="bi bi-plus" wire:loading.class="d-none" wire:target="adjustBackForward({{ $customer->id }})">Add</i>
                                                <span wire:loading wire:target="adjustBackForward({{ $customer->id }})">
                                                    <i class="spinner-border spinner-border-sm"></i>
                                                </span>
                                            </button>
                                            <button class="btn text-warning bg-outline-warning border border-1 border-warning rounded-pill px-3" wire:click="editBackForward({{ $customer->id }})" wire:loading.attr="disabled" title="Edit Amount">
                                                <i class="bi bi-pencil" wire:loading.class="d-none" wire:target="editBackForward({{ $customer->id }})">Edit</i>
                                                <span wire:loading wire:target="editBackForward({{ $customer->id }})">
                                                    <i class="spinner-border spinner-border-sm"></i>
                                                </span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center py-4" style="color: #9d1c20;">
                                    <i class="bi bi-exclamation-circle me-2"></i>No customers found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end mt-4">
                {{ $customers->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    <!-- Adjust Back-Forward Modal -->
    <div wire:ignore.self class="modal fade" id="adjustBackForwardModal" tabindex="-1" aria-labelledby="adjustBackForwardModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #9d1c20; color: white;">
                    <h1 class="modal-title fs-5 fw-bold tracking-tight" id="adjustBackForwardModalLabel">{{ $isEditing ? 'Edit Back-Forward Amount' : 'Adjust Back-Forward Amount' }}</h1>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-12">
                            <label for="adjustmentAmount" class="form-label fw-medium" style="color: #9d1c20;">Adjustment Amount <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control border-2 shadow-sm" id="adjustmentAmount" wire:model="adjustmentAmount" style="color: #9d1c20;" placeholder="Enter positive for advance/forward, negative for back/due">
                            @error('adjustmentAmount')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12">
                            <label for="adjustmentNotes" class="form-label fw-medium" style="color: #9d1c20;">Notes (Optional)</label>
                            <textarea class="form-control border-2 shadow-sm" id="adjustmentNotes" wire:model="adjustmentNotes" rows="3" style="color: #9d1c20;" placeholder="Add notes for this adjustment"></textarea>
                            @error('adjustmentNotes')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4 flex-column flex-sm-row gap-2" style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow w-sm-auto" data-bs-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow w-sm-auto" wire:click="saveAdjustment" style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">{{ $isEditing ? 'Update' : 'Save Adjustment' }}</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .input-group {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .btn {
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: scale(1.05);
    }

    .tracking-tight {
        letter-spacing: -0.025em;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    .hover\:bg-gray-50:hover {
        background-color: #f8f9fa;
    }

    .hover\:shadow:hover {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    @media (max-width: 767.98px) {
        .table {
            font-size: 0.875rem;
        }

        .btn-group-sm>.btn,
        .btn-sm {
            padding: 0.25rem 0.4rem;
        }

        .btn-group {
            display: flex;
            gap: 0.25rem;
        }

        .table td:nth-child(4),
        .table th:nth-child(4),
        /* Contact Number */
        .table td:nth-child(5),
        .table th:nth-child(5) {
            /* Email */
            display: none;
        }
    }

    @media (max-width: 575.98px) {
        .modal-footer {
            justify-content: center;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    window.addEventListener('open-adjust-modal', event => {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('adjustBackForwardModal'));
            modal.show();
        }, 100);
    });

    window.addEventListener('hide-adjust-modal', event => {
        const modal = bootstrap.Modal.getInstance(document.getElementById('adjustBackForwardModal'));
        if (modal) {
            modal.hide();
        }
    });
</script>
@endpush