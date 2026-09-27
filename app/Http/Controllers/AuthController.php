<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\PasswordChangeRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

            // Mobile clients: issue a bearer token
            if ($request->header('X-Client-Type') === 'mobile') {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'message' => 'User created successfully',
                    'user' => $user,
                    'token' => $token,
                ]);
            }

            // Web clients: establish a session
            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'status' => true,
                'message' => 'User created successfully',
                'user' => $user,
            ]);
        } catch (Exception $e) {
            Log::error('Register error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, please try again later',
            ], 500);
        }
    }
    public function login(LoginRequest $request)
    {
        try {
            $data = $request->validated();

            $user = User::where('email', $data['email'])->first();
            if (! $user || ! Hash::check($data['password'], $user->password)) {
                return response()->json(['status' => false, 'message' => 'Invalid username or password'], 400);
            }

            // Mobile clients: issue a bearer token
            if ($request->header('X-Client-Type') === 'mobile') {
                $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json([
                    'status' => true,
                    'user' => $user,
                    'token' => $token,
                ]);
            }

            // Web clients: establish a session (cookie-based, httpOnly, CSRF-protected)
            Auth::login($user);
            $request->session()->regenerate();

            return response()->json([
                'status' => true,
                'user' => $user,
            ]);
        } catch (Exception $e) {
            Log::error('Login error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong, please try again later',
            ], 500);
        }
    }
    public function logout(Request $request)
    {
        if ($request->header('X-Client-Type') === 'mobile') {
            $request->user()->currentAccessToken()->delete();
        } else {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return response()->json(['status' => true, 'message' => 'Logged out']);
    }
     public function changePassword(PasswordChangeRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect',
                'errors' => ['current_password' => ['Current password is incorrect']],
            ], 422);
        }

        if (Hash::check($request->new_password, $user->password)) {
            return response()->json([
                'message' => 'New password must be different from current password',
                'errors' => ['password' => ['New password must be different from current password']],
            ], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Password changed successfully']);
    }
}
