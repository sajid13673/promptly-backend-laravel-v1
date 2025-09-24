<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $data = $request->validated();
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return Response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => $user,
                'token' => $token
            ]);
        } catch (Exception $e) {
            return Response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();
            if (! $user || ! Hash::check($data['password'], $user->password)) {
                return response()->json(['satus' => false, 'message'  => "Invalid username or password"], 400);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'status' => true,
                'user' => $user,
                'token' => $token
            ]);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, Please try again later'
            ]);
        }
    }
    public function logout(Request $request)
    {
        try {
            $request->user()->currentAccessToken()->delete();
            return response()->json(['message' => 'Logged out']);
        } catch (Exception $e) {
            return response()->json(['message', $e->getMessage()]);
        }
    }
}
