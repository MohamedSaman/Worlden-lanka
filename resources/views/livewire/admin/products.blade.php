<div class="container-fluid py-1">

    <!-- Header Section -->
    <div class="card-header bg-transparent pb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 border-bottom" style="border-color: #233D7F;">

        <!-- Left: Icon + Title -->
        <div class="d-flex align-items-center gap-3 flex-shrink-0">
            <div class="icon-shape icon-lg bg-opacity-25 p-3 d-flex align-items-center justify-content-center">
                <i class="bi bi-box-seam fs-4" aria-hidden="true" style="color:#233D7F;"></i>
            </div>
            <div>
                <h3 class="mb-1 fw-bold tracking-tight text-dark">Product Details</h3>
                <p class="text-dark opacity-80 mb-0 text-sm">Monitor and manage your Product Details</p>
            </div>
        </div>

        <!-- Middle: Search Bar -->
        <div class="flex-grow-1 d-flex justify-content-lg-center">
            <div class="input-group" style="max-width: 400px; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);">
                <span class="input-group-text bg-gray-100 border-0 px-3">
                    <i class="bi bi-search text-primary"></i>
                </span>
                <input type="text"
                    class="form-control"
                    placeholder="Search products..."
                    wire:model.live.debounce.300ms="search"
                    autocomplete="off">
            </div>
        </div>

        <!-- Right: Buttons -->
        <div class="d-flex gap-2 flex-shrink-0 justify-content-lg-end">
            <button class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform btn-create"
                wire:click="toggleAddModal"
                style="color: #fff; background-color: #233D7F; border: 1px solid #233D7F;transition: all 0.3s ease; hover: transform: scale(1.05)">
                <i class="bi bi-plus-circle me-2"></i> Add Product
            </button>
            <button wire:click="exportToCSV"
                class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform "
                aria-label="Export stock details to CSV"
                style="color: #fff; background-color: #233D7F; border: 1px solid #233D7F; transition: all 0.3s ease; hover: transform: scale(1.05)">
                <i class="bi bi-download me-1" aria-hidden="true"></i> Export CSV
            </button>

        </div>
    </div>
    <div class="d-flex  align-items-center justify-content-between gap-4">
        <p class="ms-4">You can create custom field here <i class="bi bi-arrow-right ms-4"></i></p>
        <button
            class="btn btn-primary rounded-full mt-2 px-4 mb-4 fw-medium transition-all hover:shadow "
            wire:click="$set('showAddFieldModal', true)"
            style="background-color: #233D7F; border-color: #233D7F; color: white;">
            <i class="bi bi-plus-circle me-2"></i>Add Field
        </button>
    </div>

    <!-- Products Table -->
    <div class="card-body py-0 px-1  bg-transparent">
        <div class="table-responsive shadow-sm rounded-2 overflow-hidden">
            <table class="table table-sm">
                <thead>
                    <tr>
                        <th class="ps-4 py-3">ID</th>
                        <th class="py-3">Product Code</th>
                        <th class="py-3">Product Name</th>
                        <th class="py-3">Category</th>
                        <th class="py-3">Supplier Price</th>
                        <th class="py-3">Selling Price</th>

                        <th class="py-3">Total Quantity</th>
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
                        <td class="py-3">Rs. {{ number_format($product->supplier_price, 2) }}</td>
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
                        <td colspan="{{ 9 + count($fieldKeys) }}" class="text-center py-4" style="color: #233D7F;">
                            <i class="bi bi-exclamation-circle me-2"></i>No products found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $products->links() }}

    <!-- Add Product Modal -->
    @if ($showAddModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width: 900px;">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Add New Product</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="toggleAddModal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="save">
                    <div class="modal-body p-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="category_id" class="form-label fw-medium" style="color: #233D7F;">Category</label>
                                <select
                                    id="category_id"
                                    wire:model="category_id"
                                    class="form-select border-2 shadow-sm"
                                    style=" color: #233D7F;">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="product_name" class="form-label fw-medium" style="color: #233D7F;">Product Name</label>
                                <input
                                    type="text"
                                    id="product_name"
                                    wire:model="product_name"
                                    class="form-control border-2 shadow-sm"
                                    style=" color: #233D7F;">
                                @error('product_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="supplier_price" class="form-label fw-medium" style="color: #233D7F;">Supplier Price</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-2 border-end-0" >Rs.</span>
                                    <input
                                        type="number"
                                        id="supplier_price"
                                        wire:model="supplier_price"
                                        class="form-control border-2 shadow-sm"
                                        style=" color: #233D7F;"
                                        step="0.01">
                                </div>
                                @error('supplier_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="selling_price" class="form-label fw-medium" style="color: #233D7F;">Selling Price</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-2 border-end-0" >Rs.</span>
                                    <input
                                        type="number"
                                        id="selling_price"
                                        wire:model="selling_price"
                                        class="form-control border-2 shadow-sm"
                                        style=" color: #233D7F;"
                                        step="0.01">
                                </div>
                                @error('selling_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="stock_quantity" class="form-label fw-medium" style="color: #233D7F;">Total Quantity</label>
                                <input
                                    type="number"
                                    id="stock_quantity"
                                    wire:model="stock_quantity"
                                    class="form-control border-2 shadow-sm"
                                    style=" color: #233D7F;"
                                    min="0" step="1">
                                @error('stock_quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="damage_quantity" class="form-label fw-medium" style="color: #233D7F;">Damage Quantity</label>
                                <input
                                    type="number"
                                    id="damage_quantity"
                                    wire:model="damage_quantity"
                                    class="form-control border-2 shadow-sm"
                                    style=" color: #233D7F;"
                                    min="0" step="1">
                                @error('damage_quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mt-2">
                            <h6 class="fw-bold text-uppercase text-muted">Customer Fields</h6>
                            <div class="row g-4">
                                @foreach ($fieldKeys as $key)
                                <div class="col-md-6 mb-3">
                                    <label for="customer_field_{{ $key }}" class="form-label fw-medium" style="color: #233D7F;">{{ $key }}</label>
                                    <input
                                        type="text"
                                        id="customer_field_{{ $key }}"
                                        wire:model="customer_fields.{{ $loop->index }}.value"
                                        class="form-control border-2 shadow-sm"
                                        style="color: #233D7F;"
                                        placeholder="Enter {{ $key }}">
                                    @error('customer_fields.' . $loop->index . '.value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer py-3 px-4 d-flex justify-content-end gap-3" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="toggleAddModal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Save Product</button>
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
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Edit Product</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="toggleEditModal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="update">
                    <div class="modal-body p-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label for="edit_category_id" class="form-label fw-medium" style="color: #233D7F;">Category</label>
                                <select
                                    id="edit_category_id"
                                    wire:model="category_id"
                                    class="form-select border-2 shadow-sm"
                                    style=" color: #233D7F;">
                                    <option value="">Select a category</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="edit_product_name" class="form-label fw-medium" style="color: #233D7F;">Product Name</label>
                                <input
                                    type="text"
                                    id="edit_product_name"
                                    wire:model="product_name"
                                    class="form-control border-2 shadow-sm"
                                    style=" color: #233D7F;">
                                @error('product_name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="edit_supplier_price" class="form-label fw-medium" style="color: #233D7F;">Supplier Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-2 border-end-0" style="">Rs.</span>
                                        <input
                                            type="number"
                                            id="edit_supplier_price"
                                            wire:model="supplier_price"
                                            class="form-control border-2 shadow-sm"
                                            style=" color: #233D7F;"
                                            step="0.01">
                                    </div>
                                    @error('supplier_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="edit_selling_price" class="form-label fw-medium" style="color: #233D7F;">Selling Price</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-white border-2 border-end-0" style="">Rs.</span>
                                        <input
                                            type="number"
                                            id="edit_selling_price"
                                            wire:model="selling_price"
                                            class="form-control border-2 shadow-sm"
                                            style=" color: #233D7F;"
                                            step="0.01">
                                    </div>
                                    @error('selling_price') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="edit_stock_quantity" class="form-label fw-medium" style="color: #233D7F;">Stock Quantity</label>
                                    <input
                                        type="number"
                                        id="edit_stock_quantity"
                                        wire:model="stock_quantity"
                                        class="form-control border-2 shadow-sm"
                                        style=" color: #233D7F;"
                                        min="0" step="1">
                                    @error('stock_quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-4">
                                    <label for="edit_damage_quantity" class="form-label fw-medium" style="color: #233D7F;">Damage Quantity</label>
                                    <input
                                        type="number"
                                        id="edit_damage_quantity"
                                        wire:model="damage_quantity"
                                        class="form-control border-2 shadow-sm"
                                        style=" color: #233D7F;"
                                        min="0" step="1">
                                    @error('damage_quantity') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium" style="color: #233D7F;">Sold Quantity</label>
                                <input
                                    type="number"
                                    id="edit_sold_quantity"
                                    wire:model="sold"
                                    class="form-control border-2 shadow-sm"
                                    style=" color: #233D7F;"
                                    min="0" step="1">
                                @error('sold') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label fw-medium" style="color: #233D7F;">Status</label>
                                <select
                                    id="status"
                                    wire:model="status"
                                    class="form-select border-2 shadow-sm"
                                    style=" color: #233D7F;">
                                    <option value="">Select Status</option>
                                    <option value="Available">Available</option>
                                    <option value="Unavailable">Unavailable</option>
                                </select>
                                @error('status') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>


                        <!-- customer Fields -->
                        <div class="mb-4">
                            <label class="form-label fw-medium" style="color: #233D7F;">Customer Fields</label>
                            <div class="row g-3">
                                @foreach ($customer_fields as $index => $field)
                                @php
                                $labelKey = ucwords(strtolower($field['key']));
                                @endphp
                                <div class="col-md-6">
                                    <label class="form-label fw-medium" style="color: #233D7F;">{{ $labelKey }}</label>
                                    <input
                                        type="text"
                                        placeholder="Enter {{ $labelKey }}"
                                        wire:model="customer_fields.{{ $index }}.value"
                                        class="form-control border-2 shadow-sm"
                                        style=" color: #233D7F;">
                                    @error('customer_fields.' . $index . '.value') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>
                                @endforeach
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer py-3 px-4" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="toggleEditModal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Update Product</button>
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
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="toggleDeleteModal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5" style="color: #233D7F;">
                    <p class="mb-0">Are you sure you want to delete the product "<strong>{{ $deletingProductName }}</strong>"? This action cannot be undone.</p>
                </div>
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
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
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">

                <!-- Modal Header -->
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h5 class="modal-title fw-bold tracking-tight">Add New Fields</h5>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showAddFieldModal', false)" aria-label="Close"></button>
                </div>

                <!-- Modal Body -->
                <div class="modal-body p-5">
                    <!-- Input for new fields -->
                    <input wire:model="newFieldKey" class="form-control border-2 shadow-sm mb-3" placeholder="Enter field names" style=" color: #233D7F; " />
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
                <div class="modal-footer py-3 px-4" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                    <button class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showAddFieldModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;">Cancel</button>
                    <button class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click.prevent="manageField('add')" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Add</button>
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
                style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4"
                    style="background-color: #233D7F; color: white;">
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
                        style="color: #233D7F;">Select a field to delete</label>
                    <select wire:model.live="deleteFieldKey"
                        id="deleteFieldKey"
                        class="form-select border-2 shadow-sm"
                        style="border-color: #233D7F; color: #233D7F;"
                        wire:loading.attr="disabled">
                        <option value="">Select Field</option>
                        @foreach ($fieldKeys as $key)
                        <option value="{{ $key }}">{{ $key }}</option>
                        @endforeach
                    </select>
                    @error('deleteFieldKey') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
                <div class="modal-footer py-3 px-4"
                    style="border-top: 1px solid #233D7F; background: #f8f9fa;">
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
<div wire:ignore.self class="modal fade show d-block" id="viewProductModal" tabindex="-1" aria-labelledby="viewProductModalLabel" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
            <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                <h5 class="modal-title fw-bold tracking-tight" id="viewProductModalLabel">
                    <i class="bi bi-box-seam me-2"></i> Product Details - {{ $this->viewProductName ?? 'N/A' }}
                </h5>
                <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" wire:click="$set('showViewModal', false)" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-5">
             
                <div class="row g-4">
                    <!-- Product Image and Status -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 p-4 transition-all hover:shadow-md">
                            @php
                                $product = \App\Models\ProductDetail::find($this->viewProductCode ? \App\Models\ProductDetail::where('product_code', $this->viewProductCode)->first()->id : null);
                            @endphp
                            @if ($product && $product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $this->viewProductName ?? 'Product Image' }}"
                                 class="img-fluid rounded-3 mb-3"
                                 style="width: 100%; max-height: 200px; object-fit: cover;">
                            @else
                            <div class="bg-light d-flex flex-column align-items-center justify-content-center mb-3 rounded-3" style="width: 100%; height: 200px;">
                                <i class="bi bi-box-seam text-muted" style="font-size: 3rem;"></i>
                                <p class="text-muted mt-2">No image available</p>
                            </div>
                            @endif
                            <div class="d-flex align-items-center">
                                <i class="bi bi-info-circle text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Status:</strong>
                                    <span class="badge rounded-pill {{ $this->viewStatus == 'Available' ? 'bg-success' : ($this->viewStatus == 'Low Stock' ? 'bg-warning' : 'bg-danger') }}" aria-label="Product Status: {{ $this->viewStatus ?? 'N/A' }}">
                                        {{ $this->viewStatus ?? 'N/A' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Product Details -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-3 p-4 transition-all hover:shadow-md">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-tag-fill text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Category:</strong> {{ $this->viewCategoryName ?? 'N/A' }}</p>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-box-seam text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Product Name:</strong> {{ $this->viewProductName ?? 'N/A' }}</p>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-tag-fill text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Code:</strong>
                                    <span class="badge rounded-pill" style="background-color: #f3f4f6; color: #1f2937;" aria-label="Product Code: {{ $this->viewProductCode ?? 'N/A' }}">
                                        {{ $this->viewProductCode ?? 'N/A' }}
                                    </span>
                                </p>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-currency-dollar text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Supplier Price:</strong> Rs.{{ number_format($this->viewSupplierPrice ?? 0, 2) }}</p>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-wallet2 text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Selling Price:</strong> Rs.{{ number_format($this->viewSellingPrice ?? 0, 2) }}</p>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-box text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Stock Quantity:</strong> {{ $this->viewStockQuantity ?? 0 }}</p>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-box text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Damage Quantity:</strong> {{ $this->viewDamageQuantity ?? 0 }}</p>
                            </div>
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-cart-check text-primary me-2"></i>
                                <p class="mb-0"><strong class="text-primary">Sold Quantity:</strong> {{ $this->viewSoldQuantity ?? 0 }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Custom Fields -->
                    @if (!empty($this->viewCustomerFields))
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
                            <div class="card-header py-3 px-4" style="background-color: #eff6ff;">
                                <h6 class="fw-bold text-uppercase text-muted letter-spacing-1 mb-0">Custom Fields</h6>
                            </div>
                            <div class="card-body p-0">
                                <ul class="list-group list-group-flush">
                                    @foreach ($this->viewCustomerFields as $key => $value)
                                    <li class="list-group-item d-flex justify-content-between align-items-center py-3 px-4 transition-all hover:bg-gray-50" aria-labelledby="custom-field-{{ str_slug($key) }}">
                                        <span class="fw-medium text-gray-700" id="custom-field-{{ str_slug($key) }}">{{ $key }}:</span>
                                        <span class="text-gray-900">{{ $value ?? '-' }}</span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
             
            </div>
            <div class="modal-footer py-3 px-4" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" onclick="printProductDetails()" style="background-color: #6B7280; border-color: #6B7280; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#6B7280'; this.style.borderColor='#6B7280';">
                    <i class="bi bi-printer me-1"></i> Print Details
                </button>
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" wire:click="$set('showViewModal', false)" style="background-color: #6B7280; border-color: #6B7280; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#6B7280'; this.style.borderColor='#6B7280';">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
    @endif


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