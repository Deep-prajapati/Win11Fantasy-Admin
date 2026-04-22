<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Helpers\FileHelper;
use App\Models\Transection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Models\SiteSettings;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function profile(Request $request)
    {
        $user = auth()->user();
        $user->load('account');
        return Helper::SuccessReturn($user, 'User profile fatched successfully.');
    }

    public function profileUpdate(Request $request)
    {
        $user = auth()->user();
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'image' => [
                'nullable',
                'file',
                'image',
                'mimes:jpeg,png,jpg,gif',
                'max:2048'
            ],
            'invite_code' => [
                'nullable',
                Rule::exists('users', 'invite_code')
            ],
            'bank_name' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->input('bank_holder_name') ||
                        $request->input('bank_account_number') ||
                        $request->input('bank_ifsc_code');
                }),
                'string'
            ],
            'bank_holder_name' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->input('bank_name') ||
                        $request->input('bank_account_number') ||
                        $request->input('bank_ifsc_code');
                }),
                'string',
                function ($attribute, $value, $fail) use ($request, $user) {
                    if ($user->country_code === '+91') {
                        if (!preg_match('/^[A-Za-z\s\.]+$/', $value)) {
                            $fail('The bank holder name should contain only letters, spaces and dots.');
                        }
                    }
                }
            ],
            'bank_account_number' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->input('bank_name') ||
                        $request->input('bank_holder_name') ||
                        $request->input('bank_ifsc_code');
                }),
                'string',
                function ($attribute, $value, $fail) use ($request, $user) {
                    if ($user->country_code === '+91') {
                        if (!preg_match('/^[0-9]{9,18}$/', $value)) {
                            $fail('For Indian accounts, the bank account number should be 9-18 digits.');
                        }
                    }
                }
            ],
            'bank_ifsc_code' => [
                Rule::requiredIf(function () use ($request, $user) {
                    return $request->input('bank_name') ||
                        $request->input('bank_holder_name') ||
                        $request->input('bank_account_number');
                }),
                'string',
                function ($attribute, $value, $fail) use ($request, $user) {
                    if ($user->country_code === '+91') {
                        if (!preg_match('/^[A-Z]{4}0[A-Z0-9]{6}$/', $value)) {
                            $fail('The IFSC code format should be 4 letters followed by 0 and 6 alphanumeric characters (e.g., SBIN0123456).');
                        }
                    }
                }
            ],
            'upi_id' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value !== null) {
                        if (!preg_match('/^[a-zA-Z0-9\.-]{3,}@[a-zA-Z]{3,}$/', $value)) {
                            $fail('The UPI ID should be in valid format (e.g., username@upi).');
                        }
                    }
                }
            ],
            'upi_name' => [
                Rule::requiredIf(function () use ($request) {
                    return $request->input('upi_id');
                }),
                'string',
                function ($attribute, $value, $fail) use ($request, $user) {
                    if ($user->country_code === '+91') {
                        if (!preg_match('/^[A-Za-z\s\.]+$/', $value)) {
                            $fail('The Upi name should contain only letters, spaces and dots.');
                        }
                    }
                }
            ],
        ];

        $messages = [
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a valid string.',
            'name.max' => 'The name may not be greater than 255 characters.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',
            'image.image' => 'The profile image must be an actual image file.',
            'image.mimes' => 'The profile image must be a file of type: jpeg, png, jpg, gif.',
            'image.max' => 'The profile image may not be greater than 2MB.',
        ];
        $validator = Validator::make($request->all(), $rules, [
            'image.image' => 'Profile image sh'
        ], $messages);

        if ($validator->fails()) {
            return Helper::FalseReturn(null, $validator->errors()->first());
        }

        if ($request->hasFile('image')) {
            if ($user->image    != 'assets/default.png') {
            }
            $user->image = FileHelper::uploadFile($request->image, 'user/profile');
        }
        $user->email = $request->email;
        $user->name = $request->name;
        if (isset($request->invite_code) && !isset($user->ref_code)) {
            $user->ref_code = $request->invite_code;
            // invite bonus
            $user2 = User::where('invite_code', $request->invite_code)->with('account')->first();
            $refBonus = SiteSettings::getValue('refer_bonus', 0);
            if ($refBonus != 0 && $user2 && $user2->account) {
                $user2->account->bonus += 100;
                $user2->account->save();
                Transection::create([
                    'user_id' => $user2->id,
                    'type' => 1,
                    'amount' => 100,
                    'desc' => 'Referal Bonus',
                ]);
            }
        }
        $user->save();
        $user->load('account');
        $user->account->bank_name = $request->input('bank_name', $user->account->bank_name);
        $user->account->bank_account = $request->input('bank_account_number', $user->account->bank_account);
        $user->account->bank_holder_name = $request->input('bank_holder_name', $user->account->bank_holder_name);
        $user->account->bank_ifsc = $request->input('bank_ifsc_code', $user->account->bank_ifsc);
        $user->account->upi_id = $request->input('upi_id', $user->account->upi_id);
        $user->account->upi_name = $request->input('upi_name', $user->account->upi_name);
        $user->account->save();
        return Helper::SuccessReturn($user, 'Your profile updated successfully.');
    }

    public function transaction(Request $request)
    {
        $user = auth()->user();
        $tnx = Transection::where("user_id", $user->id)->orderby('created_at', 'desc')->get();
        return Helper::SuccessReturn($tnx, 'Data fatched');
    }

    public function leaderboard()
    {
        // fake leaderboard working
        $data = Cache::remember('leaderboard', 300, function () {
            return User::where(['role' => 3, 'is_banned' => false])
                ->inRandomOrder()
                ->take(10)
                ->get()
                ->map(function ($user) {
                    $user->score = rand(5000, 10000);
                    return $user;
                })
                ->sortByDesc('score')
                ->values();
        });
        return Helper::SuccessReturn($data, 'data fatched.');
    }
}