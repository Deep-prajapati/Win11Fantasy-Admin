<div>
    <div class="card">
        <div class="card-header d-flex align-items-center justify-content-between">
            <h5 class="card-title m-0 me-2">All Contest types</h5>
            <a href="{{ route('admin.cricket.contest.type.add') }}" class="btn btn-primary">Add New</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S.NO.</th>
                            <th>ID</th>
                            <th>Contest type</th>
                            <th>Description.</th>
                            <th>MAX Team Entry</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($contestTypes) < 1)
                            <tr>
                                <td colspan="10">
                                    <div class="d-flex justify-content-center mt-3">
                                        No default Contest record found.
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @foreach ($contestTypes as $key => $data)
                            <tr>
                                <td>{{ $contestTypes->firstItem() + $key }}</td>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->contest_type }}</td>
                                <td>{{ $data->description }}</td>
                                <td>{{ $data->max_entries }} <span class="small text-info">*Per User</span></td>
                                <td class="text-center">
                                    @if ($data->cancellable == 'true')
                                        <span class="badge bg-label-danger">Cancellable</span>
                                    @else
                                        <span class="badge bg-label-success">No Cancellable</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($data->is_deleted)
                                        <span class="badge bg-label-danger">Deleted</span>
                                    @else
                                        <span class="badge bg-label-success">Active</span>
                                    @endif
                                </td>
<td>
    @if ($data->cancellable && !$data->is_deleted)
        <button type="button" class="btn btn-outline-danger"
            wire:click="confirmDelete({{ $data->id }})">
            <i class="tf-icons bx bx-trash"></i>
        </button>
    @endif
</td>

                            </tr>
                        @endforeach
                        @if ($contestTypes->hasPages())
                            <tr>
                                <td colspan="10">
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $contestTypes->appends(request()->query())->links('pagination::bootstrap-4') }}
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
