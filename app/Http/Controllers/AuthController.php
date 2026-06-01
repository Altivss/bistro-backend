<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Simple hardcoded auth for demo - replace with real auth later
        if ($validated['email'] === 'admin@bistro.com' && $validated['password'] === 'password') {
            $token = hash('sha256', $validated['email'] . time());
            return response()->json([
                'message' => 'Login successful',
                'token' => $token,
                'user' => [
                    'id' => 1,
                    'name' => 'Admin',
                    'email' => $validated['email'],
                ]
            ], 200);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request)
    {
        return response()->json(['message' => 'Logged out successfully']);
    }

    public function forgotPassword(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
        ]);

        return response()->json([
            'message' => 'Password reset link sent to your email'
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        return response()->json([
            'message' => 'Password reset successfully'
        ]);
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'oldPassword' => 'required|string',
            'newPassword' => 'required|string|min:6',
        ]);

        return response()->json([
            'message' => 'Password changed successfully'
        ]);
    }
}
