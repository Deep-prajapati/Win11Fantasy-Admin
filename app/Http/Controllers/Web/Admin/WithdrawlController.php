<?php

namespace App\Http\Controllers\Web\Admin;

use App\Helpers\Helper;
use App\Models\UserWallet;
use App\Models\Withdrawal;
use App\Models\Transection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WithdrawlController extends Controller
{
    public function index(Request $request){
        $title = "Withdawals";
        $records = Withdrawal::query();

        $records = $records->orderby('status','asc')->with('user')->orderby('created_at','desc')->paginate(env('PER_PAGE_RECORDS',10)); // 'user.account'
        return view('withdrawl.index',compact('title','records'));
    }
    public function approveWithdrawal(Request $request, $withdrawal_id)
    {
        $recharge = Withdrawal::find($withdrawal_id);
        if (!$recharge || $recharge->status != 1) {
            return Helper::EmptyReturn('Invalid withdawal details.');
        }
        $recharge->status = 2;
        $recharge->save();
        return Helper::SuccessReturn(null, 'withdawal approved successfully.');
    }
    public function rejectWithdrawal(Request $request, $withdrawal_id)
    {
        $recharge = Withdrawal::find($withdrawal_id);
        if (!$recharge || $recharge->status != 1) {
            return Helper::EmptyReturn('Invalid withdawal details.');
        }
        $recharge->status = 3;
        $recharge->save();
        Transection::create([
            'user_id'=>$recharge->user_id,
            'type' => 1,
            'amount' => $recharge->amount,
            'desc'=> 'Withdarw Refund.',
        ]);
        $wallet = UserWallet::where('user_id',$recharge->user_id)->first();
        $wallet->winning+= $recharge->amount;
        $wallet->save();
        return Helper::SuccessReturn(null, 'Withdaw Rejected successfully.');
    }
}
