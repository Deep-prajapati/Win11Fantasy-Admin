<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserWallet;
use App\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use App\Services\OtpLessService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $otpLess;

    public function __construct()
    {
        $this->otpLess = new OtpLessService();
    }

    public function login(Request $request)
    {
        $rules = [
            'username' => [
                'required',
                'digits:10',
                'regex:/^[6-9]\d{9}$/'
            ],
        ];

        $customMessages = [
            'username.required' => 'Mobile number is required.',
            'username.digits' => 'Please enter a valid mobile number.',
            'username.regex' => 'Please enter a valid mobile number.'
        ];

        $validator = Validator::make($request->all(), $rules, $customMessages);
        
        if ($validator->fails()) 
        {
            return Helper::FalseReturn(null, $validator->errors()->first());
        }

        $user = User::where('mobile_number', $request->username)->first();

        try {
            $data = json_decode(SiteSettings::where('name', 'otp_info')->first()->value);

            $otp = rand(100000, 999999);
            
            $expiredAt = now()->addMinutes((int)$data->expiredat);
            
            if(!$user)
            {
                $user = new User();
                $user->mobile_number = $request->username;
                $user->name = 'User';
                $user->email = $credentials['email'] ?? null;
                $user->otp_expired_at = $expiredAt;
                $user->otp_token = $otp;
                $user->save();

                UserWallet::create([
                    'user_id' => $user->id
                ]);
            }else{
                $user->otp_expired_at = $expiredAt;
                $user->otp_token = $otp;
                $user->save();
            }
            
            $payload = [
                "messaging_product" => "whatsapp",
                "to" => '91'.$user->mobile_number,
                "type" => "template",
                "template" => [
                    "name" => $data->templete,
                    "language" => [
                        "code" => "en"
                    ],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => (string) $otp
                                ]
                            ]
                        ],
                        [
                            "type" => "button",
                            "sub_type" => "url",
                            "index" => "0",
                            "parameters" => [
                                [
                                    "type" => "text",
                                    "text" => (string) $otp
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            $response = Http::withToken($data->accessToken)->withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post(
                "https://graph.facebook.com/v19.0/{$data->phoneid}/messages",
                $payload
            );

            if ($response->successful()) {
                $data = $response->json();

                // example: wamid
                // $messageId = $data['messages'][0]['id'] ?? null;
                
                return Helper::SuccessReturn(null, 'Otp send on you mobile number.');
            } else {
                return Helper::EmptyReturn('Invalid mobile number. Please check your mobile number.');
            }
        } catch (\Throwable $th) {
            return Helper::EmptyReturn('Invalid mobile number. Please check your mobile number.');
        }

        // if (!$user) 
        // {
        //     $user = User::create([
        //         'mobile_number' =>  $request->username,
        //         'name' => 'User',
        //         'email' => $credentials['email'] ?? null,
        //     ]);

        //     UserWallet::create([
        //         'user_id' => $user->id
        //     ]);
        // }

        // if (!empty($request->fcm_token) && $user->fcm_token !== $request->fcm_token) 
        // {
        //     $user->update(['fcm_token' => $request->fcm_token ?? '']);
        // }

        // if (!in_array($request->username, ['9636674261', '6377632486'])) 
        // {
        //     $res = $this->otpLess->sendOtp('+91' . $request->username);

        //     if (!$res['success']) 
        //     {
        //         return Helper::EmptyReturn('Invalid mobile number. Please check your mobile number.');
        //     }

        //     $user->update([
        //         'otp_token' => $res['token']
        //     ]);
        // }

        return Helper::SuccessReturn(null, 'Otp send on you mobile number.');
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => ['required', 'string'],
            'mobile' => ['required', Rule::unique('users', 'mobile_number'), 'regex:/^[6-9]\d{9}$/'],
            // 'password' => ['required', 'min:6'],
            // 'confirm_password' => ['required', 'same:password'],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) 
        {
            return Helper::FalseReturn(null, $validator->errors()->first());
        }

        $res = $this->otpLess->sendOtp('+91' . $request->mobile);

        if (!$res['success']) 
        {
            return Helper::EmptyReturn('Invalid mobile number. Please check your mobile number.');
        }

        $user = User::create([
            'name' => $request->name,
            'mobile_number' => $request->mobile,
            'otp_token' => $res['token']
        ]);

        UserWallet::create([
            'user_id' => $user->id
        ]);

        return Helper::SuccessReturn(null, 'Otp send on you mobile number.');
    }

    public function otpVerify(Request $request)
    {
        $rules = [
            'otp' => ['required', 'digits:6'],
            'mobile' => ['required', Rule::exists('users', 'mobile_number')],
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Helper::FalseReturn(null, $validator->errors()->first());
        }

        $user = User::where('mobile_number', $request->mobile)->first();

        if (!$user) {
            return Helper::EmptyReturn('Invalid user details');
        }

        // ✅ Check OTP exists
        if (!$user->otp_token) {
            return Helper::EmptyReturn('OTP not found. Please request again.');
        }

        // ✅ Check OTP match
        if ($user->otp_token != $request->otp) {
            return Helper::EmptyReturn('Invalid OTP.');
        }

        // ✅ Check Expiry
        if ($user->otp_expired_at && now()->gt($user->otp_expired_at)) {
            return Helper::EmptyReturn('OTP expired. Please request again.');
        }

        try {
            // ✅ Clear OTP after success
            $user->otp_token = null;
            $user->otp_expired_at = null;
            $user->save();

            // ✅ Create Token
            $token = $user->createToken('authToken')->plainTextToken;

            // ✅ Load relations (if needed)
            $user->load('account');

            return Helper::SuccessReturn(
                [
                    'token' => $token,
                    'user' => $user
                ],
                'Login successfully.'
            );

        } catch (\Throwable $th) {
            return Helper::FalseReturn(null, 'Something went wrong');
        }
    }
}
