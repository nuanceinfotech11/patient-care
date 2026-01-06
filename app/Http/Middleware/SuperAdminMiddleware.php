<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use App\Models\User;
use App\Models\DeceaseInventoryMapping;
use Illuminate\Http\Request;

class SuperAdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */


    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && auth()->user()->role()->first()->name=='superadmin') {
            return $next($request);
            // $user_detail=User::with('role')->find(Auth()->user()->id);
            // echo"<pre>";print_r($user_detail);die;
            // if(in_array($user_detail->role[0]->id,[1])){
                // return $next($request);
                // }else{
                    //  return redirect('/');
                    // }
                    // echo"la";die;
                    // return redirect()->route('superadmin-login');
        }
      //  echo"jk";die;
       return redirect('/login');
        
    }
}
