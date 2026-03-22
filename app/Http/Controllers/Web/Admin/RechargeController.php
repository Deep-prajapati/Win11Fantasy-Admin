<?php

namespace App\Http\Controllers\Web\Admin;

use App\Helpers\Helper;
use App\Models\Recharge;
use App\Models\Transection;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\UserWallet;

class RechargeController extends Controller
{
    public function index(Request $request)
    {
        $title = "Recharges List";
        $recharges = Recharge::query();

        $recharges = $recharges->with('user')->orderby('status','asc')->orderby('created_at','desc')->paginate(env('PER_PAGE_RECORDS', 10));
        return view('recharges.index', compact('title', 'recharges'));
    }
    public function approveRecharge(Request $request, $recharge_id)
    {
        $recharge = Recharge::find($recharge_id);
        if (!$recharge || $recharge->status != 1) {
            return Helper::EmptyReturn('Invalid recharge details.');
        }
        $recharge->status = 2;
        $recharge->save();
        Transection::create([
            'user_id'=>$recharge->user_id,
            'type' => 1,
            'amount' => $recharge->amount,
            'desc'=> 'Recharge',
        ]);
        $wallet = UserWallet::where('user_id',$recharge->user_id)->first();
        $wallet->balance+= $recharge->amount;
        $wallet->save();
        return Helper::SuccessReturn(null, 'Recharge approved successfully.');
    }
    public function rejectRecharge(Request $request, $recharge_id)
    {
        $recharge = Recharge::find($recharge_id);
        if (!$recharge || $recharge->status != 1) {
            return Helper::EmptyReturn('Invalid recharge details.');
        }
        $recharge->status = 3;
        $recharge->save();
        return Helper::SuccessReturn(null, 'Recharge Rejected successfully.');
    }
}
