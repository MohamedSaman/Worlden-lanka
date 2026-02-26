<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card card-header-modern mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper me-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <h3 class="mb-1">Product Details</h3>
                        <p class="mb-0">Monitor and manage your Product Details</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card animate-slide-in">
                <!-- Search & Actions -->
                <div class="card-body border-bottom" style="background-color: #f8f9fa;">
                    <div class="row g-3 align-items-center">
                        <div class="col-lg-6">
                            <div class="search-box-modern">
                                <i class="bi bi-search"></i>
                                <input type="text"
                                    class="form-control"
                                    placeholder="Search products..."
                                    wire:model.live.debounce.300ms="search"
                                    autocomplete="off">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="d-flex gap-2 justify-content-lg-end">
                                <button class="btn btn-modern btn-primary-modern"
                                    wire:click="toggleAddModal">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    <span class="d-none d-sm-inline">Add </span>Product
                                </button>
                                <button class="btn btn-modern btn-info-modern"
                                    wire:click="toggleImportModal">
                                    <i class="bi bi-upload me-1"></i>
                                    <span class="d-none d-sm-inline">Import </span>Excel
                                </button>
                                <button wire:click="exportToCSV"
                                    class="btn btn-modern btn-secondary-modern">
                                    <i class="bi bi-download me-1"></i>
                                    <span class="d-none d-sm-inline">Export </span>CSV
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Custom Field Section -->
                <div class="card-body border-bottom d-flex justify-content-between align-items-center" style="background-color: #fafbfc;">
                    <p class="mb-0 text-muted">
                        You can create custom field here
                        <i class="bi bi-arrow-right ms-2"></i>
                    </p>
                    <button
                        class="btn btn-modern btn-outline-modern"
                        wire:click="$set('showAddFieldModal', true)">
                        <i class="bi bi-plus-circle me-1"></i>Add Field
                    </button>
                </div>

                <!-- Table -->
                <div class="table-modern overflow-auto">
                    <table class="table table-hover mb-0 ">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3">ID</th>
                                <th class="py-3">Product Code</th>
                                <th class="py-3">Product Name</th>
                                <th class="py-3">Category</th>
                              
                                <th class="py-3">Selling Price</th>

                                <th class="py-3">Quantity Inhand</th>
                                <th class="py-3">Sold</th>
                                <th class="py-3 text-center">Stock</th>

                                @foreach ($fieldKeys as $key)
                                <th class="text-center py-3 ">{{ $key }}</th>
                                @endforeach
                                <th class="text-center py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($products as $product)
                            <tr class="transition-all hover:bg-gray-50">
                                <td class="ps-4 py-3">{{ $product->id }}</td>
                                <td class="py-3">{{ $product->product_code }}</td>
                                <td class="py-3">{{ $product->product_name }}</td>
                                <td class="py-3">{{ $product->category->name ?? 'N/A' }}</td>
                                
                                <td class="py-3">Rs. {{ number_format($product->selling_price, 2) }}</td>
                                <td class="py-3 text-center">{{ $product->stock_quantity + $product->damage_quantity }}</td>
                                <td class="py-3 text-center">{{ $product->sold }}</td>
                                <td class="py-3 text-center">
                                    @if($product->stock_quantity > 0)
                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill">In Stock</span>
                                    @else
                                    <span class="badge bg-danger text-white px-3 py-2 rounded-pill">Out of Stock</span>
                                    @endif
                                </td>

                                @foreach ($fieldKeys as $key)
                                <td class="text-center py-3">{{ $product->customer_field[$key] ?? '-' }}</td>
                                @endforeach
                                <td class="text-center py-3">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button
                                            class="btn btn-sm "
                                            wire:click="viewProduct({{ $product->id }})"
                                            style="color: #233D7F;"
                                            title="View">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm "
                                            wire:click="editProduct({{ $product->id }})"
                                            style="color: #00C8FF;"
                                            title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm "
                                            wire:click="confirmDelete({{ $product->id }})"
                                            style=" color: #EF4444;"
                                            title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="{{ 9 + count($fieldKeys) }}" class="text-center py-4" style="color: #d34d51ff;">
                                    <i class="bi bi-exclamation-circle me-2"></i>No products found.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $products->links('livewire::bootstrap') }}
            </div>

            <!-- Add Product Modal -->
            @if ($showAddModal)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
                <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 900px;">
                    <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                        <div class="modal-header py-3 px-4" style="background-color: #9d1c20; color: white;">
                            <h5 class="modal-title fw-bold tracking-tight">Add New Product</h5>
                            <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="toggleAddModal" aria-label="Close"></button>
                        </div>
                        <form wire:submit.prevent="save">
                            <div class="modal-body p-5">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label fw-medium" style="color: #9d1c20;">Category</label>
                                        <select
                                            id="category_id"
                                            wire:model="category_id"
                                            class="form-select border-2 shadow-sm"
                                            style=" color: #9d1c20;">
                                            <option value="">Select a category</option>
                                            @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="product_name" class="form-label fw-medium" style="color: #9d1c20;">Product Name</label>
                                        <input
                                            type="text"
                                            id="product_name"
                                            wire:model="product_name"
                                            class="form-control border-2 shadow-sm"
                                            style=" color: #9d1c20;">
                                        @error('product_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="supplier_price" class="form-label fw-medium" style="color: #9d1c20;">Supplier Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-2 border-end-0">Rs.</span>
                                            <input
                                                type="number"
                                                id="supplier_price"
                                                wire:model="supplier_price"
                                                class="form-control border-2 shadow-sm"
                                                style=" color: #9d1c20;"
                                                step="0.01">
                                        </div>
                                        @error('supplier_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="selling_price" class="form-label fw-medium" style="color: #9d1c20;">Selling Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white border-2 border-end-0">Rs.</span>
                                            <input
                                                type="number"
                                                id="selling_price"
                                                wire:model="selling_price"
                                                class="form-control border-2 shadow-sm"
                                                style=" color: #9d1c20;"
                                                step="0.01">
                                        </div>
                                        @error('selling_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="stock_quantity" class="form-label fw-medium" style="color: #9d1c20;">Total Quantity</label>
                                        <input
                                            type="number"
                                            id="stock_quantity"
                                            wire:model="stock_quantity"
                                            class="form-control border-2 shadow-sm"
                                            style=" color: #9d1c20;"
                                            min="0" step="1">
                                        @error('stock_quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="damage_quantity" class="form-label fw-medium" style="color: #9d1c20;">Damage Quantity</label>
                                        <input
                                            type="number"
                                            id="damage_quantity"
                                            wire:model="damage_quantity"
                                            class="form-control border-2 shadow-sm"
                                            style=" color: #9d1c20;"
                                            min="0" step="1">
                                        @error('damage_quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>

                                <div class="mt-2">
                                    <h6 class="fw-bold text-uppercase text-muted">Customer Fields</h6>
                                    <div class="row g-4">
                                        @foreach ($fieldKeys as $key)
                                        <div class="col-md-6 mb-3">
                                            <label for="customer_field_{{ $key }}" class="form-label fw-medium" style="color: #9d1c20;">{{ $key }}</label>
                                            <input
                                                type="text"
                                                id="customer_field_{{ $key }}"
                                                wire:model="customer_fields.{{ $loop->index }}.value"
                                                class="form-control border-2 shadow-sm"
                                                style="color: #9d1c20;"
                                                placeholder="Enter {{ $key }}">
                                            @error('customer_fields.' . $loop->index . '.value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer py-3 px-4 d-flex justify-content-end gap-3" style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="toggleAddModal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">Save Product</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            @endif

            <!-- Edit Product Modal -->
            @if ($showEditModal)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                        <div class="modal-header py-3 px-4" style="background-color: #9d1c20; color: white;">
                            <h5 class="modal-title fw-bold tracking-tight">Edit Product</h5>
                            <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="toggleEditModal" aria-label="Close"></button>
                        </div>
                        <form wire:submit.prevent="update">
                            <div class="modal-body p-5">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label for="edit_category_id" class="form-label fw-medium" style="color: #9d1c20;">Category</label>
                                        <select
                                            id="edit_category_id"
                                            wire:model="category_id"
                                            class="form-select border-2 shadow-sm"
                                            style=" color: #9d1c20;">
                                            <option value="">Select a category</option>
                                            @foreach ($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edit_product_name" class="form-label fw-medium" style="color: #9d1c20;">Product Name</label>
                                        <input
                                            type="text"
                                            id="edit_product_name"
                                            wire:model="product_name"
                                            class="form-control border-2 shadow-sm"
                                            style=" color: #9d1c20;">
                                        @error('product_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="edit_supplier_price" class="form-label fw-medium" style="color: #9d1c20;">Supplier Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-2 border-end-0" style="">Rs.</span>
                                                <input
                                                    type="number"
                                                    id="edit_supplier_price"
                                                    wire:model="supplier_price"
                                                    class="form-control border-2 shadow-sm"
                                                    style=" color: #9d1c20;"
                                                    step="0.01">
                                            </div>
                                            @error('supplier_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="edit_selling_price" class="form-label fw-medium" style="color: #9d1c20;">Selling Price</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-2 border-end-0" style="">Rs.</span>
                                                <input
                                                    type="number"
                                                    id="edit_selling_price"
                                                    wire:model="selling_price"
                                                    class="form-control border-2 shadow-sm"
                                                    style=" color: #9d1c20;"
                                                    step="0.01">
                                            </div>
                                            @error('selling_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="edit_stock_quantity" class="form-label fw-medium" style="color: #9d1c20;">Stock Quantity</label>
                                            <input
                                                type="number"
                                                id="edit_stock_quantity"
                                                wire:model="stock_quantity"
                                                class="form-control border-2 shadow-sm"
                                                style=" color: #9d1c20;"
                                                min="0" step="1">
                                            @error('stock_quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-4">
                                            <label for="edit_damage_quantity" class="form-label fw-medium" style="color: #9d1c20;">Damage Quantity</label>
                                            <input
                                                type="number"
                                                id="edit_damage_quantity"
                                                wire:model="damage_quantity"
                                                class="form-control border-2 shadow-sm"
                                                style=" color: #9d1c20;"
                                                min="0" step="1">
                                            @error('damage_quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #9d1c20;">Sold Quantity</label>
                                <input
                                    type="number"
                                    id="edit_sold_quantity"
                                    wire:model="sold"
                                    class="form-control border-2 shadow-sm"
                                    style=" color: #9d1c20;"
                                    min="0" step="1" readonly>
                                @error('sold') <div class="text-danger small mt-1">{{ $message }}
                                </div> @enderror
                            </div> --}}
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-medium" style="color: #233D7F;">Status</label>
                                <select
                                    id="status"
                                    wire:model="status"
                                    class="form-select border-2 shadow-sm"
                                    style=" color: #9d1c20;">
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                                @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                    </div>


                    <!-- customer Fields -->
                    <div class="mb-4">
                        <label class="form-label fw-medium" style="color: #9d1c20;">Customer Fields</label>
                        <div class="row g-3">
                            @foreach ($customer_fields as $index => $field)
                            @php
                            $labelKey = ucwords(strtolower($field['key']));
                            @endphp
                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #9d1c20;">{{ $labelKey }}</label>
                                <input
                                    type="text"
                                    placeholder="Enter {{ $labelKey }}"
                                    wire:model="customer_fields.{{ $index }}.value"
                                    class="form-control border-2 shadow-sm"
                                    style=" color: #9d1c20;">
                                @error('customer_fields.' . $index . '.value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            @endforeach
                        </div>
                    </div>

                </div>
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="toggleEditModal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">Update Product</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #9d1c20; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="toggleDeleteModal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5" style="color: #9d1c20;">
                    <p class="mb-0">Are you sure you want to delete the product "<strong>{{ $deletingProductName }}</strong>"? This action cannot be undone.</p>
                </div>
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="toggleDeleteModal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                    <button type="button" class="btn btn-danger rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="delete" style="background-color: #EF4444; border-color: #EF4444; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#EF4444'; this.style.borderColor='#EF4444';">Delete</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Add Field Modal -->
    @if ($showAddFieldModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">

                <!-- Modal Header -->
                <div class="modal-header py-3 px-4" style="background-color: #9d1c20; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Add New Fields</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showAddFieldModal', false)" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-5">
                    <!-- Input for new fields -->
                    <input wire:model="newFieldKey" class="form-control border-2 shadow-sm mb-3" placeholder="Enter field names" style=" color: #9d1c20; " />
                    @error('newFieldKey') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                    <!-- Display all fields (existing + newly added) -->
                    @if (!empty($fieldKeys))
                    <div class="mt-4">
                        <h6 class="fw-bold mb-2">Fields:</h6>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($fieldKeys as $field)
                            <span class="badge bg-primary d-flex align-items-center px-3 py-2 rounded-pill">
                                {{ $field }}
                                <button type="button" wire:click.prevent="manageField('delete', '{{ $field }}')"
                                    class="btn btn-sm btn-danger ms-4 rounded-circle p-0"
                                    style="width: 20px; height: 20px; font-size: 12px; line-height: 1;">
                                    &times;
                                </button>
                            </span>

                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                    <button class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showAddFieldModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                    <button class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click.prevent="manageField('add')" style="background-color: #d34d51ff; border-color: #d34d51ff; color: white;" onmouseover="this.style.backgroundColor='#9d1c20'; this.style.borderColor='#9d1c20';" onmouseout="this.style.backgroundColor='#d34d51ff'; this.style.borderColor='#d34d51ff';">Add</button>
                </div>
            </div>
        </div>
    </div>
    @endif


    <!-- Delete Field Modal -->
    @if ($showDeleteFieldModal)
    <div class="modal fade show d-block"
        tabindex="-1"
        style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);"
        x-data="{ isOpen: true }"
        x-show="isOpen"
        @keydown.escape="$wire.set('showDeleteFieldModal', false)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden"
                style="border: 2px solid #9d1c20; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4"
                    style="background-color: #9d1c20; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Delete Field</h5>
                    <button type="button"
                        class="btn-close btn-close-white opacity-75 hover:opacity-100"
                        wire:click="$set('showDeleteFieldModal', false)"
                        @click="isOpen = false"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <label for="deleteFieldKey"
                        class="form-label fw-medium"
                        style="color: #9d1c20;">Select a field to delete</label>
                    <select wire:model.live="deleteFieldKey"
                        id="deleteFieldKey"
                        class="form-select border-2 shadow-sm"
                        style="border-color: #d34d51ff; color: #9d1c20;"
                        wire:loading.attr="disabled">
                        <option value="">Select Field</option>
                        @foreach ($fieldKeys as $key)
                        <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>
                    @error('deleteFieldKey') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer py-3 px-4"
                    style="border-top: 1px solid #9d1c20; background: #f8f9fa;">
                    <button class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow"
                        wire:click="$set('showDeleteFieldModal', false)"
                        @click="isOpen = false"
                        style="background-color: #6B7280; border-color: #6B7280; color: white;"
                        wire:loading.attr="disabled">
                        Cancel
                    </button>
                    <button class="btn btn-danger rounded-pill px-4 fw-medium transition-all hover:shadow"
                        wire:click="deleteField"
                        style="background-color: #DC3545; border-color: #DC3545; color: white;"
                        onmouseover="this.style.backgroundColor='#A71D2A'; this.style.borderColor='#A71D2A';"
                        onmouseout="this.style.backgroundColor='#DC3545'; this.style.borderColor='#DC3545';"
                        wire:loading.attr="disabled">
                        <span wire:loading wire:target="deleteField">
                            <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                            Deleting...
                        </span>
                        <span wire:loading.remove wire:target="deleteField">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- View Product Modal -->
    @if ($showViewModal)
    <div wire:ignore.self
        class="modal fade show d-block"
        id="viewProductModal"
        tabindex="-1"
        aria-labelledby="viewProductModalLabel"
        aria-hidden="true"
        style="background-color: rgba(0, 0, 0, 0.65); backdrop-filter: blur(8px);">

        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl border-0 overflow-hidden"
                style="background: #ffffff;">

                <!-- Header -->
                <div class="modal-header py-4 px-5 border-0"
                    style="background: linear-gradient(135deg, #9d1c20, #d34d51ff); color: white;">
                    <h4 class="modal-title fw-bold d-flex align-items-center mb-0" id="viewProductModalLabel">
                        <i class="bi bi-box-seam me-2"></i>
                        Product Details – {{ $this->viewProductName ?? 'N/A' }}
                    </h4>
                    <button type="button"
                        class="btn-close btn-close-white"
                        wire:click="$set('showViewModal', false)"
                        aria-label="Close"></button>
                </div>

                <!-- Body -->
                <div class="modal-body px-5 py-4">
                    <div class="row g-4">

                        <!-- Left: Product Image & Status -->
                        <div class="col-md-5">
                            <div class="card border-0 shadow-sm rounded-4 p-3 h-100 text-center">
                                @php
                                $product = \App\Models\ProductDetail::find(
                                $this->viewProductCode
                                ? \App\Models\ProductDetail::where('product_code', $this->viewProductCode)->first()->id
                                : null
                                );
                                @endphp

                                @if ($product && $product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                    alt="{{ $this->viewProductName ?? 'Product Image' }}"
                                    class="img-fluid rounded-3 mb-3 shadow-sm"
                                    style="max-height: 300px; object-fit: cover; width: 100%;">
                                @else
                                <div class="bg-light d-flex flex-column align-items-center justify-content-center rounded-3 mb-3 shadow-sm"
                                    style="height: 300px;">
                                    <i class="bi bi-image text-muted" style="font-size: 3.5rem;"></i>
                                    <p class="text-muted mt-2">No image available</p>
                                </div>
                                @endif

                                <div class="mt-2">
                                    <span class="fw-semibold text-secondary me-2">
                                        <i class="bi bi-info-circle"></i> Status:
                                    </span>
                                    <span class="badge fs-6 px-3 py-2 rounded-pill 
                                    {{ $this->viewStatus == 'Available' ? 'bg-success' : 
                                       ($this->viewStatus == 'Low Stock' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                        {{ $this->viewStatus ?? 'N/A' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Product Details -->
                        <div class="col-md-7">
                            <div class="card border-0 shadow-sm rounded-4 p-4 h-100">
                                <h5 class="fw-bold mb-4 text-primary">Product Information</h5>
                                <div class="row g-3">

                                    <div class="col-12">
                                        <span class="fw-medium text-secondary"><i class="bi bi-tag-fill text-primary me-2"></i>Category:</span>
                                        <span class="text-dark">{{ $this->viewCategoryName ?? 'N/A' }}</span>
                                    </div>

                                    <div class="col-12">
                                        <span class="fw-medium text-secondary"><i class="bi bi-box-seam text-primary me-2"></i>Name:</span>
                                        <span class="text-dark">{{ $this->viewProductName ?? 'N/A' }}</span>
                                    </div>

                                    <div class="col-12">
                                        <span class="fw-medium text-secondary"><i class="bi bi-upc text-primary me-2"></i>Code:</span>
                                        <span class="badge rounded-pill bg-light text-dark px-3 py-2">
                                            {{ $this->viewProductCode ?? 'N/A' }}
                                        </span>
                                    </div>

                                    <div class="col-6">
                                        <span class="fw-medium text-secondary"><i class="bi bi-currency-dollar text-primary me-2"></i>Supplier Price:</span>
                                        <span class="fw-semibold text-dark">Rs.{{ number_format($this->viewSupplierPrice ?? 0, 2) }}</span>
                                    </div>

                                    <div class="col-6">
                                        <span class="fw-medium text-secondary"><i class="bi bi-wallet2 text-primary me-2"></i>Selling Price:</span>
                                        <span class="fw-semibold text-dark">Rs.{{ number_format($this->viewSellingPrice ?? 0, 2) }}</span>
                                    </div>

                                    <div class="col-4">
                                        <span class="fw-medium text-secondary"><i class="bi bi-box text-primary me-2"></i>Stock:</span>
                                        <span class="fw-semibold text-dark">{{ $this->viewStockQuantity ?? 0 }}</span>
                                    </div>

                                    <div class="col-4">
                                        <span class="fw-medium text-secondary"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Damage:</span>
                                        <span class="fw-semibold text-dark">{{ $this->viewDamageQuantity ?? 0 }}</span>
                                    </div>

                                    <div class="col-4">
                                        <span class="fw-medium text-secondary"><i class="bi bi-cart-check text-primary me-2"></i>Sold:</span>
                                        <span class="fw-semibold text-dark">{{ $this->viewSoldQuantity ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Full Width: Custom Fields -->
                        @if (!empty($this->viewCustomerFields))
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                                <div class="card-header py-3 px-4 bg-light">
                                    <h6 class="fw-bold text-uppercase text-muted mb-0">Custom Fields</h6>
                                </div>
                                <div class="card-body p-0">
                                    <ul class="list-group list-group-flush">
                                        @foreach ($this->viewCustomerFields as $key => $value)
                                        <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4">
                                            <span class="fw-medium text-secondary">{{ $key }}:</span>
                                            <span class="fw-semibold text-dark">{{ $value ?? '-' }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>

                <!-- Footer -->
                <div class="modal-footer py-3 px-5 border-0"
                    style="background: #f9fafb;">
                    <button type="button"
                        class="btn rounded-pill px-4 fw-semibold text-white shadow-sm"
                        onclick="printProductDetails()"
                        style="background-color: #9d1c20;">
                        <i class="bi bi-printer me-1"></i> Print
                    </button>
                    <button type="button"
                        class="btn rounded-pill px-4 fw-semibold text-white shadow-sm"
                        wire:click="$set('showViewModal', false)"
                        style="background-color: #6B7280;">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Import Modal -->
    @if($showImportModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid var(--info); background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: var(--info); color: white;">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-file-earmark-excel me-2"></i>Import Products from Excel/CSV
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="toggleImportModal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="alert alert-info border-0 rounded-3 mb-4" style="background-color: rgba(59, 130, 246, 0.1); color: var(--info);">
                        <div class="d-flex">
                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Import Instructions</h6>
                                <p class="mb-0 small">Please use the template below to format your data. All products will be imported into a <strong>"General"</strong> category by default and codes will be auto-generated.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 text-center">
                        <p class="text-muted mb-2 small">First, download the official template to see the required format:</p>
                        <button wire:click="downloadTemplate" class="btn btn-outline-info btn-sm rounded-pill px-4">
                            <i class="bi bi-download me-1"></i>Download CSV Template
                        </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold small mb-2" style="color: var(--info);">Select Excel/CSV File</label>
                            <div class="input-group overflow-hidden rounded-3 border-2 shadow-sm" style="border: 1px solid var(--info);">
                                <span class="input-group-text bg-white border-0 text-info"><i class="bi bi-file-earmark-arrow-up"></i></span>
                                <input type="file" class="form-control border-0 shadow-none @error('excel_file') is-invalid @enderror" wire:model="excel_file">
                            </div>
                            @error('excel_file') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div wire:loading wire:target="excel_file" class="mt-2 small text-info text-center">
                                <span class="spinner-border spinner-border-sm me-1"></span>Uploading file...
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h6 class="fw-bold mb-3 small text-uppercase text-muted" style="letter-spacing: 1px;">Template Format Preview</h6>
                            <div class="table-responsive rounded-3 border shadow-sm">
                                <table class="table table-sm table-bordered mb-0 small text-center">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="py-2">Product Name</th>
                                            <th class="py-2">Supplier Price</th>
                                            <th class="py-2">Selling Price</th>
                                            <th class="py-2">Total Stock</th>
                                            <th class="py-2">Damage Stock</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-muted py-2">Example Item</td>
                                            <td class="text-muted py-2">100.00</td>
                                            <td class="text-muted py-2">150.00</td>
                                            <td class="text-muted py-2">100</td>
                                            <td class="text-muted py-2">5</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <p class="mt-2 text-muted italic small text-center" style="font-size: 0.75rem;">* Stock Quantity will be calculated as (Total Stock - Damage Stock)</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-3 px-4 border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" wire:click="toggleImportModal" style="background-color: #6B7280; border: none; color: white;">Cancel</button>
                    <button type="button" class="btn btn-info text-white rounded-pill px-4 shadow-sm" wire:click="importFromExcel" wire:loading.attr="disabled" style="background-color: var(--info); border: none;">
                        <span wire:loading.remove wire:target="importFromExcel"><i class="bi bi-check-circle me-1"></i>Start Import</span>
                        <span wire:loading wire:target="importFromExcel"><span class="spinner-border spinner-border-sm me-1"></span>Importing...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @push('styles')
    @include('components.admin-styles')
    @endpush



    @push('script')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = document.querySelector('.alert');
            if (alert) {
                setTimeout(function() {
                    alert.classList.add('show');
                }, 100);
                setTimeout(function() {
                    alert.classList.remove('show');
                }, 5000);
            }
        });
    </script>
    @endpush
</div>