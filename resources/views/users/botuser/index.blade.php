@extends('layouts.app')
@section('contents')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Bot User</h5>
            <a href="{{route('admin.users.bots.add')}}" class="btn btn-primary btn-sm">Add Bot</a>
        </div>
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
                                <img src="{{ asset($user->image) }}" alt="{{ $user->name }}">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email ?? 'NA' }}</td>
                            <td>{{ $user->country_code . ' ' . $user->mobile_number }}</td>
                            <td>
                                {!! userStatusBage($user) !!}
                            </td>
                            <td>
                                <a href="{{ route('admin.users.bots.status', $user->id) }}"
                                    class="btn @if ($user->is_banned) btn-primary @else
                                    btn-danger @endif">
                                    @if ($user->is_banned)
                                        Activate
                                    @else
                                        Block
                                    @endif
                                </a>
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
    </div>
@endsection
