<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Mail\UserRegister;
use App\Mail\ResetPasswordMail;
use App\Models\User;

class AuthController extends Controller
{
    public function register(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:10',
        ]);

        try {
            $userCount = User::count();
            if ($userCount == 0) {
                $role = 'administrator';
            } else {
                $role = 'customer';
            }
            
            $user = User::create([
                'role' => $role,
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'phone' => $request->phone,
            ]);

            $token = $user->createToken('register_token')->accessToken;

            Mail::to($user->email)->queue((new UserRegister($user))->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => 'User created successfully.',
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function login(Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $user = Auth::user();

            if (! $user || $user->status == 'banned') {
                return response()->json([
                    'message' => 'you are banned, so cannot login',
                ], 403);
            }

            $token = $request->user()->createToken('login_token')->accessToken;

            return response()->json([
                'message' => 'login successfully.',
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function forgotPassword(Request $request) {
            $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
        
        try {
            $user = Auth::user();

            $user = User::where('email', $request->email)->latest()->first();

            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert([
                'email' => $request->email,
                'token' => $token,
            ]);

            Mail::to($request->email)
                ->queue((new ResetPasswordMail($token, $request->email, $user))
                ->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => 'Reset token sent to user mail',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function resetPassword(Request $request, $token) {
            $request->validate([
                'email' => 'required|email|exists:users,email',
                'password' => 'required|string|min:8|confirmed',
            ]);

        try {

            $reset = DB::table('password_reset_tokens')->where([
                    'email' => $request->email,
                    'token' => $token,
                ])->latest()->first();

            if (!$reset) {
                return response()->json([
                    'message' => 'Invalid token',
                ], 500);
            }

            $user = User::where('email', $reset->email)->latest()->first();

            if (!$user) {
                return response()->json([
                    'message' => 'User not found.',
                ], 404);
            }

            $user->update([
                'password' => bcrypt($request->password),
            ]);

            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            
            return response()->json([
                'message' => 'Password reset successfully',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function logout()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json([
                    'message' => 'user not found.',
                ], 404);
            }

            $user->tokens->each(function ($token) {
                $token->delete();
            });

            return response()->json([
                'message' => 'Logout successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
