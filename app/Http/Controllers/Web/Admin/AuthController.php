<?php

namespace App\Http\Controllers\Web\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function index(Request $request)
    {
        $title = "Dashboard";
        if ($request->isMethod('post')) {
            $credentials = $request->only('username', 'password');
            if (Auth::attempt(['email' => $credentials['username'], 'password' => $credentials['password']], $request->has('remember'))) {
                $user = Auth::user();
                if ($user->role == 1) {
                    return redirect()->route('admin.dashboard');
                } else {
                    Auth::logout();
                    flash()->error('Access denied.');
                    return redirect()->route('admin.login');
                }
            }
            flash()->error('Invalid credentials.');
            return redirect()->route('admin.login',compact('title'));
        }
        return view('auth.login');
    }
    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
