<div>
    <div class="container-fluid py-6 bg-gray-50 min-vh-100 transition-colors duration-300">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                    <div class="card-header text-white p-5 rounded-t-4 d-flex align-items-center"
                        style="background: linear-gradient(90deg, #d97706 0%, #fbbf24 100%);">
                        <div class="icon-shape icon-lg bg-white bg-opacity-25 rounded-circle p-3 d-flex align-items-center justify-content-center me-3">
                            <i class="bi bi-arrow-counterclockwise text-white fs-4" aria-hidden="true"></i>
                        </div>
                        <div>
                            <h3 class="mb-1 fw-bold tracking-tight text-white">Returned Cheque Management</h3>
                            <p class="text-white opacity-80 mb-0 text-sm">Manage and re-process returned cheques</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                    <div class="card-header p-4" style="background-color: #eff6ff;">
                        <h5 class="mb-0 fw-bold" style="color: #1e3a8a;">Returned Cheque List</h5>
                    </div>

                    <div class="card-body p-5">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #eff6ff;">
                                    <tr>
                                        <th class="ps-4 text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Cheque No</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Bank</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Customer Name</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Amount</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Cheque Date</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Status</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($chequeDetails as $cheque)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $cheque->cheque_number }}</td>
                                        <td class="text-center">{{ $cheque->bank_name }}</td>
                                        <td class="text-center">{{ $cheque->customer->name }}</td>
                                        <td class="text-center">Rs.{{ number_format($cheque->cheque_amount, 2) }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($cheque->cheque_date)->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2">
                                                {{ ucfirst($cheque->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if($cheque->status === 'return')
                                                <button wire:click="openReentryModal({{ $cheque->id }})" class="btn btn-sm btn-warning rounded-pill px-3">Re-entry</button>
                                            @else
                                                <span class="badge rounded-pill bg-success bg-opacity-10 text-success px-3 py-2">Processed</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-gray-600">No returned cheques found.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div wire:ignore.self class="modal fade" id="reentry-modal" tabindex="-1" aria-labelledby="reentry-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content rounded-4 shadow-lg">
                    <div class="modal-header text-white p-4" style="background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%);">
                        <h5 class="modal-title fw-bold tracking-tight" id="reentry-modal-label">
                            <i class="bi bi-journal-plus me-2"></i> New Cheque Re-entry
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-5">
                        <div class="bg-light p-4 border-bottom rounded-4 mb-4">
                            <h6 class="fw-bold text-gray-800" style="color: #1e3a8a;">Add New Cheque Details</h6>
                            <p class="text-sm text-gray-600 mb-0">Enter the details for one or more new cheques received from the customer.</p>
                        </div>
                        <div class="row g-3 mb-4 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Cheque Number</label>
                                <input type="text" class="form-control rounded-4 shadow-sm" placeholder="Enter Cheque Number" wire:model="chequeNumber">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Bank Name</label>
                                <input type="text" class="form-control rounded-4 shadow-sm" placeholder="Enter Bank Name" wire:model="bankName">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Amount</label>
                                <input type="number" class="form-control rounded-4 shadow-sm" placeholder="Enter Amount" wire:model="chequeAmount">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-sm fw-semibold mb-2" style="color: #1e3a8a;">Cheque Date</label>
                                <input type="date" class="form-control rounded-4 shadow-sm" wire:model="chequeDate">
                            </div>
                            <div class="col-md-12 text-end">
                                <button type="button" wire:click="addCheque" class="btn btn-success rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105">
                                    <i class="bi bi-plus-circle me-1"></i> Add Cheque
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead style="background-color: #eff6ff;">
                                    <tr>
                                        <th class="ps-4 text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Cheque No</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Bank</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Date</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Amount</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #1e3a8a;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($cheques as $index => $cheque)
                                    <tr>
                                        <td class="text-center">{{ $cheque['number'] }}</td>
                                        <td class="text-center">{{ $cheque['bank'] }}</td>
                                        <td class="text-center">{{ $cheque['date'] }}</td>
                                        <td class="text-center">Rs.{{ number_format($cheque['amount'], 2) }}</td>
                                        <td class="text-center">
                                            <button type="button" wire:click="removeCheque({{ $index }})" class="btn btn-sm btn-danger rounded-pill">Remove</button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-gray-600">No new cheques added yet.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer p-4 bg-light border-top">
                        <button type="button" class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105" data-bs-dismiss="modal">
                            <i class="bi bi-x me-1"></i> Cancel
                        </button>
                        <button type="button" wire:click="submitNewCheque" class="btn btn-light rounded-full shadow-sm px-4 py-2 transition-transform hover:scale-105">
                            <i class="bi bi-check2-circle me-1"></i> Save Cheque(s)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        body {
            font-family: 'Inter', sans-serif;
            font-size: 15px;
            color: #1f2937;
        }

        .tracking-tight {
            letter-spacing: -0.025em;
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .transition-transform {
            transition: transform 0.2s ease;
        }

        .hover\:scale-105:hover {
            transform: scale(1.05);
        }

        .icon-shape {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .icon-shape.icon-lg {
            width: 3rem;
            height: 3rem;
        }

        .table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th,
        .table td {
            border: 1px solid #e5e7eb;
            vertical-align: middle;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .table tbody tr:hover {
            background-color: #f1f5f9;
        }

        .rounded-full {
            border-radius: 9999px;
        }

        .rounded-4 {
            border-radius: 1rem;
        }

        .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .shadow-sm {
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        }

        .btn-light {
            background-color: #ffffff;
            border-color: #ffffff;
            color: #1e3a8a;
        }

        .btn-light:hover {
            background-color: #f1f5f9;
            border-color: #f1f5f9;
            color: #1e3a8a;
        }

        .form-control,
        .form-select {
            border-radius: 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #1e40af;
            box-shadow: 0 0 0 0.2rem rgba(30, 64, 175, 0.25);
        }
    </style>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            let reentryModal = new bootstrap.Modal(document.getElementById('reentry-modal'));

            @this.on('open-reentry-modal', () => {
                reentryModal.show();
            });

            @this.on('close-reentry-modal', () => {
                reentryModal.hide();
            });
        });
    </script>
    @endpush
</div>