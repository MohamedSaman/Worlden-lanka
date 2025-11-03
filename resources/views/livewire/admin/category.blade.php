<div class="container-fluid py-4">
    <!-- Success Message -->
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 shadow-sm" role="alert" style="border-left: 5px solid #28a745; color: #155724; background: #d4edda;">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>
            {{ session('message') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Page Header -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-header-modern mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-collection"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Product Category Details</h3>
                        <p class="mb-0">Monitor and manage your Product Category Details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card animate-slide-in">
                <!-- Search & Actions Bar -->
                <div class="card-body border-bottom" style="background-color: #f8f9fa;">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-7">
                            <div class="search-box-modern">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="bi bi-search text-primary-custom"></i>
                                    </span>
                                    <input type="text"
                                        class="form-control"
                                        placeholder="Search category..."
                                        wire:model.live.debounce.300ms="search"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-5 text-lg-end">
                            <button class="btn-modern btn-primary-modern" wire:click="toggleAddModal">
                                <i class="bi bi-plus-circle me-2"></i>Add Category
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table Content -->
                <div class="table-modern">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                            <tr>
                                <td>
                                    <span class="fw-bold text-primary-custom">#{{ $category->id }}</span>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ $category->name }}</div>
                                </td>
                                <td>
                                    <div class="small text-secondary">{{ $category->description }}</div>
                                </td>
                                <td>
                                    <div class="small">{{ $category->created_at->format('d M Y') }}</div>
                                </td>
                                <td class="text-center">
                                    <button wire:click="toggleEditModal({{ $category->id }})"
                                        class="btn-action btn-edit me-1"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button wire:click="toggleDeleteModal({{ $category->id }})"
                                        class="btn-action btn-delete"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox display-6"></i>
                                        <p class="mt-2 mb-0">No categories found</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($categories->hasPages())
                <div class="card-footer bg-white border-top p-4">
                    <div class="pagination-modern">
                        {{ $categories->links('livewire::bootstrap') }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    @if($showAddModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #9d1c20; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Add New Category</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showAddModal', false)" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body p-5">
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium" style="color: #9d1c20;">Category Name</label>
                            <input type="text" wire:model="name" class="form-control border-2 shadow-sm" style="border-color: #d34d51ff; color: #9d1c20;">
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium" style="color: #9d1c20;">Description</label>
                            <textarea wire:model="description" class="form-control border-2 shadow-sm" rows="4" style="border-color: #d34d51ff; color: #9d1c20;"></textarea>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer py-3 px-4" style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showAddModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Edit Category Modal -->
    @if($showEditModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #9d1c20; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showEditModal', false)" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body p-5">
                        <div class="mb-4">
                            <label for="edit-name" class="form-label fw-medium" style="color: #9d1c20;">Category Name</label>
                            <input type="text" wire:model="name" class="form-control border-2 shadow-sm" style="border-color: #d34d51ff; color: #9d1c20;">
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit-description" class="form-label fw-medium" style="color: #9d1c20;">Description</label>
                            <textarea wire:model="description" class="form-control border-2 shadow-sm" rows="4" style="border-color: #d34d51ff; color: #9d1c20;"></textarea>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer py-3 px-4" style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showEditModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #EF4444; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showDeleteModal', false)" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5" style="color: #9d1c20;">
                    <p class="mb-0">Are you sure you want to delete this category? This action cannot be undone.</p>
                </div>
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showDeleteModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="delete" style="background-color: #EF4444; border-color: #EF4444; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#EF4444'; this.style.borderColor='#EF4444';">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
@include('components.admin-styles')
@endpush

@push('script')
<!-- Include Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(function() {
                alert.classList.add('show');
            }, 100);
        }
    });
</script>
@endpush