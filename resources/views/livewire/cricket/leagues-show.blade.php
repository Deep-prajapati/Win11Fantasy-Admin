<div>
    <div class="card">
        <div class="card-body">
            <div class="table-responsive text-nowrap mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>S. No.</th>
                            <th wire:click="sortBy('league_id')" style="cursor: pointer;">
                                League ID
                                @if ($sortColumn === 'league_id')
                                    @if ($sortDirection === 'asc')
                                        <i class="fas fa-sort-up"></i>
                                    @else
                                        <i class="fas fa-sort-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th wire:click="sortBy('name')" style="cursor: pointer;">
                                Name
                                @if ($sortColumn === 'name')
                                    <i class="bx bx-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                            <th wire:click="sortBy('code')" style="cursor: pointer;">
                                Code
                                @if ($sortColumn === 'code')
                                    @if ($sortDirection === 'asc')
                                        <i class="fas fa-sort-up"></i>
                                    @else
                                        <i class="fas fa-sort-down"></i>
                                    @endif
                                @endif
                            </th>
                            <th wire:click="sortBy('status')" style="cursor: pointer;">
                                Status
                                @if ($sortColumn === 'status')
                                    <i class="bx bx-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($leagues as $key => $data)
                            <tr>
                                <td>{{ $leagues->firstItem() + $key }}</td>
                                <td>{{ $data->league_id }}</td>
                                <td>{{ $data->name }}</td>
                                <td>{{ $data->code }}</td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active_{{ $data->id }}"
                                            wire:click="toggleStatus({{ $data->id }})"
                                            @if ($data->status) checked @endif>
                                        <label class="form-check-label" for="is_active_{{ $data->id }}">
                                            {{ $data->status ? 'Active' : 'Inactive' }}
                                        </label>
                                    </div>
                                </td>
        
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No record found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
        
                <div class="mt-3">
                    {{ $leagues->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
