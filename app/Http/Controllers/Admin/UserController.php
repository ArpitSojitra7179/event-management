<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = User::query()->when($request->role, function ($query, $role) {
                return $query->where('role', $role);
            })->when($request->email, function ($query, $email) {
                return $query->where('email', $email);
            })->when($request->name, function ($query, $name) {
                return $query->where('name', $name);
            })->get();

            return response()->json([
                'All User List' => $user,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|min:10|max:10',
        ]);

        try {
            $email = User::where('status', 'active')->pluck('email');

            if ($email == $request->email) {
                return response()->json([
                    'message' => 'This email is already exists',
                ]);
            }

            $user->update($request->only('name', 'email', 'phone'));

            return response()->json([
                'message' => 'User record updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function banUser(User $user)
    {
        try {
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            if (! $user || $user->status == 'banned') {
                return response()->json([
                    'message' => 'User already banned.',
                ]);
            }

            $user->update([
                'status' => 'banned',
            ]);

            return response()->json([
                'message' => 'User banned successfully.'
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
