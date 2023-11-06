<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cookie;

class LanguageController extends Controller
{
    public function setLanguage($locale){
        Cookie::queue(Cookie::make('locale', $locale, 60 * 24 * 90, null, null, false, true));
        return redirect()->back();
    }
}
