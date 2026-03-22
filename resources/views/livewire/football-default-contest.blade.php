<div>
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
                        <th>Free</th>
                        <th>Allowed Cancellation</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @forelse ($contests as $key => $contest)
                        <tr>
                            <td>{{ $contests->firstItem() + $key }}</td>
                            <td>{{ $contest->id }}</td>
                            <td>{{ $contest->contest_type }}</td>
                            <td>&#8377; {{ $contest->entry_fees }}</td>
                            <td>&#8377; {{ $contest->usable_bonus }}</td>
                            <td>&#8377; {{ $contest->total_winning_prize }}</td>
                            <td>{{ $contest->total_spots }}</td>
                            <td>{{ $contest->bot_user }}</td>
                            <td>{{ $contest->max_entries }} <span class="small text-info">*Per User</span></td>
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

                            <td>
                                <a class="btn btn-outline-primary"
                                    href="{{ route('admin.football.default.contest.view', $contest->id) }}">
                                    <i class="tf-icons bx bx-detail"></i>
                                </a>
                                <a href="{{ route('admin.football.default.contest.edit', $contest->id) }}"
                                    class="btn btn-outline-secondary">
                                    <i class="tf-icons bx bx-edit-alt"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger"
                                    wire:click="confirmDelete({{ $contest->id }})">
                                    <i class="tf-icons bx bx-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">
                                <div class="d-flex justify-content-center mt-3">
                                    No default Contest record found.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
        <div class="mt-3">
            {{ $contests->links() }}
        </div>
    </div>
</div>
