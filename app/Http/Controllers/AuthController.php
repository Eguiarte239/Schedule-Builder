<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PhpParser\Node\Expr\New_;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        event(new Registered($user));

        return response($user, Response::HTTP_CREATED);
    }

    public function login(Request $request){
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken('token', ['expires_in' => 3600])->plainTextToken;
            return response(["token"=>$token], Response::HTTP_ACCEPTED);
        }
        else{
            return response(Response::HTTP_UNAUTHORIZED);
        }

    }

    public function userProfile(){
        return response()->json([
            "message" => "userProfile ok",
            "userData" => auth()->user()
        ], Response::HTTP_OK);
    }

    public function logout(Request $request){
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json([
            'message' => 'Tokens eliminados correctamente',
        ]);
    }
}
