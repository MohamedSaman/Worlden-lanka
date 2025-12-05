<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-header-modern mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-gear"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Settings</h3>
                        <p class="mb-0">Manage system configuration and settings</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card animate-slide-in">
                <div class="card-header" style="background-color: #f8f9fa; border-bottom: 2px solid #9d1c20;">
                    <ul class="nav nav-tabs card-header-tabs" id="settingsTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="quantity-tab" data-bs-toggle="tab"
                                data-bs-target="#quantity-pane" type="button" role="tab"
                                aria-controls="quantity-pane" aria-selected="true">
                                <i class="bi bi-box-seam me-2"></i>Quantity Types
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body">
                    <!-- Quantity Types Tab -->
                    <div class="tab-content" id="settingsTabContent">
                        <div class="tab-pane fade show active" id="quantity-pane" role="tabpanel"
                            aria-labelledby="quantity-tab">

                            <!-- Add New Quantity Type Form -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <div class="card border-1 shadow-sm" style="border-color: #9d1c20;">
                                        <div class="card-header" style="background-color: #9d1c20; color: white;">
                                            <h5 class="mb-0">
                                                <i class="bi bi-plus-circle me-2"></i>Add New Quantity Type
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <form wire:submit.prevent="addQuantityType">
                                                <div class="row g-3">
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-semibold">
                                                            Quantity Type Name <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text"
                                                            class="form-control form-control-sm rounded-3 @error('newQuantityTypeName') is-invalid @enderror"
                                                            wire:model.lazy="newQuantityTypeName"
                                                            placeholder="e.g., Pieces, Box, Pack"
                                                            autocomplete="off">
                                                        @error('newQuantityTypeName')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">
                                                            Code <span class="text-danger">*</span>
                                                        </label>
                                                        <input type="text"
                                                            class="form-control form-control-sm rounded-3 @error('newQuantityTypeCode') is-invalid @enderror"
                                                            wire:model.lazy="newQuantityTypeCode"
                                                            placeholder="e.g., pcs, box, pack"
                                                            autocomplete="off">
                                                        @error('newQuantityTypeCode')
                                                        <div class="invalid-feedback d-block">{{ $message }}</div>
                                                        @enderror
                                                    </div>

                                                    <div class="col-md-3">
                                                        <label class="form-label fw-semibold">Description</label>
                                                        <input type="text"
                                                            class="form-control form-control-sm rounded-3"
                                                            wire:model.lazy="newQuantityTypeDescription"
                                                            placeholder="Optional description"
                                                            autocomplete="off">
                                                    </div>

                                                    <div class="col-md-2 d-flex align-items-end">
                                                        <button type="submit"
                                                            class="btn btn-success rounded-3 w-100"
                                                            wire:loading.attr="disabled">
                                                            <span wire:loading.remove>
                                                                <i class="bi bi-plus me-1"></i>Add Type
                                                            </span>
                                                            <span wire:loading>
                                                                <span class="spinner-border spinner-border-sm me-2"
                                                                    role="status" aria-hidden="true"></span>Adding...
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quantity Types List -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-0">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">
                                                <i class="bi bi-list-ul me-2"></i>Quantity Types List
                                            </h5>
                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-hover mb-0">
                                                <thead style="background-color: #f8f9fa;">
                                                    <tr>
                                                        <th class="text-uppercase text-xs fw-semibold"
                                                            style="color: #9d1c20;">ID</th>
                                                        <th class="text-uppercase text-xs fw-semibold"
                                                            style="color: #9d1c20;">Name</th>
                                                        <th class="text-uppercase text-xs fw-semibold"
                                                            style="color: #9d1c20;">Code</th>
                                                        <th class="text-uppercase text-xs fw-semibold"
                                                            style="color: #9d1c20;">Description</th>
                                                        <th class="text-uppercase text-xs fw-semibold text-center"
                                                            style="color: #9d1c20;">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($quantityTypes as $type)
                                                    <tr>
                                                        <td>
                                                            <span class="badge badge-primary">{{ $type['id'] }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="fw-semibold">{{ $type['name'] }}</span>
                                                        </td>
                                                        <td>
                                                            <code class="bg-light px-2 py-1 rounded">{{ strtoupper($type['code']) }}</code>
                                                        </td>
                                                        <td>
                                                            <small class="text-muted">{{ $type['description'] ?? '-' }}</small>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button"
                                                                class="btn btn-sm btn-warning rounded-2 me-2"
                                                                wire:click="startEditQuantityType({{ $type['id'] }})"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#editQuantityTypeModal"
                                                                title="Edit">
                                                                <i class="bi bi-pencil-square"></i>
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-sm btn-danger rounded-2"
                                                                wire:click="deleteQuantityType({{ $type['id'] }})"
                                                                wire:confirm="Are you sure you want to delete this quantity type?"
                                                                title="Delete">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-5">
                                                            <div class="text-muted">
                                                                <i class="bi bi-inbox fs-1"></i>
                                                                <p class="mt-2">No quantity types added yet</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Quantity Type Modal -->
    <div wire:ignore.self class="modal fade" id="editQuantityTypeModal" tabindex="-1"
        aria-labelledby="editQuantityTypeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header" style="background-color: #9d1c20; color: white;">
                    <h5 class="modal-title fw-bold" id="editQuantityTypeModalLabel">
                        <i class="bi bi-pencil-square me-2"></i>Edit Quantity Type
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    @if ($editingQuantityTypeId)
                    <form wire:submit.prevent="updateQuantityType">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Quantity Type Name <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-sm rounded-3 @error('editQuantityTypeName') is-invalid @enderror"
                                wire:model.lazy="editQuantityTypeName"
                                placeholder="e.g., Pieces">
                            @error('editQuantityTypeName')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Code <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control form-control-sm rounded-3 @error('editQuantityTypeCode') is-invalid @enderror"
                                wire:model.lazy="editQuantityTypeCode"
                                placeholder="e.g., pcs">
                            @error('editQuantityTypeCode')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control form-control-sm rounded-3" rows="3"
                                wire:model.lazy="editQuantityTypeDescription"
                                placeholder="Optional description"></textarea>
                        </div>
                    </form>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light rounded-pill px-4"
                        wire:click="cancelEdit" data-bs-dismiss="modal">
                        <i class="bi bi-x me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4"
                        wire:click="updateQuantityType" data-bs-dismiss="modal"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove>
                            <i class="bi bi-check-circle me-1"></i>Save Changes
                        </span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm me-2" role="status"
                                aria-hidden="true"></span>Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

@push('styles')
@include('components.admin-styles')
<style>
    .card-header-modern {
        background: linear-gradient(135deg, #9d1c20 0%, #d34d51 100%);
        border-radius: 12px;
        color: white;
    }

    .nav-tabs .nav-link {
        color: #495057;
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s ease;
    }

    .nav-tabs .nav-link:hover {
        border-bottom-color: #9d1c20;
        color: #9d1c20;
    }

    .nav-tabs .nav-link.active {
        border-bottom-color: #9d1c20;
        color: #9d1c20;
        background: none;
    }

    .badge-primary {
        background-color: #9d1c20;
    }

    .btn-success {
        background-color: #22c55e;
        border-color: #22c55e;
    }

    .btn-success:hover {
        background-color: #16a34a;
        border-color: #16a34a;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        window.addEventListener('show-toast', event => {
            const data = event.detail[0];
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: data.type,
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });

        @this.on('showToast', (data) => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: data.type,
                title: data.message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        });
    });
</script>
@endpush