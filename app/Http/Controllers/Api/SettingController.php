<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\SiteSettings;

class SettingController extends Controller
{
    public function UPI()
    {
        try {
            if(SiteSettings::getValue('payment_upi_info') == null)
            {
                return Helper::EmptyReturn('No UPI info found.');
            }

            return Helper::SuccessReturn(SiteSettings::getValue('payment_upi_info'));
        } catch (\Throwable $th) {
            return Helper::EmptyReturn('Something went wrong.');
        }
    }
}
