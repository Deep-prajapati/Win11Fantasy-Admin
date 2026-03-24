<?php

namespace App\Http\Controllers\Api;

use App\Helpers\Helper;
use App\Models\Recharge;
use App\Models\Withdrawal;
use App\Helpers\FileHelper;
use App\Models\Transection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class RechargeController extends Controller
{
    public function initiate(Request $request)
    {
        $rules = [
            'method' => ['required', 'in:1,2'],
            'amount' => ['required', 'numeric', 'min:1'],
            // 'image' => [
            //     Rule::requiredIf(function () {
            //         return request()->input('method') == 1;
            //     }),
            //     'file',
            //     'image',
            //     'mimes:jpeg,png,jpg,gif',
            //     'max:2048' // 2MB limit
            // ],
            'utr_no' => [
                Rule::requiredIf(function () {
                    return request()->input('method') == 1;
                }),
                'string'
            ],
        ];
        $validator = Validator::make($request->all(), $rules, [
            'amount.required' => "Deposit amount is required.",
            'amount.numeric' => "Deposit amount must be a number.",
            'amount.min' => "Minimum deposit amount is 100.",
            'method.required' => "Payment method required.",
            'method.in' => "Invalid payment method required.",
            'utr_no.required' => "UTR number is required when using manual payment method.",
            'utr_no.string' => "UTR number is invalid"
        ]);

        if ($validator->fails()) {
            return Helper::EmptyReturn($validator->errors()->first());
        }
        $recharge = new Recharge();
        // if ($request->file('image')) {
        //     $filePath = FileHelper::uploadFile($request->file('image'), 'uploads/recharges');
        //     // $recharge->image = $filePath;
        // }
        $recharge->method = $request->method;
        $recharge->user_id = auth()->user()->id;
        $recharge->amount = $request->amount;
        $recharge->utr_no = $request->utr_no;
        $recharge->save();
        return Helper::SuccessReturn(null, 'Deposit request created successfully.');
    }
    public function withdraw(Request $request)
    {
        $rules = [
            'method' => ['required', 'in:Bank,UPI'],
            'amount' => ['required', 'numeric', 'min:100', 'max:10000'],
        ];
        $validator = Validator::make($request->all(), $rules, [
            'amount.required' => "winnings is required.",
            'amount.numeric' => "winnings must be a number.",
            'amount.min' => "Minimum withdrawl winnings is 100.",
            'amount.max' => "Minimum withdrawl winnings is 10000.",
            'method.required' => "Withdraw method required.",
            'method.in' => "Invalid withdraw method required.",
        ]);
        $user = auth()->user();
        if ($validator->fails()) {
            return Helper::EmptyReturn($validator->errors()->first());
        }
        if ($user->account->winning < $request->amount) {
            return Helper::EmptyReturn("Insufficent Winnings.");
        }
        $details = $this->withdrawDetails($user, $request->method);

        if (!$details) {
            return Helper::EmptyReturn('Missing required withdrawal details.');
        }
        $withdraw = new Withdrawal();
        $withdraw->user_id = $user->id;
        $withdraw->method = $request->method;
        $withdraw->amount = $request->amount;
        $withdraw->details = $details;
        $withdraw->save();
        $user->account->winning =  $user->account->winning - $request->amount;
        $user->account->save();
        Transection::create([
            'user_id' => $user->id,
            'type' => 2,
            'amount' => $request->amount,
            'desc' => 'Withdrawal',
        ]);
        return Helper::SuccessReturn(null, 'Withdraw request created successfully.');
    }
    protected function withdrawDetails($user, $method)
    {
        if (!$user || !$user->account) {
            return null;
        }

        $bankFields = ['bank_holder_name', 'bank_name', 'bank_ifsc', 'bank_account'];
        $upiFields = ['upi_id', 'upi_name'];

        if ($method === 'Bank' && $this->checkFields($user->account, $bankFields)) {
            return $this->getAccountDetails($user->account, $bankFields);
        } elseif ($method === 'UPI' && $this->checkFields($user->account, $upiFields)) {
            return $this->getAccountDetails($user->account, $upiFields);
        }
        return null;
    }

    private function checkFields($account, $fields)
    {
        foreach ($fields as $field) {
            if (!isset($account->$field) || empty($account->$field)) {
                return false;
            }
        }
        return true;
    }

    private function getAccountDetails($account, $fields)
    {
        $details = [];
        foreach ($fields as $field) {
            $details[$field] = $account->$field;
        }
        return $details;
    }
}
