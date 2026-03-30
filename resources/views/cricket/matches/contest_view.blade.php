@extends('layouts.app')

@section('contents')
    <div class="row mb-2">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-4">
                            <div class="card-body">
                                <h5 class="card-title text-primary">{{ $match->league->name }} ( {{ $match->round }})</h5>
                                <div class="container mt-4">
                                    <div class="row  text-center">
                                        <div class="col-5 text-start">
                                            <img src="{{ $match->localteam_image_path }}" height="50" width="50"
                                                alt="Local Team" class="img-fluid rounded">
                                            <p class="mt-2 fw-bold">{{ $match->localteam_code }}</p>
                                        </div>
                                        <div class="col-2 text-center">
                                            <p class="h4 fw-bold">VS</p>
                                        </div>
                                        <div class="col-5 text-end">
                                            <img src="{{ $match->visitorteam_image_path }}" height="50" width="50"
                                                alt="Visitor Team" class="img-fluid rounded">
                                            <p class="mt-2 fw-bold">{{ $match->visitorteam_code }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 text-center">
                            <div class="card-body pb-0 px-0 px-md-4">
                                <img src="{{ $match->league->image_path }}" height="140" alt="Match"
                                    data-app-dark-img="{{ $match->league->image_path }}"
                                    data-app-light-img="{{ $match->league->image_path }}">
                            </div>
                        </div>
                        <div class="col-sm-5 text-end">
                            <p>Contest : {{$contest->id }}</p>
                            <p>Contest Type : {{$contest->contestType->contest_type ?? ''}}</p>
                            <p>Total Spots : {{$contest->total_spots}}</p>
                            <p>Filled Spots : {{$contest->filled_spot}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    </div>
                    <span class="fw-semibold d-block mb-1">Total winning prize</span>
                    <h3 class="card-title mb-2">&#8377; {{ number_format($contest->total_winning_prize, 2) }}</h3>
                    {{-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small> --}}
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    </div>
                    <span class="fw-semibold d-block mb-1">Total Joined Fee</span>
                    <h3 class="card-title mb-2">&#8377; {{ number_format($totalEntryAmount, 2) }}</h3>
                    {{-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small> --}}
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    </div>
                    <span class="fw-semibold d-block mb-1">User Winning Prize</span>
                    <h3 class="card-title mb-2">&#8377; {{ number_format($totalWinnings, 2) }}</h3>
                    {{-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small> --}}
                </div>
            </div>
        </div>
        <div class="col-sm-3">
            <div class="card">
                <div class="card-body">
                    <div class="card-title d-flex align-items-start justify-content-between">
                    </div>
                    <span class="fw-semibold d-block mb-1">Profit</span>
                    <h3
                        class="card-title mb-2
                    {{ $totalEntryAmount - $totalWinnings < 0 ? 'text-danger' : 'text-success' }}">
                        &#8377; {{ number_format($totalEntryAmount - $totalWinnings, 2) }}
                    </h3>
                </div>
            </div>
        </div>
    </div>
    <div class="row mb-2">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>S.NO.</th>
                                <th>User</th>
                                <th>Mobile No.</th>
                                <th>Entry Fee</th>
                                <th>Team</th>
                                <td>Team Points</td>
                                <th>Rank</th>
                                <th>Winnings</th>
                                <th>User Type</th>
                                <td>Status</td>
                            </tr>
                        </thead>
                        <tbody class="table-border-bottom-0">
                            @if (count($joinedUsers) < 1)
                                <tr>
                                    <td colspan="10">
                                        <div class="d-flex justify-content-center mt-3">
                                            No default Contest record found.
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @foreach ($joinedUsers as $key => $data)
                                <tr>
                                    <td>{{ $joinedUsers->firstItem() + $key }}</td>
                                    <td>
                                        <div class="d-flex flex-column justify-contant-center align-items-center">
                                            <img src="{{ getUsersFilesUrl($data->user->image) }}" alt="user-avatar"
                                                class="d-block rounded" height="50" width="50" id="uploadedAvatar">
                                            <div class="data mt-2 text-center">
                                                <p>{{ $data->user->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $data->user->country_code . ' ' . $data->user->mobile_number }}</td>
                                    <td>&#8377; {{ $contest->entry_fees }} </td>
                                    <td>{{ $data->user->name }} ({{ $data->team_count }})</td>
                                    <td>
                                        <a href="{{ route('admin.cricket.match.contests.team.view', ['fixture_id' => $match->fixture_id, 'contest_id' => $contest->id, 'team_id' => $data->team->id]) }}">
                                            {{ $data->points }}
                                        </a>
                                    </td>
                                    <td>#{{ $data->ranks }}</td>
                                    <td>&#8377; {{ $data->winning_amount }}</td>
                                    <td>{{ $data->user->role == 3 ? 'BOT' : 'User' }}</td>
                                    <td>
                                        @if ($match->is_completed && $match->is_prize_distributed)
                                            <span class="badge bg-success">Prize Distributed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if ($joinedUsers->hasPages())
                                <tr>
                                    <td colspan="10">
                                        <div class="d-flex justify-content-center mt-3">
                                            {{ $joinedUsers->appends(request()->query())->links('pagination::bootstrap-4') }}
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
@endsection