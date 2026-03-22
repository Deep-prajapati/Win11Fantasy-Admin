<div>
    <div class="alert alert-warning alert-dismissible w-100" role="alert" wire:offline>
        Whoops, your device has lost connection. The web page you are viewing is offline.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <div>
        <div class="row mt-2">
            <div class="col-sm-4 mb-3">
                <label for="match_id" class="form-label">Match ID</label>
                <input type="number" min="0" class="form-control" wire:model.live="match_id"
                    placeholder="Match Id">
            </div>

            <div class="col-sm-4 mb-3">
                <label for="date" class="form-label">Match Date</label>
                <input type="date" class="form-control" wire:model.live="date" placeholder="Match date">
            </div>

            <div class="col-sm-4 mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" wire:model.live="status">
                    <option value="">Select Match Status</option>
                    <option value="upcomming">Upcoming</option>
                    <option value="live">Live</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="col-sm-4 mb-3">
                <label for="league" class="form-label">League</label>
                <select class="form-select" wire:model.live="league">
                    <option value="">Select League</option>
                    @foreach ($leagues as $league)
                        <option value="{{ $league->league_id }}">{{ $league->name }} ({{ $league->short_code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-sm-4 mb-3">
                <label for="season" class="form-label">Season</label>
                <select class="form-select" wire:model.live="season">
                    <option value="">Select Season</option>
                    @foreach ($seasons as $season)
                        <option value="{{ $season->season_id }}">{{ $season->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>
        <button type="button" wire:click="clearFilters" class="btn btn-sm btn-outline-danger">Clear</button>
    </div>
    <div class="table-responsive text-nowrap mt-4">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th wire:click="sortBy('fixture_id')" style="cursor: pointer;">
                        Match ID
                        @if ($sortColumn === 'fixture_id')
                            <i class="bx bx-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Name</th>
                    {{-- <th wire:click="sortBy('type')" style="cursor: pointer;">
                        Type
                        @if ($sortColumn === 'type')
                            <i class="bx bx-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th> --}}
                    <th>
                        Leg
                    </th>
                    <th wire:click="sortBy('league_id')" style="cursor: pointer;">
                        League
                        @if ($sortColumn === 'league_id')
                            <i class="bx bx-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th wire:click="sortBy('season_id')" style="cursor: pointer;">
                        Season
                        @if ($sortColumn === 'season_id')
                            <i class="bx bx-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>
                        Status
                    </th>
                    <th wire:click="sortBy('starting_at')" style="cursor: pointer;">
                        Starting Time
                        @if ($sortColumn === 'starting_at')
                            <i class="bx bx-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                        @endif
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($matches as $key => $match)
                    <tr>
                        <td>{{ $matches->firstItem() + $key }}</td>
                        <td><a
                                href="{{ route('admin.football.match.contests.list', $match->match_id) }}">{{ $match->match_id }}</a>
                        </td>
                        <td>{{ $match->name }}</td>
                        <td>{{ $match->leg }}</td>
                        <td>{{ $match->league->name }} ({{ $match->league->short_code }})</td>
                        <td>{{ $match->season->name ?? 'NA' }}</td>
                        <td>{!! matchFootballStatusBage($match) !!}</td>
                        <td>{{ formatDateTime($match->starting_at) }}</td>
                        <td>
                            <div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                    data-bs-toggle="dropdown">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu">
                                    @if ($match->is_cancelled && $match->is_upcomming)
                                        <button class="dropdown-item" {{-- wire:confirm="Are you sure you want to restore this match?" --}}
                                            wire:click="restoreMatch({{ $match->match_id }})">
                                            <i class="bx bx-refresh me-1"></i> Restore
                                        </button>
                                    @endif
                                    @if (!$match->is_cancelled && !$match->is_completed && $match->is_upcomming)
                                        <button class="dropdown-item" {{-- wire:confirm="Are you sure you want to cancel this match?" --}}
                                            wire:click="cancelMatch({{ $match->match_id }})">
                                            <i class="bx bx-user-x me-1"></i> Cancel
                                        </button>
                                    @endif
                                    <a class="dropdown-item"
                                        href="{{ route('admin.cricket.match.contests.list', $match->match_id) }}">
                                        <i class="bx bx-list-ul me-1"></i> View Contests
                                    </a>

                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">No match record found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-3">
            {{ $matches->links() }}
        </div>
    </div>
</div>
