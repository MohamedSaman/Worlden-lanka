<div class="container-fluid py-2">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="border-color: #233D7F;">
        <div class="card-header bg-white py-3 px-4">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                <h4 class="card-title mb-0 fw-bold text-uppercase tracking-tight" style="color: #233D7F;">Supplier List</h4>
                <div class="card-tools">
                    <button class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="createSupplier" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">
                        <i class="bi bi-plus-circle me-2"></i>Create Supplier
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #233D7F; color: white;">
                        <tr>
                            <th class="text-center ps-4 py-3">#</th>
                            <th class="text-center py-3">Supplier Name</th>
                            <th class="text-center py-3">Contact Number</th>
                            <th class="text-center py-3">Email</th>
                            <th class="text-center py-3">Address</th>
                            <th class="text-center py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody style="color: #233D7F;">
                        @if ($suppliers->count() > 0)
                            @foreach ($suppliers as $supplier)
                            <tr class="transition-all hover:bg-gray-50">
                                <td class="text-center align-middle ps-4 py-3">{{ ($suppliers->currentPage() - 1) * $suppliers->perPage() + $loop->iteration }}</td>
                                <td class="text-center align-middle py-3">{{ $supplier->name ?? '-' }}</td>
                                <td class="text-center align-middle py-3">{{ $supplier->contact ?? '-' }}</td>
                                <td class="text-center align-middle py-3">{{ $supplier->email ?? '-' }}</td>
                                <td class="text-center align-middle py-3">{{ $supplier->address ?? '-' }}</td>
                                <td class="text-center py-3">
                                    <div class="btn-group btn-group-sm gap-2" role="group">
                                        <button class="btn btn-outline-primary rounded-pill px-3 transition-all hover:shadow" wire:click="editSupplier({{ $supplier->id }})" wire:loading.attr="disabled" style="border-color: #00C8FF; color: #00C8FF;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='#00C8FF'; this.style.color='#00C8FF';" title="Edit">
                                            <i class="bi bi-pencil" wire:loading.class="d-none" wire:target="editSupplier({{ $supplier->id }})"></i>
                                            <span wire:loading wire:target="editSupplier({{ $supplier->id }})">
                                                <i class="spinner-border spinner-border-sm"></i>
                                            </span>
                                        </button>
                                        <button class="btn btn-outline-danger rounded-pill px-3 transition-all hover:shadow" wire:click="confirmDelete({{ $supplier->id }})" style="border-color: #EF4444; color: #EF4444;" onmouseover="this.style.backgroundColor='#EF4444'; this.style.borderColor='#EF4444'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='#EF4444'; this.style.color='#EF4444';" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="text-center py-4" style="color: #233D7F;">
                                    <i class="bi bi-exclamation-circle me-2"></i>No suppliers found.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Supplier Modal --}}
    <div wire:ignore.self wire:key="create-supplier-modal" class="modal fade" id="createSupplierModal" tabindex="-1" aria-labelledby="createSupplierModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h1 class="modal-title fs-5 fw-bold tracking-tight" id="createSupplierModalLabel">Add New Supplier</h1>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" wire:click="resetForm" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <label for="supplierName" class="form-label fw-medium" style="color: #233D7F;">Supplier Name</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="supplierName" wire:model.defer="name" placeholder="Enter supplier name" style="border-color: #233D7F; color: #233D7F;">
                            @error('name')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contactNumber" class="form-label fw-medium" style="color: #233D7F;">Contact Number</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="contactNumber" wire:model.defer="contactNumber" placeholder="Enter contact number" style="border-color: #233D7F; color: #233D7F;">
                            @error('contactNumber')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-medium" style="color: #233D7F;">Email</label>
                            <input type="email" class="form-control border-2 shadow-sm" id="email" wire:model.defer="email" placeholder="Enter email" style="border-color: #233D7F; color: #233D7F;">
                            @error('email')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="address" class="form-label fw-medium" style="color: #233D7F;">Address</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="address" wire:model.defer="address" placeholder="Enter address" style="border-color: #233D7F; color: #233D7F;">
                            @error('address')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4 flex-column flex-sm-row gap-2" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow  w-sm-auto" wire:click="resetForm" data-bs-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow  w-sm-auto" wire:click="saveSupplier" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Add Supplier</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Supplier Modal --}}
    <div wire:ignore.self wire:key="edit-supplier-modal-{{ $editSupplierId ?? 'new' }}" class="modal fade" id="editSupplierModal" tabindex="-1" aria-labelledby="editSupplierModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h1 class="modal-title fs-5 fw-bold tracking-tight" id="editSupplierModalLabel">Edit Supplier</h1>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" wire:click="resetForm" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <label for="editName" class="form-label fw-medium" style="color: #233D7F;">Supplier Name</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="editName" wire:model="editName" placeholder="Enter supplier name" style="border-color: #233D7F; color: #233D7F;">
                            @error('editName')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editContactNumber" class="form-label fw-medium" style="color: #233D7F;">Contact Number</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="editContactNumber" wire:model="editContactNumber" placeholder="Enter contact number" style="border-color: #233D7F; color: #233D7F;">
                            @error('editContactNumber')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editEmail" class="form-label fw-medium" style="color: #233D7F;">Email</label>
                            <input type="email" class="form-control border-2 shadow-sm" id="editEmail" wire:model="editEmail" placeholder="Enter email" style="border-color: #233D7F; color: #233D7F;">
                            @error('editEmail')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editAddress" class="form-label fw-medium" style="color: #233D7F;">Address</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="editAddress" wire:model="editAddress" placeholder="Enter address" style="border-color: #233D7F; color: #233D7F;">
                            @error('editAddress')
                                <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4 flex-column flex-sm-row gap-2" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow  w-sm-auto" data-bs-dismiss="modal" wire:click="resetForm" style="background-color: #6B7280; border-color: #6B7280; color: white;">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow  w-sm-auto" wire:click="updateSupplier({{ $editSupplierId }})" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Update Supplier</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
        .btn-group-sm > .btn, .btn-sm {
            padding: 0.25rem 0.4rem;
        }
        .btn-group {
            display: flex;
            gap: 0.25rem;
        }
        .table td:nth-child(4), .table th:nth-child(4), /* Email */
        .table td:nth-child(5), .table th:nth-child(5) { /* Address */
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
    window.addEventListener('confirm-delete', event => {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#00C8FF",
            cancelButtonColor: "#EF4444",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('confirmDelete');
                Swal.fire({
                    title: "Deleted!",
                    text: "Supplier has been deleted.",
                    icon: "success"
                });
            }
        });
    });

    window.addEventListener('create-supplier', event => {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('createSupplierModal'));
            modal.show();
        }, 100);
    });

    window.addEventListener('edit-supplier', event => {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('editSupplierModal'));
            modal.show();
        }, 100);
    });

    document.addEventListener('DOMContentLoaded', function() {
        const createModal = document.getElementById('createSupplierModal');
        const editModal = document.getElementById('editSupplierModal');
        if (createModal) {
            createModal.addEventListener('hidden.bs.modal', function () {
                Livewire.dispatch('resetForm');
            });
        }
        if (editModal) {
            editModal.addEventListener('hidden.bs.modal', function () {
                Livewire.dispatch('resetForm');
            });
        }
    });
</script>
@endpush

