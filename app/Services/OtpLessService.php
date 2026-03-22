<?php

namespace App\Services;

use App\Models\SiteSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;

class OtpLessService
{
    protected $clientSecret;
    protected $clientId;
    protected $baseUrl;

    public function __construct()
    {
        $otplessInfo = json_decode(SiteSettings::getValue('otpless_info'), true);

        $this->clientId = $otplessInfo['clientId'] ?? 'LGAAJ64PNYBDPJVDCYDX047OEO0UFPQO';
        $this->clientSecret = $otplessInfo['clientSecret'] ?? '7ter5uk8lm8x7gd7yfv348g8bclgk3kq';
    }

    public function sendOtp($number)
    {
        $respons = Http::withHeaders([
            'Content-Type' => 'application/json',
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
        ])->post('https://auth.otpless.app/auth/v1/initiate/otp', [
            'phoneNumber' => $number,
            'expiry' => 120,
            'otpLength' => 6,
            'channels' => ['SMS'],
        ]);
        if (isset($respons['requestId'])) {
            return [
                'success' => true,
                "token" => $respons['requestId']
            ];
        } else {
            return [
                'success' => false,
                "message" => $respons['message']
            ];
        }
    }
    public function verifyOtp($otp, $token)
    {
        $respons = Http::withHeaders([
            'Content-Type' => 'application/json',
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
        ])->post('https://auth.otpless.app/auth/v1/verify/otp', [
            'otp' => $otp,
            'requestId' => $token
        ]);
        if (isset($respons['isOTPVerified'])) {
            return [
                'success' => true,
                'message' => $respons['message']
            ];
        } else {
            return [
                'success' => false,
                'message' => $respons['message']
            ];
        }
    }
}
