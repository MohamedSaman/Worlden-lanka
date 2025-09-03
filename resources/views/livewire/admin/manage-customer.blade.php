<div class="container-fluid py-2">
    <div class="card border-0 " >

    <!-- header  -->
        <div class="card-header bg-transparent pb-4 d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3">

            <!-- Left: Icon + Title -->
            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                <div class="icon-shape icon-lg bg-opacity-25 p-3 d-flex align-items-center justify-content-center">
                    <i class="bi bi-people fs-4" aria-hidden="true" style="color:#233D7F;"></i>
                </div>
                <div>
                    <h3 class="mb-1 fw-bold tracking-tight text-dark">Customer Management</h3>
                    <p class="text-dark opacity-80 mb-0 text-sm">Monitor and manage your Customer Details</p>
                </div>
            </div>

            <!-- Middle: Search Bar -->
            <div class="flex-grow-1 d-flex justify-content-lg-center">
                <div class="input-group " style="max-width: 400px;">
                    <span class="input-group-text bg-gray-100 border-0 px-3">
                        <i class="bi bi-search text-primary"></i>
                    </span>
                    <input type="text"
                        class="form-control "
                        placeholder="Search customers..."
                        wire:model.live.debounce.300ms="search"
                        autocomplete="off">
                </div>
            </div>

            <!-- Right: Buttons -->
            <div class="d-flex gap-2 flex-shrink-0 justify-content-lg-end">
                
                <button class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105 btn-create"
                    wire:click="createCustomer"
                    style="color: #fff; background-color: #233D7F; border: 1px solid #233D7F;">
                    <i class="bi bi-plus-circle me-2"></i> Create Customer
                </button>
                <button wire:click="exportToCSV"
                    class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105"
                    aria-label="Export stock details to CSV"
                    style="color: #fff; background-color: #233D7F; border: 1px solid #233D7F;">
                    <i class="bi bi-download me-1" aria-hidden="true"></i> Export CSV
                </button>
            </div>
        </div>


        <div class="card-body p-1  pt-5 bg-transparent">
            <div class="table-responsive  shadow-sm rounded-2 overflow-hidden">
                <table class="table table-sm ">
                    <thead>
                        <tr>
                            <th class="text-center ps-4 py-3">ID</th>
                            <th class="text-center py-3">Customer Name</th>
                            <th class="text-center py-3">Business Name</th>
                            <th class="text-center py-3">Contact Number</th>
                            <th class="text-center py-3">Email</th>
                            <th class="text-center py-3">Type</th>
                            <th class="text-center py-3">Address</th>
                            <th class="text-center py-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($customers->count() > 0)
                        @foreach ($customers as $customer)
                        <tr class="transition-all hover:bg-gray-50">
                            <td class="text-sm text-center  ps-4 py-3">{{$loop->iteration }}</td>
                            <td class="text-sm text-center py-3 ">{{ $customer->name ?? '-' }}</td>
                            <td class="text-sm text-center py-3 ">{{ $customer->business_name ?? '-' }}</td>
                            <td class="text-sm text-center py-3 ">{{ $customer->phone ?? '-' }}</td>
                            <td class="text-sm text-center py-3 ">{{ $customer->email ?? '-' }}</td>
                            <td class="text-sm text-center py-3 ">{{ ucfirst($customer->type) ?? '-' }}</td>
                            <td class="text-sm text-center py-3 ">{{ $customer->address ?? '-' }}</td>
                            <td class="text-sm text-center">
                                <div class="btn-group btn-group-sm gap-2" role="group">
                                    <button class="btn text-info rounded-pill px-3 " wire:click="editCustomer({{ $customer->id }})" wire:loading.attr="disabled" title="Edit">
                                        <i class="bi bi-pencil" wire:loading.class="d-none" wire:target="editCustomer({{ $customer->id }})"></i>
                                        <span wire:loading wire:target="editCustomer({{ $customer->id }})">
                                            <i class="spinner-border spinner-border-sm"></i>
                                        </span>
                                    </button>
                                    <button class="btn text-danger rounded-pill px-3" wire:click="confirmDelete({{ $customer->id }})" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="text-center py-4" style="color: #233D7F;">
                                <i class="bi bi-exclamation-circle me-2"></i>No customers found.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Create Customer Modal --}}
    <div wire:ignore.self class="modal fade" id="createCustomerModal" tabindex="-1" aria-labelledby="createCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h1 class="modal-title fs-5 fw-bold tracking-tight" id="createCustomerModalLabel">Create Customer</h1>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <label for="customerName" class="form-label fw-medium" style="color: #233D7F;">Customer Name</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="customerName" wire:model="name" placeholder="Enter customer name" style="border-color: #233D7F; color: #233D7F;">
                            @error('name')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="contactNumber" class="form-label fw-medium" style="color: #233D7F;">Contact Number</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="contactNumber" wire:model="contactNumber" placeholder="Enter contact number" style="border-color: #233D7F; color: #233D7F;">
                            @error('contactNumber')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="email" class="form-label fw-medium" style="color: #233D7F;">Email</label>
                            <input type="email" class="form-control border-2 shadow-sm" id="email" wire:model="email" placeholder="Enter email" style="border-color: #233D7F; color: #233D7F;">
                            @error('email')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="businessName" class="form-label fw-medium" style="color: #233D7F;">Business Name</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="businessName" wire:model="bussinessName" placeholder="Enter business name" style="border-color: #233D7F; color: #233D7F;">
                            @error('businessName')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="customerType" class="form-label fw-medium" style="color: #233D7F;">Customer Type</label>
                            <select class="form-select border-2 shadow-sm" id="customerType" wire:model="customerType" style="border-color: #233D7F; color: #233D7F;">
                                <option value="">Select customer type</option>
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                            @error('customerType')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="address" class="form-label fw-medium" style="color: #233D7F;">Address</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="address" wire:model="address" placeholder="Enter address" style="border-color: #233D7F; color: #233D7F;">
                            @error('address')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4 flex-column flex-sm-row gap-2" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow  w-sm-auto" data-bs-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow w-sm-auto" wire:click="saveCustomer" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Add Customer</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Edit Customer Modal --}}
    <div wire:ignore.self wire:key="edit-modal-{{ $editCustomerId ?? 'new' }}" class="modal fade" id="editCustomerModal" tabindex="-1" aria-labelledby="editCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                    <h1 class="modal-title fs-5 fw-bold tracking-tight" id="editCustomerModalLabel">Edit Customer</h1>
                    <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5">
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <label for="editName" class="form-label fw-medium" style="color: #233D7F;">Customer Name</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="editName" wire:model="editName" style="border-color: #233D7F; color: #233D7F;">
                            @error('editName')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editContactNumber" class="form-label fw-medium" style="color: #233D7F;">Contact Number</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="editContactNumber" wire:model="editContactNumber" style="border-color: #233D7F; color: #233D7F;">
                            @error('editContactNumber')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editEmail" class="form-label fw-medium" style="color: #233D7F;">Email</label>
                            <input type="email" class="form-control border-2 shadow-sm" id="editEmail" wire:model="editEmail" style="border-color: #233D7F; color: #233D7F;">
                            @error('editEmail')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editBusinessName" class="form-label fw-medium" style="color: #233D7F;">Business Name</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="editBusinessName" wire:model="editBussinessName" style="border-color: #233D7F; color: #233D7F;">
                            @error('editBusinessName')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editCustomerType" class="form-label fw-medium" style="color: #233D7F;">Customer Type</label>
                            <select class="form-select border-2 shadow-sm" id="editCustomerType" wire:model="editCustomerType" style="border-color: #233D7F; color: #233D7F;">
                                <option value="retail">Retail</option>
                                <option value="wholesale">Wholesale</option>
                            </select>
                            @error('editCustomerType')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="col-12 col-md-6">
                            <label for="editAddress" class="form-label fw-medium" style="color: #233D7F;">Address</label>
                            <input type="text" class="form-control border-2 shadow-sm" id="editAddress" wire:model="editAddress" style="border-color: #233D7F; color: #233D7F;">
                            @error('editAddress')
                            <span class="text-danger small mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-3 px-4 flex-column flex-sm-row gap-2" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                    <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow  w-sm-auto" data-bs-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: white;">Close</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow w-sm-auto" wire:click="updateCustomer({{ $editCustomerId }})" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">Update Customer</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .input-group{
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    .btn{
        transition: all 0.3s ease;
    }
    .btn:hover{
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

        .table td:nth-child(5),
        .table th:nth-child(5),
        /* Email */
        .table td:nth-child(7),
        .table th:nth-child(7) {
            /* Address */
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
                    text: "Customer has been deleted.",
                    icon: "success"
                });
            }
        });
    });

    window.addEventListener('open-edit-modal', event => {
        setTimeout(() => {
            const modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
            modal.show();
        }, 100);
    });
</script>
@endpush