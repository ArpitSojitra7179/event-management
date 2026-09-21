<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Rules\MatchOldPassword;
use App\Models\User;
use App\Http\helper;

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
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|min:10|max:10',
            'country_name' => 'nullable|string',
            'country_code' => 'nullable|string',
            'region_name' => 'nullable|string',
            'region_code' => 'nullable|string',
        ]);

        try {
            $user = auth()->user();

            $data = $request->only(['name', 'phone', 'country_name', 'country_code', 'region_name', 'region_code']);

            if ($request->name !== $user->name) {
                $data['avatar'] = 'https://api.dicebear.com/10.x/initials/svg?seed=' . urlencode($request->name);
            }

            $user->update($data);

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

    public function toggle($status)
    {
        try {
            $user = auth()->user();

            if (!isset($status) || !in_array($status, ["enable", "disable"])) {
                return response()->json([
                    'message' => 'Invalid status',
                ], 500);
            }

            if ($status == 'enable' && $user->api_token !== null) {
                return response()->json([
                    'message' => 'An external API token already exists. Please refresh the token if you want a new one.'
                ], 500);
            }

            $user->update([
                'api_token' => $status == 'enable' ? helper::generateUniqueToken('users', 'api_token', 32) : null
            ]);

            return response()->json([
                'message' => "your api_token {$status}",
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function refreshExternalToken() {
        try {
            $user = auth()->user();

            if ($user->api_token == null) {
                return response()->json([
                    'message' => 'You have not a api_token, so you cannot refresh it.'
                ], 500);
            }

            $user->update([
                'api_token' => helper::generateUniqueToken('users', 'api_token', 32)
            ]);

            return response()->json([
                'message' => 'Your api_token refreshed successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
