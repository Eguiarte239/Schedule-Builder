<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function setLanguage($locale){
        if(Auth::check()){
            $user = Auth::user();
            $user = User::find($user->id);
        
            // Check if the user's locale in the database is different from the one in the cookie
            if ($user->locale != $locale) {
                // Update the user's locale in the database
                $user->locale = $locale;
                $user->save();
            }
        }
        else{
            session()->put('locale', $locale);
        }
        return redirect()->back();
    }
}
