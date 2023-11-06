<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LanguageMiddleware
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
        if(Cookie::has('locale')){
            App::setLocale(Cookie::get('locale'));
    
            // Check if the user is logged in
            if (Auth::check()) {
                $user = Auth::user();
                $user = User::find($user->id);
    
                // Check if the user's locale in the database is different from the one in the cookie
                if ($user->locale != Cookie::get('locale')) {
                    // Update the user's locale in the database
                    $user->locale = Cookie::get('locale');
                    $user->save();
                }
            }
        }
        return $next($request);
    }
}
