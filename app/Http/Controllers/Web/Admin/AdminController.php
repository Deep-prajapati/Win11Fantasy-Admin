<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(Request $request){
        $title = "Profile";
        return view('profile.index',compact('title'));
    }

    public function settings(Request $request){
        $title = 'Settings';
        return view('settings.index',compact('title'));
    }
}
