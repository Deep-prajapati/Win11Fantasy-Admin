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
        <h5 class="card-header">Otpless Info</h5>
        <div class="card-body">
            <form wire:submit.prevent="updateOtplessInfo">
                <div class="row mb-2">
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Client ID</label>
                        <input type="text" class="form-control" wire:model.live="clientId" placeholder="Enter Client ID">
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label class="form-label">Client Secret</label>
                        <input type="text" class="form-control" wire:model.live="clientSecret" placeholder="Enter Client Secret">
                    </div>
                </div>
                <button type="submit" class="btn btn-warning">Update Otpless Info</button>
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
</div>
