<div>
    <div class="card">
        <div class="card-body">
            <div class="mb-3">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label for="type" class="form-label">Type</label>
                        <select wire:model.live="type" id="type" class="form-select">
                            <option value="">All Types</option>
                            <option value="1">Credit</option>
                            <option value="2">Deduct</option>
                        </select>
                    </div>
            
                    <div class="col-md-3">
                        <label for="search" class="form-label">Name or Mobile</label>
                        <input wire:model.live="search" type="text" id="search" class="form-control"
                               placeholder="Search name or mobile">
                    </div>
            
                    <div class="col-md-3">
                        <label for="date" class="form-label">Date</label>
                        <input wire:model.live="date" type="date" id="date" class="form-control">
                    </div>
            
                    <div class="col-md-3 d-flex align-items-end">
                        <button wire:click="exportPdf" class="btn btn-danger w-100">Export PDF</button>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive text-nowrap mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User Name</th>
                            <th>Mobile</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Description</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tnxlist as $txn)
                            <tr>
                                <td>{{ $txn->id }}</td>
                                <td>{{ $txn->user->name }}</td>
                                <td>{{ $txn->user->mobile_number }}</td>
                                <td>
                                    <span class="badge bg-{{ $txn->type == 1 ? 'success' : 'danger' }}">
                                        {{ $txn->type == 1 ? 'Credit' : 'Deduct' }}
                                    </span>
                                </td>
                                <td>{{ $txn->amount }}</td>
                                <td>{{ $txn->desc }}</td>
                                <td>{{ \Carbon\Carbon::parse($txn->created_at)->format('d-m-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $tnxlist->links() }}
                </div>
            </div>

        </div>
    </div>
</div>
