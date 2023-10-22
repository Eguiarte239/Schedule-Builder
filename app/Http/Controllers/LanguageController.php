<?php

namespace App\Http\Controllers;

class LanguageController extends Controller
{
    public function setLanguage($locale){
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
