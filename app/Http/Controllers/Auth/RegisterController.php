<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistrationRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(RegistrationRequest $request)
    {
        try {

            $newuser = $request->validated();
            $newuser['password'] = Hash::make($newuser['password']);
            $user = User::create($newuser);
            $success['token'] = $user->createToken('access-token')->plainTextToken;
            return response()->json($success, 200);
        } catch (Exception $e) {
            return response()->json([
                'error' => 1,
                'msg' => $e->getMessage(),
            ]);
        }
    }
}
