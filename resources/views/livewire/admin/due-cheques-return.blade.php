<div>
    <div class="container-fluid px-4">
        <h1 class="mt-4">Due Cheques Return</h1>

        <div class="card mb-4">
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Cheque No</th>
                        <th>Bank</th>
                        <th>Customer Name</th>
                        <th>Amount</th>
                        <th>Cheque Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chequeDetails as $cheque)
                    <tr>
                        <td>{{ $cheque->cheque_number }}</td>
                        <td>{{ $cheque->bank_name }}</td>
                        <td>{{ $cheque->customer->name }}</td>
                        <td>Rs.{{ number_format($cheque->cheque_amount, 2) }}</td>
                        <td>{{ $cheque->cheque_date }}</td>
                        <td>{{ $cheque->status }}</td>
                        <td>
                            @if($cheque->status === 'return')
                                <button wire:click="openReentryModal({{ $cheque->id }})" class="btn btn-warning btn-sm">Re-entry</button>
                            @else
                                <span class="text-success">Processed</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Modal -->
        <div wire:ignore.self class="modal fade" id="reentry-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">New Cheque Re-entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Cheque Number" wire:model="chequeNumber">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" placeholder="Bank Name" wire:model="bankName">
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control" placeholder="Amount" wire:model="chequeAmount">
                            </div>
                            <div class="col-md-6">
                                <input type="date" class="form-control" wire:model="chequeDate">
                            </div>
                            <div class="col-md-2">
                                <button type="button" wire:click="addCheque" class="btn btn-success w-100">Add</button>
                            </div>
                        </div>

                        <!-- Temporary cheque table -->
                        <table class="table table-bordered table-sm mt-3">
                            <thead>
                                <tr>
                                    <th>Cheque No</th>
                                    <th>Bank</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($cheques as $index => $cheque)
                                <tr>
                                    <td>{{ $cheque['number'] }}</td>
                                    <td>{{ $cheque['bank'] }}</td>
                                    <td>{{ $cheque['date'] }}</td>
                                    <td>Rs.{{ number_format($cheque['amount'], 2) }}</td>
                                    <td>
                                        <button type="button" wire:click="removeCheque({{ $index }})" class="btn btn-danger btn-sm">Remove</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No cheques added</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" wire:click="submitNewCheque" class="btn btn-primary">Save Cheque(s)</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@push('scripts')
        <script>
        Livewire.on('open-reentry-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('reentry-modal'));
            myModal.show();
        });

        Livewire.on('close-reentry-modal', () => {
            var myModalEl = document.getElementById('reentry-modal');
            var modal = bootstrap.Modal.getInstance(myModalEl);
            modal.hide();
        });
    </script>
</div>

@endpush
    <!-- Bootstrap modal trigger for Livewire 3 -->
