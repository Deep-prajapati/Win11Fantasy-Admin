@extends('layouts.app')
@section('contents')
    <div class="card">
        <h5 class="card-header">Filters</h5>
        <div class="card-body">
            <form class="row" action="{{ route('admin.cricket.matches') }}">
                <div class="row">
                    <div class="col-sm-4 mb-3">
                        <div>
                            <label for="match_id" class="form-label">Match id</label>
                            <input type="number" min="0" class="form-control"
                                value="{{ request()->get('match_id') }}" name="match_id" placeholder="Match Id">
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <div>
                            <label for="match_id" class="form-label">Match Date</label>
                            <input type="date" class="form-control"
                                value="{{ request()->get('date') }}" name="date" placeholder="Match date">
                        </div>
                    </div>


                    <div class="col-sm-4 mb-3">
                        <div class="mb-3">
                            <label for="status_select" class="form-label">Status</label>
                            <select class="form-select" id="status_select" name="status" aria-label="Select Status">
                                <option value="">Select Match Status</option>
                                <option value="upcomming" {{ request()->get('status') == 'upcomming' ? 'selected' : '' }}>
                                    Upcomming</option>
                                <option value="live" {{ request()->get('status') == 'live' ? 'selected' : '' }}>Live
                                </option>
                                <option value="completed" {{ request()->get('status') == 'completed' ? 'selected' : '' }}>
                                    Completed</option>
                                <option value="cancelled" {{ request()->get('status') == 'cancelled' ? 'selected' : '' }}>
                                    Cancelled</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-sm-4 mb-3">
                        <div class="mb-3">
                            <label for="league_select" class="form-label">League</label>
                            <select class="form-select" id="league_select" name="league" aria-label="Select League">
                                <option value="">Select League</option>
                                @foreach ($leagues as $league)
                                    <option value="{{ $league->league_id }}"
                                        {{ request()->get('league') == $league->league_id ? 'selected' : '' }}>
                                        {{ $league->name }} ({{ $league->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-sm-4 mb-3">
                        <div class="mb-3">
                            <label for="season_select" class="form-label">Season</label>
                            <select class="form-select" id="season_select" name="season" id="seasons"
                                aria-label="Select League">
                                <option value="">Select Season</option>
                            </select>
                        </div>
                    </div>
                </div>
                @if (request()->has('page'))
                    <input type="hidden" name="page" value="{{ request()->query('page') }}">
                @endif
                <div class="inline-block mt-2">
                    <button class="btn btn-sm btn-primary mr-3">Apply Filters</button>
                    <a href="{{ route('admin.cricket.matches') }}" class="btn btn-sm btn-outline-danger">Clear</a>
                </div>
            </form>

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Match ID</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Round</th>
                            <th>League</th>
                            <th>Season</th>
                            <th>Status</th>
                            <th>Starting Time</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($matches) < 1)
                        <tr>
                            <td colspan="10">
                                <div class="d-flex justify-content-center mt-3">
                                    No match record found.
                                </div>
                            </td>
                        </tr>
                        @endif
                        @foreach ($matches as $key => $match)
                            <tr>
                                <td>
                                    {{ $matches->firstItem() + $key }}
                                </td>
                                <td>{{ $match->fixture_id }}</td>
                                <td>{{ $match->localteam_code . ' vs ' . $match->visitorteam_code }}</td>
                                <td>{{ $match->type }}</td>
                                <td>{{ $match->round }}</td>
                                <td>{{ $match->league->name }} ( {{ $match->league->code }})</td>
                                <td>{{ $match->season->name }} ( {{ $match->season->code }})</td>
                                <td>{!! matchStatusBage($match->status) !!}</td>
                                <td>{{ formatDateTime($match->starting_at) }}</td>
                                <td>
                                    Action
                                </td>
                            </tr>
                        @endforeach
                        @if ($matches->hasPages())
                            <tr>
                                <td colspan="10">
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $matches->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </td>
                            </tr>
                        @endif

                    </tbody>

                </table>

            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(document).ready(function() {
            // Store the previously selected season from query params
            var selectedSeason = '{{ request()->get('season') ?? '' }}';
            $('#league_select').on('change', function() {
                var leagueId = $(this).val();
                $('#season_select').html('<option value="">Select Season</option>');
                if (leagueId) {
                    $.ajax({
                        url: '{{ route('admin.cricket.getseasons') }}',
                        type: 'GET',
                        data: {
                            league: leagueId
                        },
                        success: function(response) {
                            $.each(response.data, function(index, season) {
                                var isSelected = selectedSeason == season.season_id ?
                                    'selected' : '';
                                $('#season_select').append('<option value="' + season
                                    .season_id + '" ' + isSelected + '>' + season
                                    .name + ' (' + season.code + ')</option>');
                            });
                        },
                        error: function() {
                            alert('Failed to fetch seasons.');
                        }
                    });
                }
            });
        });
    </script>
@endsection
