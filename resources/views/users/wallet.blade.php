@extends('layouts.app')

@section('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">

    <style>
        .form-control:disabled, .form-control[readonly] {
            background-color: #fdfeff !important;
            opacity: 1;
            /* cursor: not-allowed; */
            color: #697a8d !important;
        }
    </style>
@endsection

@section('contents')
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">
            User /
        </span>
        Account
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">
                    User Details
                </h5>
                <!-- Account -->
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="{{ getUsersFilesUrl($user->image) }}" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                    </div>
                </div>
                <hr class="my-0">
                <div class="card-body">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label for="name" class="form-label">
                                    Name
                                </label>
                                <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">
                                    E-mail
                                </label>
                                <input class="form-control" type="text" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="john.doe@example.com">
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phoneNumber">
                                    Mobile Number
                                </label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">
                                        IN (+91)
                                    </span>
                                    <input type="text" id="phoneNumber" class="form-control" value="{{ $user->mobile_number ?? 'No Data' }}" readonly>
                                    <span class="input-group-text copy-btn" data-copy="{{ $user->mobile_number }}">
                                        <i class="tf-icons bx bx-copy"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary me-2">Save changes</button>
                                <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                            </div>
                            
                            <hr class="my-0 mb-4 mt-3">

                            <div class="mb-3 col-md-4">
                                <label for="bankHolderName" class="form-label">
                                    Bank Holder Name
                                </label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="text" id="bankHolderName" value="{{ $user->account->bank_holder_name ?? 'No Data' }}" readonly>
                                    <span class="input-group-text copy-btn" data-copy="{{ $user->account->bank_holder_name ?? '' }}">
                                        <i class="tf-icons bx bx-copy"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3 col-md-4">
                                <label for="bankName" class="form-label">
                                    Bank Name
                                </label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="text" id="bankName" value="{{ $user->account->bank_name ?? 'No Data' }}" readonly>
                                    <span class="input-group-text copy-btn" data-copy="{{ $user->account->bank_name ?? '' }}">
                                        <i class="tf-icons bx bx-copy"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3 col-md-4">
                                <label for="bankIFSC" class="form-label">
                                    Bank IFSC
                                </label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="text" id="bankIFSC" value="{{ $user->account->bank_ifsc ?? 'No Data' }}" readonly>
                                    <span class="input-group-text copy-btn" data-copy="{{ $user->account->bank_ifsc ?? '' }}">
                                        <i class="tf-icons bx bx-copy"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3 col-md-4">
                                <label for="bankAccountNo" class="form-label">
                                    Bank Account No.
                                </label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="text" id="bankAccountNo" value="{{ $user->account->bank_account ?? 'No Data' }}" readonly>
                                    <span class="input-group-text copy-btn" data-copy="{{ $user->account->bank_account ?? '' }}">
                                        <i class="tf-icons bx bx-copy"></i>
                                    </span>
                                </div>
                            </div>

                            <hr class="my-0 mb-4 mt-3">

                            <div class="mb-3 col-md-4">
                                <label for="upiid" class="form-label">
                                    UPI ID
                                </label>
                                <div class="input-group input-group-merge">
                                    <input class="form-control" type="text" id="upiid" value="{{ $user->account->upi_id ?? 'No Data' }}" readonly>
                                    <span class="input-group-text copy-btn" data-copy="{{ $user->account->upi_id ?? '' }}">
                                        <i class="tf-icons bx bx-copy"></i>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mb-3 col-md-4">
                                <label for="upiname" class="form-label">
                                    UPI Name
                                </label>
                                <div class="input-group input-group-merge"> 
                                    <input class="form-control" type="text" id="upiname" value="{{ $user->account->upi_name ?? 'No Data' }}" readonly>
                                    <span class="input-group-text copy-btn" data-copy="{{ $user->account->upi_name ?? '' }}">
                                        <i class="tf-icons bx bx-copy"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- /Account -->
            </div>

            <div class="card mb-4">
                <h5 class="card-header">Wallet Details</h5>
                <div class="card-body">
                    <form action="{{ route('admin.users.wallet', $user->id) }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mb-3 col-md-4">
                                <label for="bonus" class="form-label">
                                    Bonus
                                </label>
                                <input class="form-control" type="text" id="bonus" name="bonus" value="{{ old('bonus', $user->account->bonus) }}">
                            </div>
                            <div class="mb-3 col-md-4">
                                <label for="balance" class="form-label">
                                    Balance
                                </label>
                                <input class="form-control" type="text" id="balance" name="balance" value="{{ old('balance', $user->account->balance) }}">
                            </div>
                            <div class="mb-3 col-md-4">
                                <label class="form-label" for="winning">
                                    Winning
                                </label>
                                <input class="form-control" type="text" id="winning" name="winning" value="{{ old('winning', $user->account->winning) }}">
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="submit" class="btn btn-primary me-2">
                                Update
                            </button>
                            <button type="reset" class="btn btn-outline-secondary">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
                <!-- /Winning -->
            </div>

            <!-- <div class="card">
                <h5 class="card-header">Delete Account</h5>
                <div class="card-body">
                    <div class="mb-3 col-12 mb-0">
                        <div class="alert alert-warning">
                            <h6 class="alert-heading fw-bold mb-1">Are you sure you want to delete your account?</h6>
                            <p class="mb-0">Once you delete your account, there is no going back. Please be certain.</p>
                        </div>
                    </div>
                    {{-- <form id="formAccountDeactivation" onsubmit="return false"> --}}
                    <button type="submit" class="btn btn-danger deactivate-account">Deactivate Account</button>
                    {{-- </form> --}}
                </div>
            </div> -->
        </div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

    <script>
        document.querySelectorAll('.copy-btn').forEach(button => {
            button.addEventListener('click', () => {
                const textToCopy = button.getAttribute('data-copy');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    Toastify({
                        text: "Copied",
                        duration: 1000,
                        destination: "https://github.com/apvarun/toastify-js",
                        newWindow: true,
                        close: false,
                        gravity: "top", // `top` or `bottom`
                        position: "center", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                            background: "linear-gradient(to right, #00b09b, #96c93d)",
                        },
                        onClick: function(){} // Callback after click
                    }).showToast();
                }).catch(err => {
                    Toastify({
                        text: "Copied Failed",
                        duration: 1000,
                        destination: "https://github.com/apvarun/toastify-js",
                        newWindow: true,
                        close: false,
                        gravity: "top", // `top` or `bottom`
                        position: "center", // `left`, `center` or `right`
                        stopOnFocus: true, // Prevents dismissing of toast on hover
                        style: {
                            background: "linear-gradient(to right, #ff1616, #c934b2)",
                        },
                        onClick: function(){} // Callback after click
                    }).showToast();
                });
            });
        });
    </script>
@endsection
