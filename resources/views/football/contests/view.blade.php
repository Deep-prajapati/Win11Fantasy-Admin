@extends('layouts.app')
@section('contents')
    <div class="row">
        <div class="col-sm-12 col-md-5">
            <div class="row">
                <div class="col-sm-12 mb-2">
                    <div class="card">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <h5 class="card-title m-0 me-2">Contest Info ({{$contest->contest_type}})</h5>
                            {{-- <div class="dropdown">
                                <button class="btn p-0" type="button" id="transactionID" data-bs-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-dots-vertical-rounded"></i>
                                </button>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
                                    <a class="dropdown-item" href="javascript:void(0);">Last 28 Days</a>
                                    <a class="dropdown-item" href="javascript:void(0);">Last Month</a>
                                    <a class="dropdown-item" href="javascript:void(0);">Last Year</a>
                                </div>
                            </div> --}}
                        </div>
                        <div class="card-body">
                            <ul class="p-0 m-0">
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{asset('assets/img/icons/unicons/paypal.png')}}" alt="User" class="rounded">
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="text-muted d-block mb-1">Spots</small>
                                            <h6 class="mb-0">Total Spots</h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">{{$contest->total_spots}}</h6>
                                            <span class="text-muted">Team</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{asset('assets/img/icons/unicons/wallet.png')}}" alt="User" class="rounded">
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="text-muted d-block mb-1">Winning</small>
                                            <h6 class="mb-0">Total Winning ammount</h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">{{$contest->total_winning_prize}}</h6>
                                            <span class="text-muted">&#8377;</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{asset('assets/img/icons/unicons/wallet.png')}}" alt="User" class="rounded">
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="text-muted d-block mb-1">Max Teams</small>
                                            <h6 class="mb-0">Per User</h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">{{$contest->max_entries}}</h6>
                                            <span class="text-muted">Team</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{asset('assets/img/icons/unicons/chart.png')}}" alt="User" class="rounded">
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="text-muted d-block mb-1">Entry Fee</small>
                                            <h6 class="mb-0">Charged from user for join contest</h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">{{ number_format($contest->entry_fees, 2) }}</h6>
                                            <span class="text-muted">&#8377;</span>
                                        </div>
                                    </div>
                                </li>
                                <li class="d-flex mb-4 pb-1">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{asset('assets/img/icons/unicons/cc-success.png')}}" alt="User"
                                            class="rounded">
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="text-muted d-block mb-1">Bonus</small>
                                            <h6 class="mb-0">Max usable at joinning of team from user</h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">{{ number_format($contest->usable_bonus, 2) }}</h6>
                                            <span class="text-muted">&#8377;</span>
                                        </div>
                                    </div>
                                </li>

                                {{-- <li class="d-flex">
                                    <div class="avatar flex-shrink-0 me-3">
                                        <img src="{{asset('assets/img/icons/unicons/cc-warning.png')}}" alt="User"
                                            class="rounded">
                                    </div>
                                    <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                        <div class="me-2">
                                            <small class="text-muted d-block mb-1">Mastercard</small>
                                            <h6 class="mb-0">Ordered Food</h6>
                                        </div>
                                        <div class="user-progress d-flex align-items-center gap-1">
                                            <h6 class="mb-0">-92.45</h6>
                                            <span class="text-muted">USD</span>
                                        </div>
                                    </div>
                                </li> --}}
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex flex-column align-items-center gap-1">
                                    <h2 class="mb-2">&#8377; {{ $contest->total_winning_prize }} </h2>
                                    <span>Total Winnings</span>
                                </div>
                            </div>
                            <ul class="p-0 m-0">
                                @foreach ($contest->prizeBreakup as $data)
                                    <li class="d-flex mb-4 pb-1">
                                        <div class="avatar flex-shrink-0 me-3">
                                            <span class="avatar-initial rounded bg-label-primary">
                                                <i class="bx bx-award"></i>
                                            </span>
                                        </div>
                                        <div
                                            class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                            <div class="me-2">
                                                <h6 class="mb-0">
                                                    @if ($data->rank_from == $data->rank_upto)
                                                        For Rank {{ $data->rank_from }}
                                                    @else
                                                        For Rank {{ $data->rank_from }} To {{ $data->rank_upto }}
                                                    @endif
                                                </h6>
                                                <small class="text-muted">Winning Reward</small>
                                            </div>
                                            <div class="user-progress">
                                                <small class="fw-semibold">&#8377; {{ $data->prize_amount }}</small>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12 col-md-7">
            <div class="card">
                <h5 class="card-header">Default Contest Used Matches List</h5>
                <div class="card-body">
                    <form class="row" action="{{ route('admin.cricket.default.contest.view',$contest->id) }}">
                        <div class="row">
                            <div class="col-sm-4">
                                <div>
                                    <label for="match_id" class="form-label">Match id</label>
                                    <input type="number" min="0" class="form-control"
                                        value="{{ request()->get('match_id') }}" name="match_id" placeholder="Match Id">
                                </div>
                            </div>

                            <div class="col-sm-4">
                                <div>
                                    <label for="match_id" class="form-label">Match Date</label>
                                    <input type="date" class="form-control"
                                        value="{{ request()->get('date') }}" name="date" placeholder="Match date">
                                </div>
                            </div>

                            <div class="col-sm-4">
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

                        </div>
                        @if (request()->has('page'))
                            <input type="hidden" name="page" value="{{ request()->query('page') }}">
                        @endif
                        <div class="inline-block mt-2  mb-2">
                            <button class="btn btn-sm btn-primary mr-3">Apply Filters</button>
                            <a href="{{ route('admin.cricket.default.contest.view',$contest->id) }}" class="btn btn-sm btn-outline-danger">Clear</a>
                        </div>
                    </form>
                    <div class="table-responsive text-nowrap">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>S.NO.</th>
                                    <th>Match ID</th>
                                    <th>Match Date</th>
                                    <th>Match Status</th>
                                    <th>Filled Spots</th>
                                    <th>Empty Spots</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                                @if (count($usercontests) < 1)
                                    <tr>
                                        <td colspan="10">
                                            <div class="d-flex justify-content-center mt-3">
                                                No Match contest found for this contest.
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @foreach ($usercontests as $key => $data)
                                    <tr>
                                        <td class="text-center">{{ $usercontests->firstItem() + $key }}</td>
                                        <td class="text-center">{{ $data->match_id }}</td>
                                        <td class="text-center">{{ formatDateTime($data->starting_at) }}</td>
                                        <td class="text-center">{!! matchStatusBageByStatus($data->match_status) !!}</td>
                                        <td class="text-center">{{ $data->filled_spot }}</td>
                                        <td class="text-center">{{ $data->total_spots - $data->filled_spot }}</td>
                                        <td class="text-center">
                                            @if ($data->is_active == 1)
                                                <span class="badge bg-label-success">Active</span>
                                            @else
                                                <span class="badge bg-label-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-outline-danger">
                                                <i class="tf-icons bx bx-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                @if ($usercontests->hasPages())
                                    <tr>
                                        <td colspan="8">
                                            <div class="d-flex justify-content-center mt-3">
                                                {{ $usercontests->appends(request()->query())->links('pagination::bootstrap-4') }}
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
    </div>
@endsection
