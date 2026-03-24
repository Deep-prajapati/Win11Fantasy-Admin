<?php

namespace App\Http\Middleware;

use App\Helpers\Helper;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserBlocked
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if(auth()->user() && auth()->user()->is_banned){
             return response()->json(['success' => false, 'status' => config('constant.STATUS_UNAUTHORIZED'), 'message' => 'Oops! It looks like your account has been blocked. Reach out to our team if you think this is a mistake.'], config('constant.STATUS_UNAUTHORIZED'));
        }else{
            return $next($request);
        }
    }
}
