@extends('layouts.app')
@section('contents')
    <div class="card">
        <h5 class="card-header">records List</h5>
        <div class="card-body">

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Tnx ID</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>User</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($records) < 1)
                            <tr>
                                <td colspan="9">
                                    <div class="d-flex justify-content-center mt-3">
                                        No withdrawals record found.
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @foreach ($records as $key => $data)
                            <tr>
                                <td>
                                    {{ $records->firstItem() + $key }}
                                </td>
                                <td>{{ $data->id }}</td>
                                <td>
                                    <span class="badge bg-label-primary me-1">{{ $data->method }}</span>
                                <td>{{ number_format($data->amount, 2) }}</td>
                                <td>
                                    <div>
                                        <img src="{{ getUsersFilesUrl($data->user->image) }}" alt="user-avatar"
                                            class="d-block rounded" height="50" width="50" id="uploadedAvatar">
                                        <div class="data mt-2">
                                            <p class="m-0">{{ $data->user->name }}</p>
                                            <p class="m-0">
                                                {{ $data->user->country_code . ' ' . $data->user->mobile_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($data->method == 'UPI')
                                        <div class="demo-inline-spacing mt-3">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex align-items-center">
                                                    UPI Name : {{$data->details['upi_name']}}
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    UPI Id : {{$data->details['upi_id']}}
                                                </li>
                                            </ul>
                                        </div>
                                    @else
                                        <div class="demo-inline-spacing mt-3">
                                            <ul class="list-group">
                                                <li class="list-group-item d-flex align-items-center">
                                                    Bank Holder Name :  {{$data->details['bank_holder_name']}}
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    Bank Name:  {{$data->details['bank_name']}}
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    Bank Account Number:  {{$data->details['bank_account']}}
                                                </li>
                                                <li class="list-group-item d-flex align-items-center">
                                                    Bank IFSC Code :  {{$data->details['bank_ifsc']}}
                                                </li>
                                            </ul>

                                        </div>
                                    @endif
                                </td>
                                <td>{!! rechargePaymentStatus($data->status) !!}</td>
                                <td>{{ formatDateTime($data->created_at) }}</td>
                                <td>
                                    @if ($data->status == 1)
                                        <button type="button" class="btn btn-success mr-2 approveBtn"
                                            data-withdrawal-id="{{ $data->id }}">
                                            Approve
                                        </button>
                                        <button type="button" class="btn btn-danger mr-2 rejectBtn"
                                            data-withdrawal-id="{{ $data->id }}">
                                            Reject
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        @if ($records->hasPages())
                            <tr>
                                <td colspan="9">
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $records->appends(request()->query())->links('pagination::bootstrap-4') }}
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
            $('.approveBtn').on('click', function() {
                let rechargeId = $(this).data('withdrawal-id');
                let url = "{{ route('admin.withdawal.approve', ':id') }}".replace(':id', rechargeId);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log("Success:", response);
                        alert("Withdrawal Approved Successfully!");
                        $(`.approveBtn[data-withdrawal-id="${rechargeId}"]`)
                            .text("Approved")
                            .prop("disabled", true)
                            .removeClass("btn-success")
                            .addClass("btn-secondary");
                        $(`.rejectBtn[data-withdrawal-id="${rechargeId}"]`)
                            .text("Reject")
                            .prop("disabled", true)
                            .removeClass("btn-success")
                            .addClass("btn-secondary");
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                        alert("Something went wrong. Please try again.");
                    }
                });
            });
            $('.rejectBtn').on('click', function() {
                let rechargeId = $(this).data('withdrawal-id');
                let url = "{{ route('admin.withdawal.reject', ':id') }}".replace(':id', rechargeId);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log("Success:", response);
                        alert("Withdrawal Rejected Successfully!");
                        $(`.approveBtn[data-withdrawal-id="${rechargeId}"]`)
                            .text("Approved")
                            .prop("disabled", true)
                            .removeClass("btn-success")
                            .addClass("btn-secondary");
                        $(`.rejectBtn[data-withdrawal-id="${rechargeId}"]`)
                            .text("Reject")
                            .prop("disabled", true)
                            .removeClass("btn-success")
                            .addClass("btn-secondary");
                    },
                    error: function(xhr, status, error) {
                        console.error("Error:", xhr.responseText);
                        alert("Something went wrong. Please try again.");
                    }
                });
            });
        });
    </script>
@endsection
