<div>
    <div class="card">
        <h5 class="card-header">All Default Contest List</h5>
        <div class="card-body">
            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.NO.</th>
                            <th>Contest ID</th>
                            <th>Contest type</th>
                            <th>Entry Fee</th>
                            <th>Usable bonus</th>
                            <th>Total winning prize</th>
                            <th>Total Spots</th>
                            <th>Max Bots</th>
                            <th>MAX Team Entry</th>
                            <th>Flexiable</th>
                            <th>Free</th>
                            <th>Allowed Cancellation</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($contests) < 1)
                            <tr>
                            <td colspan="10">
                                <div class="d-flex justify-content-center mt-3">
                                    No default Contest record found.
                                </div>
                            </td>
                            </tr>
                            @endif
                            @foreach ($contests as $key => $contest)
                            <tr>
                                <td>{{ $contests->firstItem() + $key }}</td>
                                <td>{{ $contest->id }}</td>
                                <td>{{ $contest->contestType->contest_type ?? '' }}</td>
                                <td>&#8377; {{ $contest->entry_fees }}</td>
                                <td>&#8377; {{ $contest->usable_bonus }}</td>
                                <td>
                                    @if($contest->is_felexible)
                                        {{ $contest->total_winning_prize }} %
                                    @else
                                        &#8377; {{ $contest->total_winning_prize }}
                                    @endif
                                </td>
                                <td>{{ $contest->total_spots }}</td>
                                <td>{{ $contest->bot_user }}</td>
                                <td>
                                    {{ $contest->contestType->max_entries ?? '' }} 
                                    <span class="small text-info">
                                        *Per User
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if ($contest->is_felexible)
                                        <span class="badge bg-label-success">Yes</span>
                                    @else
                                        <span class="badge bg-label-danger">NO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($contest->is_free)
                                    <span class="badge bg-label-success">Yes</span>
                                    @else
                                    <span class="badge bg-label-danger">NO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($contest->cancellation)
                                    <span class="badge bg-label-success">Yes</span>
                                    @else
                                    <span class="badge bg-label-danger">NO</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if ($contest->is_deleted)
                                    <span class="badge bg-label-danger">Deleted</span>
                                    @else
                                    <span class="badge bg-label-success">Active</span>
                                    @endif
                                </td>
                                <td>
                                    <a class="btn btn-outline-primary"
                                        href="{{ route('admin.cricket.default.contest.view', $contest->id) }}">
                                        <i class="tf-icons bx bx-detail"></i>
                                    </a>
                                    @if ($contest->cancellation && !$contest->is_deleted)
                                    <a href="{{ route('admin.cricket.default.contest.edit', $contest->id) }}"
                                        class="btn btn-outline-secondary">
                                        <i class="tf-icons bx bx-edit-alt"></i>
                                    </a>
                                    @endif
                                    @if ($contest->cancellation && !$contest->is_deleted)
                                    <button type="button" class="btn btn-outline-danger"
                                        wire:click="confirmDelete({{ $contest->id }})">
                                        <i class="tf-icons bx bx-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @if ($contests->hasPages())
                            <tr>
                                <td colspan="10">
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $contests->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </td>
                            </tr>
                            @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>