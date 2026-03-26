<div>
    <div class="card mb-4">
        <h5 class="card-header">Site Settings</h5>
        <div class="card-body">
            <form wire:submit.prevent="updateSettings">
                <div class="row mb-2">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Payment UPI Info</label>
                        <input type="text" class="form-control" wire:model.live="payment_upi_info" placeholder="Enter UPI ID">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Sportsmonk API Key</label>
                        <input type="text" class="form-control" wire:model.live="sportsmonk_api_key" placeholder="Enter API Key">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Update Settings</button>
            </form>
        </div>
    </div>

    <div class="card mb-4">
        <h5 class="card-header">Whatsapp OTP Info</h5>
        <div class="card-body">
            <form wire:submit.prevent="updateOtpInfo">
                <div class="row mb-2">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Access Token</label>
                        <input type="text" class="form-control" wire:model.live="accessToken" placeholder="Enter Access Token">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Phone Number ID</label>
                        <input type="number" class="form-control" wire:model.live="phoneid" placeholder="Enter Phone Number ID">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Templete</label>
                        <input type="text" class="form-control" wire:model.live="templete" placeholder="Enter Templete">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Expired At</label>
                        <input type="text" class="form-control" wire:model.live="expiredat" placeholder="Enter Expired At">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning">Update Otp Info</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Bonus Settings</h5>
        <div class="card-body">
            <form wire:submit.prevent="updateBonuses">
                <div class="row mb-2">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Refer Bonus</label>
                        <input type="number" class="form-control" wire:model.live="refer_bonus" placeholder="Enter Refer Bonus">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Signup Bonus</label>
                        <input type="number" class="form-control" wire:model.live="signup_bonus" placeholder="Enter Signup Bonus">
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Update Bonus Settings</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h5 class="card-header">Version Settings</h5>
        <div class="card-body">
            <form wire:submit.prevent="updateVersion">
                <div class="row mb-2">
                    <div class="col-sm-5 mb-3">
                        <label class="form-label">Version</label>
                        <input type="text" class="form-control" wire:model.live="version" placeholder="Enter Version">
                    </div>
                    <div class="col-sm-2 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" @if ($mendatory) checked @endif wire:model.live="mendatory">
                            <label class="form-check-label" for="cancellable">Is Mendatory</label>
                            @error('cancellable')
                                <div id="cancellable" class="form-text text-danger">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-sm-5 mb-3">
                        <label class="form-label">Link</label>
                        <input type="text" class="form-control" wire:model.live="link" placeholder="Enter link">
                    </div>
                </div>
                <button type="submit" class="btn btn-success">Update Version Settings</button>
            </form>
        </div>
    </div>
</div>
