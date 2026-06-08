<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->query('search');
            $status = $request->query('status');
            $role = $request->query('role');

            $users = User::when($search, function($query) use ($search){ 
                    $query->whereAny(['name', 'email'], 'like', "%$search%");
                })
            ->when($status, function($query) use ($status){ 
                    $query->where('status', $status);
                })
            ->when($role, function($query) use ($role) {
                    $query->where('role', $role);
            })->orderByDesc('id')->cursorPaginate(10);

            return response()->json([
                'users' => $users,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
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
            $email = User::where('email', $request->email)->exists();

            if ($email) {
                return response()->json([
                    'message' => 'This email is already exists.',
                ]);
            }

            $user->update($request->only('name', 'email', 'phone'));

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

    public function toggle(User $user, $status)
    {
        try {
            if(!isset($status) || !in_array($status, ["active", "banned"])) {
               return response()->json([
                    'message' => 'Invalid status.',
                ], 500);
            }

            if ($user->status == $status) {
                return response()->json([
                    'message' => "User already {$status}.",
                ], 500);
            }

            $user->tokens->each(function ($token) {
                $token->delete();
            });

            $user->update([
                'status' => $status,
            ]);

            return response()->json([
                'message' => "User account {$status} successfully.",
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function show(User $user) {
        try {
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

    public function destroy(User $user) {
        try {
            $user->tokens->each(function ($token) {
                $token->delete();
            });

            $user->delete();

            return response()->json([
                'message' => 'User account deleted has been successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
