<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $user = Socialite::driver('google')->user();
        //$userExists = User::where('external_id', $user->id)->where('external_auth', 'google')->first();    
        /*if (!strpos($user->getEmail(), '@alumnos.udg.mx')) {
            return redirect('/register')->withErrors([
                'email' => 'Solo se permiten correos electrónicos institucionales de la Universidad de Guadalajara.',
            ]);
        }*/
        $userExists = User::where('email', $user->email)->first();
    
        if($userExists && $userExists->external_id === $user->id && $userExists->external_auth === 'google'){
            Auth::login($userExists);
        }
        else{
            $userNew = User::create([
                'name' => $user->name,
                'email' => $user->email,
                'avatar' => $user->avatar,
                'external_id' => $user->id,
                'external_auth' => 'google',
            ]);
            // Asignar el rol "google-user" al usuario recién creado
            $userNew->assignRole('google-user');
            Auth::login($userNew);
        }
        
        return redirect('/');
    }
}
