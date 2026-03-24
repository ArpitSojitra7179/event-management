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
    public function show(User $user) 
    {
        try {
            $user = auth()->user();

            return response()->json([
                'Your Detail' => $user,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }

    public function update(Request $request) 
    {
        $validated = $request->validate([
            'name' => 'nullable|string',
            'phone' => 'nullable|string|max:10',
        ]);

        try {
            $user = auth()->user();

            $user->update($validated);

            return response()->json([
                'message' => 'Your record updated successfully.',
                'updated record' => $user,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        try {
            $user = auth()->user();

            $user->password = Hash::make($validated['new_password']);
            $user->save();

            return response()->json([
                'message' => 'Password change successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }

    public function deleteUser(Request $request)
    {

        $validated = $request->validate([
            'password' => ['required', 'confirmed', new MatchOldPassword],
        ]);

        try {
            $user = auth()->user();

            $user->tokens->each(function ($token) {
                $token->delete();
            });

            $user->delete();

            return response()->json([
                'message' => 'User deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }
}
