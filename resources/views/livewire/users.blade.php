<div>
    <div class="card">
        <h5 class="card-header">All User</h5>
        <div class="card-body">
            <div>
                <div class="row mb-2">
                    <div class="col-sm-4 mb-3">
                        <label for="name" class="form-label">User name</label>
                        <input type="text" class="form-control" wire:model.live="name" placeholder="name">
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" wire:model.live="email" placeholder="email">
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label for="mobile_number" class="form-label">Mobile No.</label>
                        <input type="number" min="0" class="form-control" wire:model.live="mobile_number"
                            placeholder="mobile number">
                    </div>
                    <div class="col-sm-4 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" wire:model.live="status">
                            <option value="">Select status</option>
                            <option value="active">Active</option>
                            <option value="blocked">Blocked</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-2 mb-4">
                        <button type="button" class="btn btn-primary" wire:click="ExportCsv">Export</button>
                    </div>

                    <div class="col-sm-2 mb-4">
                        <button type="button" wire:click="clearFilters" class="btn btn-sm btn-outline-danger">Clear</button>
                    </div>
                </div>
                
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Wallets</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @forelse ($users as $key => $user)
                            <tr>
                                <td>
                                    {{ $users->firstItem() + $key }}
                                </td>
                                <td>
                                    <img height="40" width="40" src="{{ getUsersFilesUrl($user->image) }}" alt="{{ $user->name ?? 'NA' }}">
                                </td>
                                <td>{{ $user->name ?? 'NA' }}</td>
                                <td>{{ $user->email ?? 'NA' }}</td>
                                <td>
                                    {{ ($user->country_code ?? 'NA') . ' ' . ($user->mobile_number ?? 'NA') }}
                                </td>
                                <td>
                                    <div><strong>Balance:</strong> {{ number_format($user->account->balance ?? 0, 2) }}</div>
                                    <div><strong>Bonus:</strong> {{ number_format($user->account->bonus ?? 0, 2) }}</div>
                                    <div><strong>Winning:</strong> {{ number_format($user->account->winning ?? 0, 2) }}</div>
                                </td>
                                <td>
                                    {!! userStatusBage($user) !!}
                                </td>
                                <td>
                                    <div class="dropdown">
                                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="bx bx-dots-vertical-rounded"></i>
                                        </button>
                                        <div class="dropdown-menu" style="">
                                            @if ($user->is_banned)
                                                <button type="button" class="dropdown-item"
                                                    wire:click="unblockUser({{ $user->id }})">
                                                    <i class="bx bx-user-check me-1"></i>
                                                    Unblock
                                                </button>
                                            @else
                                                <button type="button" class="dropdown-item"
                                                    wire:click="blockUser({{ $user->id }})">
                                                    <i class="bx bx-user-x me-1"></i>
                                                    Block
                                                </button>
                                            @endif
                                            <a class="dropdown-item" href="{{ route('admin.users.view', $user->id) }}">
                                                <i class="bx bx-user me-1"></i>
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    <strong>No records found.</strong>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
