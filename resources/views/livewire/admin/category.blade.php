<div class="container-fluid py-2">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
        <h1 class="h2 fw-bold text-uppercase tracking-tight" style="color: #233D7F;">Product Categories</h1>
        <div class="d-flex flex-column flex-md-row align-items-center gap-3 mt-3 mt-md-0">
            <div class="input-group" style="max-width: 300px;">
                <span class="input-group-text bg-white border-2 border-end-0" style="border-color: #233D7F;">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input
                    type="text"
                    wire:model.debounce.500ms="search"
                    placeholder="Search categories..."
                    class="form-control border-2 border-start-0 shadow-sm"
                    style="border-color: #233D7F; color: #233D7F;">
            </div>
            <button
                class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow w-100"
                wire:click="toggleAddModal"
                style="background-color: #00C8FF; border-color: #00C8FF; color: white;"
                onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';"
                onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">
                <i class="bi bi-plus-circle me-2"></i>Add Category
            </button>
        </div>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show mb-5 rounded-3 shadow-sm" role="alert" style="border-left: 5px solid #28a745; color: #233D7F; background: #e6f4ea;">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 text-success"></i>
            {{ session('message') }}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Categories Table -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="border-color: #233D7F;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background-color: #233D7F; color: white;">
                        <tr>
                            <th class="ps-4 py-3">ID</th>
                            <th class="py-3">Name</th>
                            <th class="py-3">Description</th>
                            <th class="py-3">Created At</th>
                            <th class="text-center py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody style="color: #233D7F;">
                        @forelse ($categories as $category)
                        <tr class="transition-all hover:bg-gray-50">
                            <td class="ps-4 py-3">{{ $category->id }}</td>
                            <td class="py-3">{{ $category->name }}</td>
                            <td class="py-3">{{ $category->description }}</td>
                            <td class="py-3">{{ $category->created_at->format('d/m/Y') }}</td>
                            <td class="text-center py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <button
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3 transition-all hover:shadow"
                                        wire:click="toggleEditModal({{ $category->id }})"
                                        style="border-color: #00C8FF; color: #00C8FF;"
                                        onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F'; this.style.color='white';"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='#00C8FF'; this.style.color='#00C8FF';"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3 transition-all hover:shadow"
                                        wire:click="toggleDeleteModal({{ $category->id }})"
                                        style="border-color: #EF4444; color: #EF4444;"
                                        onmouseover="this.style.backgroundColor='#EF4444'; this.style.borderColor='#EF4444'; this.style.color='white';"
                                        onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='#EF4444'; this.style.color='#EF4444';"
                                        title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4" style="color: #233D7F;">
                                <i class="bi bi-exclamation-circle me-2"></i>No categories found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3 mx-2">
                    {{ $categories->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Category Modal -->
    @if($showAddModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Add New Category</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showAddModal', false)" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body p-5">
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium" style="color: #233D7F;">Category Name</label>
                            <input type="text" wire:model="name" class="form-control border-2 shadow-sm" style="border-color: #233D7F; color: #233D7F;">
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium" style="color: #233D7F;">Description</label>
                            <textarea wire:model="description" class="form-control border-2 shadow-sm" rows="4" style="border-color: #233D7F; color: #233D7F;"></textarea>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer py-3 px-4" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showAddModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Save</button>
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
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showEditModal', false)" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body p-5">
                        <div class="mb-4">
                            <label for="edit-name" class="form-label fw-medium" style="color: #233D7F;">Category Name</label>
                            <input type="text" wire:model="name" class="form-control border-2 shadow-sm" style="border-color: #233D7F; color: #233D7F;">
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label for="edit-description" class="form-label fw-medium" style="color: #233D7F;">Description</label>
                            <textarea wire:model="description" class="form-control border-2 shadow-sm" rows="4" style="border-color: #233D7F; color: #233D7F;"></textarea>
                            @error('description') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer py-3 px-4" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showEditModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Update</button>
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
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #EF4444; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showDeleteModal', false)" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5" style="color: #233D7F;">
                    <p class="mb-0">Are you sure you want to delete this category? This action cannot be undone.</p>
                </div>
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showDeleteModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="delete" style="background-color: #EF4444; border-color: #EF4444; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#EF4444'; this.style.borderColor='#EF4444';">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif
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
</style>
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