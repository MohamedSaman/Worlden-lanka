<div>
    <div class="container-fluid py-6 bg-gray-50 min-vh-100 transition-colors duration-300">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg overflow-hidden bg-white">
                    <div class="card-header text-white p-2 d-flex align-items-center" style="background: linear-gradient(90deg, #1e40af 0%, #3b82f6 100%); border-radius: 20px 20px 0 0;">
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
                    <div class="card-header p-4" style="background-color: #f8f9fa;">
                        <h5 class="mb-0 fw-bold" style="color: #233D7F;">Returned Cheque List</h5>
                    </div>

                    <div class="card-body p-5">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead style="background-color: #f8f9fa;">
                                    <tr>
                                        <th class="ps-4 text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F;">Cheque No</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F;">Bank</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F;">Customer Name</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F;">Amount</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F;">Cheque Date</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F;">Status</th>
                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($chequeDetails as $cheque)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $cheque->cheque_number }}</td>
                                        <td class="text-center">{{ $cheque->bank_name }}</td>
                                        <td class="text-center">{{ $cheque->customer ? $cheque->customer->name : 'N/A' }}</td>
                                        <td class="text-center">Rs. {{ number_format($cheque->cheque_amount, 2) }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($cheque->cheque_date)->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger px-3 py-2">
                                                {{ ucfirst($cheque->status) }}
                                            </span>
                                        </td>
                                        <td class="text-center d-flex justify-content-center gap-2">
                                            @if($cheque->status === 'return')
                                            <button wire:click="openReentryModal({{ $cheque->id }})" class="btn btn-sm btn-primary rounded-pill px-3 transition-all hover:shadow" style="background-color: #00C8FF; border-color: #00C8FF; color: white;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">
                                                <i class="bi bi-redo me-1"></i>Re-entry
                                            </button>
                                            <button wire:click="openCompleteModal({{ $cheque->id }})" class="btn btn-sm btn-success rounded-pill px-3 transition-all hover:shadow" style="background-color: #28a745; border-color: #28a745; color: white;" onmouseover="this.style.backgroundColor='#1e7e34'; this.style.borderColor='#1e7e34';" onmouseout="this.style.backgroundColor='#28a745'; this.style.borderColor='#28a745';">
                                                <i class="bi bi-check-circle me-1"></i>To Complete
                                            </button>
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

        <!-- Re-entry Cheque Modal -->
        <div wire:ignore.self class="modal fade" id="reentry-modal" tabindex="-1" aria-labelledby="reentry-modal-label" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-lg modal-dialog-centered" style="max-width: 1200px;">
                <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                    <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                        <h5 class="modal-title fw-bold tracking-tight" id="reentry-modal-label" style="font-size: 1.5rem; font-weight: 600;">
                            <i class="bi bi-journal-plus me-2"></i>New Cheque Re-entry
                        </h5>
                        <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-5">
                        <div class="row g-4">
                            <!-- Original Cheque Details Column -->
                            <div class="col-md-3">
                                @if($originalCheque)
                                <div class="card border-0 shadow-sm rounded-4" style="background-color: #e6f3ff; border: 1px solid #233D7F;">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3" style="color: #233D7F; font-size: 1.25rem;">Original Cheque Details</h6>
                                        <div style="font-size: 0.95rem; line-height: 1.6;">
                                            <p class="mb-2"><strong>Customer:</strong> {{ $originalCheque->customer ? $originalCheque->customer->name : 'N/A' }}</p>
                                            <p class="mb-2"><strong>Cheque Number:</strong> {{ $originalCheque->cheque_number }}</p>
                                            <p class="mb-2"><strong>Bank:</strong> {{ $originalCheque->bank_name }}</p>
                                            <p class="mb-2"><strong>Amount:</strong> Rs. {{ number_format($originalCheque->cheque_amount, 2) }}</p>
                                            <p class="mb-0"><strong>Date:</strong> {{ \Carbon\Carbon::parse($originalCheque->cheque_date)->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Cheque Details Column -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background-color: #f8f9fa; border: 1px solid #233D7F;">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3" style="color: #233D7F; font-size: 1.25rem;">Add New Cheque Details</h6>
                                        <p class="text-sm text-gray-600 mb-4" style="font-size: 0.9rem;">Enter details for new cheques. Total must match Rs. {{ $originalCheque ? number_format($originalCheque->cheque_amount, 2) : 'N/A' }}.</p>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #233D7F; font-size: 0.95rem;">Cheque Number</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-2 border-end-0" style=" color: #233D7F;"><i class="bi bi-hash"></i></span>
                                                    <input type="text" class="form-control border-2 shadow-sm rounded-4" placeholder="Enter Cheque Number" wire:model="chequeNumber" style=" color: #233D7F; font-size: 0.9rem;">
                                                </div>
                                                @error('chequeNumber') <div class="text-danger small mt-1" style="font-size: 0.85rem;">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #233D7F; font-size: 0.95rem;">Bank Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-2 border-end-0" style="color: #233D7F;">
                                                        <i class="bi bi-bank"></i>
                                                    </span>
                                                    <select class="form-select" wire:model="bankName">
                                                        <option value="">-- Select a bank --</option>
                                                        @foreach($banks as $bank)
                                                        <option value="{{ $bank }}">{{ $bank }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                @error('bankName') <div class="text-danger small mt-1" style="font-size: 0.85rem;">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #233D7F; font-size: 0.95rem;">Amount</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-2 border-end-0" style=" color: #233D7F;">Rs.</span>
                                                    <input type="number" class="form-control border-2 shadow-sm rounded-4" placeholder="Enter Amount" wire:model="chequeAmount" step="0.01" style=" color: #233D7F; font-size: 0.9rem;">
                                                </div>
                                                @error('chequeAmount') <div class="text-danger small mt-1" style="font-size: 0.85rem;">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #233D7F; font-size: 0.95rem;">Cheque Date</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-2 border-end-0" style=" color: #233D7F;"><i class="bi bi-calendar"></i></span>
                                                    <input type="date" class="form-control border-2 shadow-sm rounded-4" wire:model="chequeDate" style=" color: #233D7F; font-size: 0.9rem;">
                                                </div>
                                                @error('chequeDate') <div class="text-danger small mt-1" style="font-size: 0.85rem;">{{ $message }}</div> @enderror
                                            </div>
                                            <div class="col-12 text-end">
                                                <button type="button" wire:click="addCheque" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #00C8FF; border-color: #00C8FF; color: white; font-size: 0.9rem;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">
                                                    <i class="bi bi-plus-circle me-1"></i>Add Cheque
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm rounded-4" style="background-color: #f8f9fa; border: 1px solid #233D7F;">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3" style="color: #233D7F; font-size: 1.25rem;">Added Cheques</h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover align-middle">
                                                <thead style="background-color: #f8f9fa;">
                                                    <tr>
                                                        <th class="ps-4 text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F; font-size: 0.85rem;">Cheque No</th>
                                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F; font-size: 0.85rem;">Bank</th>
                                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F; font-size: 0.85rem;">Date</th>
                                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F; font-size: 0.85rem;">Amount</th>
                                                        <th class="text-uppercase text-xs fw-semibold py-3 text-center" style="color: #233D7F; font-size: 0.85rem;">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($cheques as $index => $cheque)
                                                    <tr>
                                                        <td class="text-center" style="font-size: 0.9rem;">{{ $cheque['number'] }}</td>
                                                        <td class="text-center" style="font-size: 0.9rem;">{{ $cheque['bank'] }}</td>
                                                        <td class="text-center" style="font-size: 0.9rem;">{{ \Carbon\Carbon::parse($cheque['date'])->format('d/m/Y') }}</td>
                                                        <td class="text-center" style="font-size: 0.9rem;">Rs. {{ number_format($cheque['amount'], 2) }}</td>
                                                        <td class="text-center">
                                                            <button type="button" wire:click="removeCheque({{ $index }})" class="btn btn-sm btn-danger rounded-pill px-3 transition-all hover:shadow" style="font-size: 0.85rem;">Remove</button>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr>
                                                        <td colspan="5" class="text-center py-4 text-gray-600" style="font-size: 0.9rem;">No new cheques added yet.</td>
                                                    </tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cash and Notes Column -->
                            <div class="col-md-3">
                                <div class="card border-0 shadow-sm rounded-4 mb-4" style="background-color: #f8f9fa; border: 1px solid #233D7F;">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3" style="color: #233D7F; font-size: 1.25rem;">Add Cash Payment</h6>
                                        <p class="text-sm text-gray-600 mb-4" style="font-size: 0.9rem;">Enter cash amount if applicable. Combined with cheques, total must match original amount.</p>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <label class="form-label fw-medium" style="color: #233D7F; font-size: 0.95rem;">Cash Amount</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-2 border-end-0" style=" color: #233D7F;">Rs.</span>
                                                    <input type="number" class="form-control border-2 shadow-sm rounded-4" placeholder="Enter Cash Amount" wire:model="cashAmount" step="0.01" style=" color: #233D7F; font-size: 0.9rem;">
                                                </div>
                                                @error('cashAmount') <div class="text-danger small mt-1" style="font-size: 0.85rem;">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card border-0 shadow-sm rounded-4" style="background-color: #f8f9fa; border: 1px solid #233D7F;">
                                    <div class="card-body p-4">
                                        <h6 class="fw-bold mb-3" style="color: #233D7F; font-size: 1.25rem;">Notes</h6>
                                        <p class="text-sm text-gray-600 mb-4" style="font-size: 0.9rem;">Add any additional notes for this re-entry.</p>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <textarea class="form-control border-2 shadow-sm rounded-4" rows="6" placeholder="Enter notes here" wire:model="note" style="border-color: #233D7F; color: #233D7F; font-size: 0.9rem;"></textarea>
                                                @error('note') <div class="text-danger small mt-1" style="font-size: 0.85rem;">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($errors->any())
                        <div class="alert alert-danger mt-4" style="border-color: #dc3545; color: #dc3545; font-size: 0.9rem;">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer py-3 px-4 d-flex justify-content-end gap-3" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" data-bs-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: white; font-size: 0.9rem;">
                            <i class="bi bi-x me-1"></i>Cancel
                        </button>
                        <button type="button" wire:click="submitNewCheque" class="btn btn-primary rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #00C8FF; border-color: #00C8FF; color: white; font-size: 0.9rem;" onmouseover="this.style.backgroundColor='#233D7F'; this.style.borderColor='#233D7F';" onmouseout="this.style.backgroundColor='#00C8FF'; this.style.borderColor='#00C8FF';">
                            <i class="bi bi-check2-circle me-1"></i>Save Cheque(s)
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complete with Cash Modal -->
        <div wire:ignore.self class="modal fade" id="complete-modal" tabindex="-1" aria-labelledby="complete-modal-label" aria-hidden="true" style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 shadow-xl overflow-hidden" style="border: 2px solid #233D7F; background: linear-gradient(145deg, #ffffff, #f8f9fa);">
                    <div class="modal-header py-3 px-4" style="background-color: #233D7F; color: white;">
                        <h5 class="modal-title fw-bold tracking-tight" id="complete-modal-label">
                            <i class="bi bi-cash-stack me-2"></i>Complete with Cash
                        </h5>
                        <button type="button" class="btn-close btn-close-white opacity-75 hover:opacity-100" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-5">
                        <!-- Original Cheque Details -->
                        @if($originalCheque)
                        <div class="alert alert-info mb-4" style="background-color: #e6f3ff; border-color: #233D7F; color: #233D7F;">
                            <h6 class="fw-bold mb-2">Original Cheque Details</h6>
                            <p class="mb-1"><strong>Customer:</strong> {{ $originalCheque->customer ? $originalCheque->customer->name : 'N/A' }}</p>
                            <p class="mb-1"><strong>Cheque Number:</strong> {{ $originalCheque->cheque_number }}</p>
                            <p class="mb-1"><strong>Bank:</strong> {{ $originalCheque->bank_name }}</p>
                            <p class="mb-1"><strong>Amount:</strong> Rs. {{ number_format($originalCheque->cheque_amount, 2) }}</p>
                            <p class="mb-0"><strong>Date:</strong> {{ \Carbon\Carbon::parse($originalCheque->cheque_date)->format('d/m/Y') }}</p>
                        </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label fw-medium" style="color: #233D7F;">Cash Amount <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-2 border-end-0" style="border-color: #233D7F; color: #233D7F;">Rs.</span>
                                <input type="number" class="form-control border-2 shadow-sm rounded-4" wire:model="completeCashAmount" step="0.01" style="border-color: #233D7F; color: #233D7F;">
                            </div>
                            @error('completeCashAmount') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-medium" style="color: #233D7F;">Note <span class="text-danger">*</span></label>
                            <textarea class="form-control border-2 shadow-sm rounded-4" rows="3" placeholder="Enter note here" wire:model="completeNote" style="border-color: #233D7F; color: #233D7F;"></textarea>
                            @error('completeNote') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>
                    <div class="modal-footer py-3 px-4 d-flex justify-content-end gap-3" style="border-top: 1px solid #233D7F; background: #f8f9fa;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4 fw-medium transition-all hover:shadow" data-bs-dismiss="modal" style="background-color: #6B7280; border-color: #6B7280; color: white;">
                            <i class="bi bi-x me-1"></i>Cancel
                        </button>
                        <button type="button" wire:click="submitCompleteWithCash" class="btn btn-success rounded-pill px-4 fw-medium transition-all hover:shadow" style="background-color: #28a745; border-color: #28a745; color: white;" onmouseover="this.style.backgroundColor='#1e7e34'; this.style.borderColor='#1e7e34';" onmouseout="this.style.backgroundColor='#28a745'; this.style.borderColor='#28a745';">
                            <i class="bi bi-check2-circle me-1"></i>Submit Cash Payment
                        </button>
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
                color: #233D7F;
            }

            .btn-light:hover {
                background-color: #f1f5f9;
                border-color: #f1f5f9;
                color: #233D7F;
            }

            .form-control,
            .form-select {
                border-radius: 1rem;
                border: 2px solid #e5e7eb;
            }

            .form-control:focus,
            .form-select:focus {
                border-color: #233D7F;
                box-shadow: 0 0 0 0.2rem rgba(35, 61, 127, 0.25);
            }
        </style>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
        @endpush

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('livewire:initialized', () => {
                let reentryModal = new bootstrap.Modal(document.getElementById('reentry-modal'));
                let completeModal = new bootstrap.Modal(document.getElementById('complete-modal'));

                @this.on('open-reentry-modal', () => {
                    reentryModal.show();
                });

                @this.on('close-reentry-modal', () => {
                    reentryModal.hide();
                });

                @this.on('open-complete-modal', () => {
                    completeModal.show();
                });

                @this.on('close-complete-modal', () => {
                    completeModal.hide();
                });

                @this.on('notify', event => {
                    Swal.fire({
                        icon: event.detail.type,
                        title: event.detail.type === 'success' ? 'Success!' : 'Error!',
                        text: event.detail.message,
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                    });
                });
            });
        </script>
        @endpush
    </div>