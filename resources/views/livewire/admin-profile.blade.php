<div>
    <div class="card mb-4">
        <h5 class="card-header">Profile Details</h5>
        <div class="card-body">
            <div class="d-flex align-items-start align-items-sm-center gap-4">
                <img src="{{ asset($user->image) }}" alt="user-avatar" class="d-block rounded" height="100" width="100">

                <div class="button-wrapper">
                    <input type="file" wire:model="image" class="d-none" id="upload" accept="image/*">
                    <label for="upload" class="btn btn-primary me-2 mb-4">
                        <span class="d-none d-sm-block">Upload new photo</span>
                    </label>
                    <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                </div>
            </div>
        </div>

        <hr class="my-0">

        <div class="card-body">
            @if (session()->has('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input class="form-control" type="text" id="name" wire:model="name">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label for="email" class="form-label">E-mail</label>
                        <input class="form-control" type="text" id="email" wire:model="email">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3 col-md-6">
                        <label class="form-label" for="phoneNumber">Mobile Number</label>
                        <div class="input-group input-group-merge">
                            <span class="input-group-text">IN (+91)</span>
                            <input type="text" id="phoneNumber" wire:model="mobile_number" class="form-control"
                                placeholder="9876543210">
                        </div>
                        @error('mobile_number')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-primary me-2">Save changes</button>
                    <button type="reset" class="btn btn-outline-secondary">Cancel</button>
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <h5 class="card-header">Change Password</h5>
        <div class="card-body">
            <form wire:submit.prevent="updatePassword">
                <div class="row">
                    <div class="mb-3 col-md-6">
                        <div class="form-password-toggle mb-3">
                            <label class="form-label" for="current-password">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="current-password"
                                    wire:model="current_password" placeholder="············"
                                    aria-describedby="current-password-visibility">
                                <span id="current-password-visibility" class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            @error('current_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3 col-md-6">
                        <div class="form-password-toggle mb-3">
                            <label class="form-label" for="new-password">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="new-password" wire:model="new_password"
                                    placeholder="············" aria-describedby="new-password-visibility">
                                <span id="new-password-visibility" class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                            @error('new_password')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <diw class="row">
                    <div class="mb-3 col-md-6">
                        <div class="form-password-toggle mb-3">
                            <label class="form-label" for="confirm-password">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="confirm-password"
                                    wire:model="new_password_confirmation" placeholder="············"
                                    aria-describedby="confirm-password-visibility">
                                <span id="confirm-password-visibility" class="input-group-text cursor-pointer">
                                    <i class="bx bx-hide"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </diw>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
    </div>
</div>
