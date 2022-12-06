<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        if (Auth::attempt([
            'username'=>$request->get('username'),
            'password'=>$request->get('password')
        ])){
            $token=Auth::user()->createToken('myToken'.Auth::user()->id)->plainTextToken;
            $user = Auth::user();
            $user->is_locked = true;
            $user->save();
            return \response(['user'=> $user,'token'=>$token]);
        } else {
            return \response('error with the username or password', 401);
        }
    }

    public function logout()
    {
        $user = auth()->user();
        $user->is_locked = false;
        return auth()->user()->tokens()->delete();
    }
}
