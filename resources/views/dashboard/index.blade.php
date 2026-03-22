@extends('layouts.app')
@section('scripts')
    <script src="{{ asset('assets/js/dashboards-analytics.js') }}"></script>
@endsection
@section('styles')
@endsection
@section('contents')
    <div class="row">
        <div class="col-lg-6 col-md-8 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Congratulations {{ $user->name }}! 🎉</h5>
                            <p class="mb-4">
                                <span class="fw-bold">{{ $todayUserCount }}</span> new users have registered today.
                                Welcome them to the community!
                            </p>
                            <a href="{{route('admin.users.list')}}" class="btn btn-sm btn-outline-primary">View Users</a>
                        </div>
                    </div>
                    <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                            <img src="{{ asset('assets/img/illustrations/man-with-laptop-light.png') }}" height="140"
                                alt="View Badge User" data-app-dark-img="illustrations/man-with-laptop-dark.png')}}"
                                data-app-light-img="illustrations/man-with-laptop-light.png')}}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-4 order-1">
            <div class="row">
                <div class="col-lg-4 col-md-12 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/chart-success.png') }}" alt="chart success"
                                        class="rounded" />
                                </div>
                                {{-- <div class="dropdown">
                  <button  class="btn p-0"  type="button"  id="cardOpt3"  data-bs-toggle="dropdown"  aria-haspopup="true"  aria-expanded="false">
                    <i class="bx bx-dots-vertical-rounded"></i>
                  </button>
                  <div  class="dropdown-menu dropdown-menu-end"  aria-labelledby="cardOpt3">
                    <a class="dropdown-item" href="javascript:void(0);"  >View More</a>
                    <a class="dropdown-item" href="javascript:void(0);">Delete</a>
                  </div>
                </div> --}}
                            </div>
                            <span class="fw-semibold d-block mb-1">Total Users</span>
                            <h3 class="card-title mb-2">{{ $totalUsers }}</h3>
                            <small class="text-success fw-semibold">
                                @if ($totalUsers > 0 && $todayUserCount > 0)
                                    @php $increase = ($todayUserCount / $totalUsers) * 100; @endphp
                                    <i class="bx bx-up-arrow-alt"></i> +{{ round($increase, 2) }} %
                                @else
                                    0 %
                                @endif
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/paypal.png') }}" alt="Credit Card"
                                        class="rounded" />
                                </div>
                            </div>
                            <span class="d-block mb-1">Total Deposits</span>
                            <h3 class="card-title text-nowrap mb-2">&#8377; {{ $totalDeposit }}</h3>
                            <small class="text-danger fw-semibold"><i class="bx bx-down-arrow-alt"></i> -14.82%</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-sm-12 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                                <div class="avatar flex-shrink-0">
                                    <img src="{{ asset('assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card"
                                        class="rounded" />
                                </div>
                            </div>
                            <span>Payment Pending</span>
                            <h3 class="card-title text-nowrap mb-1">&#8377; {{ $pendingDeposit }}</h3>
                            <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.42%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
