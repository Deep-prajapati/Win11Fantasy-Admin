<?php

namespace App\Livewire;

use App\Models\SiteSettings as SettingsModel;
use Flasher\Laravel\Facade\Flasher;
use Livewire\Component;

class SiteSettings extends Component
{
    public $payment_upi_info;
    public $sportsmonk_api_key;
    public $accessToken;
    public $phoneid;
    public $templete;
    public $expiredat;
    public $refer_bonus;
    public $signup_bonus;

    public $version;
    public $mendatory;
    public $link;

    public function mount()
    {
        // Get general settings
        $this->payment_upi_info = SettingsModel::getValue('payment_upi_info');
        $this->sportsmonk_api_key = SettingsModel::getValue('sportsmonk_api_key');

        // Get otpless info
        $otpInfo = json_decode(SettingsModel::getValue('otp_info'), true);
        $this->accessToken = $otpInfo['accessToken'] ?? '';
        $this->phoneid = $otpInfo['phoneid'] ?? '';
        $this->templete = $otpInfo['templete'] ?? '';
        $this->expiredat = $otpInfo['expiredat'] ?? '';

        // Get bonuses
        $this->refer_bonus = SettingsModel::getValue('refer_bonus');
        $this->signup_bonus = SettingsModel::getValue('signup_bonus');

        // Get otpless info
        $version = json_decode(SettingsModel::getValue('version'), true);
        $this->version = $version['version'] ?? '';
        $this->mendatory = $version['mendatory'] ?? '';
        $this->link = $version['link'] ?? '';
    }

    public function updateSettings()
    {
        // Update general settings
        SettingsModel::updateOrCreate(['name' => 'payment_upi_info'], ['value' => $this->payment_upi_info]);
        SettingsModel::updateOrCreate(['name' => 'sportsmonk_api_key'], ['value' => $this->sportsmonk_api_key]);

        Flasher::success('General settings updated successfully.');
    }

    public function updateOtpInfo()
    {
        // Save otpless info as JSON
        $json = json_encode([
            'accessToken' => $this->accessToken,
            'phoneid' => $this->phoneid,
            'templete' => $this->templete,
            'expiredat' => $this->expiredat,
        ]);

        SettingsModel::updateOrCreate(['name' => 'otp_info'], ['value' => $json]);

        Flasher::success('Otp info updated successfully.');
    }

    public function updateBonuses()
    {
        // Update the bonuses
        SettingsModel::updateOrCreate(['name' => 'refer_bonus'], ['value' => $this->refer_bonus]);
        SettingsModel::updateOrCreate(['name' => 'signup_bonus'], ['value' => $this->signup_bonus]);

        Flasher::success('Bonus settings updated successfully.');
    }

    public function updateVersion()
    {
        // Save otpless info as JSON
        $json = json_encode([
            'version' => $this->version,
            'mendatory' => $this->mendatory,
            'link' => $this->link,
        ]);

        SettingsModel::updateOrCreate(['name' => 'version'], ['value' => $json]);

        Flasher::success('Version info updated successfully.');
    }

    public function render()
    {
        return view('livewire.site-settings');
    }
}