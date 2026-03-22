@extends('layouts.app')
@section('contents')
    <div class="card">
        <h5 class="card-header">Recharges List</h5>
        <div class="card-body">

            <div class="table-responsive text-nowrap">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Tnx ID</th>
                            <th>Order ID / UTR NO.</th>
                            <th>Method</th>
                            <th>Amount</th>
                            <th>User</th>
                            <th>Mobile No.</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        @if (count($recharges) < 1)
                            <tr>
                                <td colspan="9">
                                    <div class="d-flex justify-content-center mt-3">
                                        No Recharge record found.
                                    </div>
                                </td>
                            </tr>
                        @endif
                        @foreach ($recharges as $key => $data)
                            <tr>
                                <td>
                                    {{ $recharges->firstItem() + $key }}
                                </td>
                                <td>{{ $data->tnx_id }}</td>
                                <td>
                                    @if ($data->method == 1)
                                        {{ $data->utr_no }}
                                    @else
                                        {{ $data->order_id }}
                                    @endif
                                </td>
                                <td>
                                    @if ($data->method == 1)
                                        <span class="badge bg-label-primary me-1">Manual</span>
                                    @else
                                        <span class="badge bg-label-success me-1">Payment GateWay</span>
                                    @endif
                                </td>
                                <td>{{ number_format($data->amount, 2) }}</td>
                                <td>
                                    <div class="text-center">
                                        <img src="{{ getUsersFilesUrl($data->user->image) }}" alt="user-avatar"
                                            class="d-block rounded" height="50" width="50" id="uploadedAvatar">
                                        <div class="data mt-2">
                                            <p>{{ $data->user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $data->user->country_code . ' ' . $data->user->mobile_number }}</td>
                                <td>{!! rechargePaymentStatus($data->status) !!}</td>
                                <td>{{ formatDateTime($data->created_at) }}</td>
                                <td>
                                    @if ($data->status == 1 && $data->method == 1)
                                        <button type="button" class="btn btn-success mr-2 approveBtn"
                                            data-recharge-id="{{ $data->id }}"
                                            data-prof="{{ getUsersFilesUrl($data->image) }}">
                                            Approve
                                        </button>
                                        <button type="button" class="btn btn-danger mr-2 rejectBtn"
                                            data-recharge-id="{{ $data->id }}"
                                            data-prof="{{ getUsersFilesUrl($data->image) }}">
                                            Reject
                                        </button>
                                    @else
                                        NA
                                    @endif

                                </td>
                            </tr>
                        @endforeach
                        @if ($recharges->hasPages())
                            <tr>
                                <td colspan="9">
                                    <div class="d-flex justify-content-center mt-3">
                                        {{ $recharges->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Bootstrap Modal -->
    {{-- <div class="modal fade" id="modalCenter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">UTR NO. : </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Payment Image" class="img-fluid rounded"
                        style="max-width: 100%; height: auto;">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div> --}}
    {{-- <div class="modal fade" id="modalRechargeAction" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalRechargeActionTitle">Payment Action</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="action">
                    <div class="mb-2">
                        <label for="remark" class="form-label">Remark</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div> --}}
    </div>
@endsection
{{--  $('.viewBtn').on('click', function() {
                let imageUrl = $(this).data('prof');
                $('#modalCenterTitle').text('UTR NO. : ' + $(this).data('utr'));
                $('#modalImage').attr('src', imageUrl);
                $('#modalCenter').modal('show');
            });
             --}}
@section('scripts')
    <script>
        $(document).ready(function() {
            $('.approveBtn').on('click', function() {
                let rechargeId = $(this).data('recharge-id');
                let url = "{{ route('admin.recharge.approve', ':id') }}".replace(':id', rechargeId);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log("Success:", response);
                        alert("Recharge Approved Successfully!");
                        $(`.approveBtn[data-recharge-id="${rechargeId}"]`)
                            .text("Approved")
                            .prop("disabled", true)
                            .removeClass("btn-success")
                            .addClass("btn-secondary");
                        $(`.rejectBtn[data-recharge-id="${rechargeId}"]`)
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
                let rechargeId = $(this).data('recharge-id');
                let url = "{{ route('admin.recharge.reject', ':id') }}".replace(':id', rechargeId);
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        _token: csrfToken
                    },
                    success: function(response) {
                        console.log("Success:", response);
                        alert("Recharge Rejected Successfully!");
                        $(`.approveBtn[data-recharge-id="${rechargeId}"]`)
                            .text("Approved")
                            .prop("disabled", true)
                            .removeClass("btn-success")
                            .addClass("btn-secondary");
                        $(`.rejectBtn[data-recharge-id="${rechargeId}"]`)
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
