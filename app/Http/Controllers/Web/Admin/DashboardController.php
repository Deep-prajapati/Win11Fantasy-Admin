<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Models\Recharge;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(){
        $title = "Dashboard";
        $user = auth()->user();
        $totalDeposit = Recharge::where('status',2)->sum('amount');
        $pendingDeposit = Recharge::where('status',1)->sum('amount');
        $totalUsers = User::where('role',2)->count();
        $todayUserCount = todayUserCount();
        return view('dashboard.index',compact('title','user','totalDeposit','pendingDeposit','totalUsers','todayUserCount'));
    }
}
