<?php

namespace App\Livewire;

use App\Models\SiteSettings as SettingsModel;
use Flasher\Laravel\Facade\Flasher;
use Livewire\Component;

class SiteSettings extends Component
{
    public $payment_upi_info;
    public $sportsmonk_api_key;
    public $clientId;
    public $clientSecret;
    public $refer_bonus;
    public $signup_bonus;

    public function mount()
    {
        // Get general settings
        $this->payment_upi_info = SettingsModel::getValue('payment_upi_info');
        $this->sportsmonk_api_key = SettingsModel::getValue('sportsmonk_api_key');

        // Get otpless info
        $otplessInfo = json_decode(SettingsModel::getValue('otpless_info'), true);
        $this->clientId = $otplessInfo['clientId'] ?? '';
        $this->clientSecret = $otplessInfo['clientSecret'] ?? '';

        // Get bonuses
        $this->refer_bonus = SettingsModel::getValue('refer_bonus');
        $this->signup_bonus = SettingsModel::getValue('signup_bonus');
    }

    public function updateSettings()
    {
        // Update general settings
        SettingsModel::updateOrCreate(['name' => 'payment_upi_info'], ['value' => $this->payment_upi_info]);
        SettingsModel::updateOrCreate(['name' => 'sportsmonk_api_key'], ['value' => $this->sportsmonk_api_key]);

        Flasher::success('General settings updated successfully.');
    }

    public function updateOtplessInfo()
    {
        // Save otpless info as JSON
        $json = json_encode([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
        ]);

        SettingsModel::updateOrCreate(['name' => 'otpless_info'], ['value' => $json]);

        Flasher::success('Otpless info updated successfully.');
    }

    public function updateBonuses()
    {
        // Update the bonuses
        SettingsModel::updateOrCreate(['name' => 'refer_bonus'], ['value' => $this->refer_bonus]);
        SettingsModel::updateOrCreate(['name' => 'signup_bonus'], ['value' => $this->signup_bonus]);

        Flasher::success('Bonus settings updated successfully.');
    }

    public function render()
    {
        return view('livewire.site-settings');
    }
}
