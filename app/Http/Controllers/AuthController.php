<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request){
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        $token = $user->createToken("register")->plainTextToken;
        return response()->json([
            'message' => 'User authenticated',
            'token' => $token
        ], 201);
    }
    public function login(LoginRequest $request){
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            abort(404);
        }
        $token = $user->createToken('login')->plainTextToken;
        return response()->json([
            'message'=> 'User logged succesfully',
            'token'=> $token
        ]);
    }
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return response()->json([
            'message'=> 'User logged out succesfully'
        ],200);
    }
}
