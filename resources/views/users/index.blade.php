@extends('layouts.app')
@section('styles')
    @livewireStyles
@endsection
@section('contents')
    {{-- <div class="card">
        <h5 class="card-header">All User</h5>
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody class="table-border-bottom-0">
                    @foreach ($users as $key => $user)
                        <tr>
                            <td>
                                {{ $users->firstItem() + $key }}
                            </td>
                            <td>
                                <img height="40" width="40" src="{{ getUsersFilesUrl($user->image) }}"
                                    alt="{{ $user->name }}">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email ?? 'NA' }}</td>
                            <td>{{ $user->country_code . ' ' . $user->mobile_number }}</td>
                            <td>
                                {!! userStatusBage($user) !!}
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu" style="">
                                        @if ($user->is_banned)
                                        <a class="dropdown-item" href="{{route('admin.users.unblock',$user->id)}}">
                                            <i class="bx bx-user-check me-1"></i>
                                            Unblock
                                        </a>
                                        @else
                                        <a class="dropdown-item" href="{{route('admin.users.block',$user->id)}}">
                                            <i class="bx bx-user-x me-1"></i>
                                            Block
                                        </a>
                                        @endif
                                        <a class="dropdown-item" href="{{route('admin.users.view',$user->id)}}">
                                            <i class="bx bx-user me-1"></i>
                                            View
                                        </a>

                                    </div>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                    @if ($users->hasPages())
                        <tr>
                            <td colspan="5">
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $users->links('pagination::bootstrap-4') }}
                                </div>
                            </td>
                        </tr>
                    @endif
                </tbody>

            </table>

        </div>
    </div> --}}
    @livewire('users')
@endsection
@section('scripts')
    @livewireScripts
@endsection
