<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use App\Models\User;

class UserController extends Controller
{
    public function show() 
    {
        try {
            $user = auth()->user();

            return response()->json([
                'user' => $user,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function update(Request $request) 
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|min:10|max:10',
        ]);

        try {
            $user = auth()->user();

            $user->update($request->only(['name', 'phone']));

            return response()->json([
                'message' => 'Your account has been updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = auth()->user();

            $user->update([
                'password' => bcrypt($request->new_password),
            ]);

            return response()->json([
                'message' => 'Password changed successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function destroy(Request $request)
    {

        $request->validate([
            'password' => ['required', 'confirmed', new MatchOldPassword],
        ]);

        try {
            $user = auth()->user();

            $user->tokens->each(function ($token) {
                $token->delete();
            });

            $user->delete();

            return response()->json([
                'message' => 'User account has been deleted successfully.'
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
