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
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                'phone' => 'nullable|string|max:10',
            ]);

        try {
            $userCount = User::count();
            if ($userCount == 0) {
                $role = 'admin';
            } else {
                $role = 'customer';
            }
            
            $user = User::create([
                'role' => $role,
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => bcrypt($validated['password']),
                'phone' => $validated['phone'],
            ]);

            $token = $user->createToken('register token')->accessToken;

            Mail::to($user->email)->queue((new UserRegister($user))->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => 'User Created Successfully.',
                'User' => $user,
                'Register Token' => $token,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }

    public function login(Request $request) {
        try {
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                return response()->json([
                    'message' => 'Invalid credentials'
                ], 401);
            }

            $token = $request->user()->createToken('Login Token')->accessToken;

            return response()->json([
                'ditails' => $credentials,
                'token' => $token,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }

    public function forgotPassword(Request $request) {
            $validated = $request->validate([
                'email' => 'required|email|exists:users,email',
            ]);
        
        try {
            $user = User::where('email', $validated['email'])->first();

            $token = Str::random(60);

            DB::table('password_reset_tokens')->updateOrInsert([
                'email' => $validated['email'],
                'token' => $token,
            ]);

            Mail::to($validated['email'])
                ->queue((new ResetPasswordMail($token, $validated['email'], $user))
                ->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => 'Reset token sent to user mail',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }

    public function resetPassword(Request $request, $token) {
            $validated = $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);

        try {
            $email = $request->query('email');

            $reset = DB::table('password_reset_tokens')->where([
                    'email' => $email,
                    'token' => $token,
                ])->first();

            if (!$reset) {
                return response()->json([
                    'message' => 'Invalid Token',
                ], 500);
            }

            $user = User::where('email', $email)->first();

            $user->update([
                'password' => bcrypt($validated['password']),
            ]);

            DB::table('password_reset_tokens')->where('email', $email)->delete();
            // $reset->delete();

            return response()->json([
                'message' => 'Password reset Successfully',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }
}
