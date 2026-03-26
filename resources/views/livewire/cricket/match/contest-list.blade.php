<div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
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
                            <th>Filled Spots</th>
                            <th>MAX Team Entry</th>
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
                                <td><a
                                        href="{{ route('admin.cricket.match.contests.view', ['fixture_id' => $fixture->fixture_id, 'contest_id' => $contest->id]) }}">{{ $contest->id }}</a>
                                </td>
                                <td>{{ $contest->contestType->contest_type ?? '' }}</td>
                                <td>&#8377; {{ $contest->entry_fees }}</td>
                                <td>&#8377; {{ $contest->usable_bonus }}</td>
                                <td>&#8377; {{ $contest->total_winning_prize }}</td>
                                <td>{{ $contest->total_spots }}</td>
                                <td>{{ $contest->filled_spot }}</td>
                                <td>{{ $contest->contestType->max_entries ?? '' }} <span class="small text-info">*Per
                                        User</span></td>
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
                                    @if ($contest->is_cancelled)
                                    <span class="badge bg-label-danger">Cancelled</span>
                                    @else
                                    <span class="badge bg-label-success">Active</span>
                                    @endif
                                </td>

                                <td>
                                    @if (!$contest->is_cancelled && $contest->cancellation)
                                    <button wire:click="confirmCancel({{ $contest->id }})" type="button"
                                        class="btn btn-outline-danger">
                                        Cancel
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