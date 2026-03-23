<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrganizerRequestMail;
use App\Models\User;
use App\Models\UserMeta;

class OrganizerRequestController extends Controller
{
    public function organizerRequest(Request $request, User $user) {
            $validated = $request->validate([
                'description' => 'required|string'
            ]);
        try {
            $user = auth()->user();

            $requestData = json_encode([
                'name' => $user->name,
                'email' => $user->email,
                'status' => 'pending',
                'description' => $validated['description'],
            ]);

            $userMeta = UserMeta::updateOrCreate(
                ['user_id' => $user->id, 'key' => 'organizer_request'],
                [
                    'value' => $requestData,
                ],
            );

            Mail::to('admin@gmail.com')->queue((new OrganizerRequestMail($user))->delay(now()->addSeconds(5)));

            return response()->json([
                'success' => 'Your Request is sent to admin.',
                'user meta' => $userMeta,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }
}
