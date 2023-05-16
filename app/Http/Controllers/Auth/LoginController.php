<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        try {
            $credentials = [
                'email' => $request->email,
                'password' => $request->password,
            ];
            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'error' => 0,
                    'msg' => 'wrong email or password'
                ]);
            }
            $token = $request->user()->createToken('auth_token');
            return response()->json([
                'error' => 0,
                'msg' => 'successfully logedin',
                'token' => $token->plainTextToken,
                200
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => 1,
                'msg' => $e->getMessage(),
            ]);
        }
    }
}
