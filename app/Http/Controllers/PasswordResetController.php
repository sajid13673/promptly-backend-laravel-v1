<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResetPasswordRequest;
use App\Models\PasswordResetCode;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class PasswordResetController extends Controller
{
    public function sendCode(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $code = random_int(100000, 999999); // 6-digit OTP

        PasswordResetCode::updateOrCreate(
            ['email' => $request->email],
            ['code' => Hash::make($code), 'expires_at' => now()->addMinutes(15)]
        );

        Mail::to($request->email)->send(new \App\Mail\PasswordResetCodeMail($code));

        return response()->json(['message' => 'Verification code sent']);
    }

    public function verifyCode(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|digits:6',
        ]);

        $record = PasswordResetCode::where('email', $request->email)->first();

        if (!$record || now()->greaterThan($record->expires_at) || !Hash::check($request->code, $record->code)) {
            return response()->json(['message' => 'Invalid or expired code'], 422);
        }

        return response()->json(['message' => 'Code verified']);
    }

    public function reset(ResetPasswordRequest $request): JsonResponse
    {
        $record = PasswordResetCode::where('email', $request->email)->first();

        if (!$record || now()->greaterThan($record->expires_at) || !Hash::check($request->code, $record->code)) {
            return response()->json(['message' => 'Invalid or expired code'], 422);
        }

        $user = User::where('email', $request->email)->first();
        $user->update(['password' => Hash::make($request->password)]);

        $record->delete();

        return response()->json(['message' => 'Password reset successfully']);
    }
}
