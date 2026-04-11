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
    public function organizerRequest(Request $request) {
            $request->validate([
                'description' => 'required|string'
            ]);

        try {
            $user = auth()->user();
            $adminEmail = User::where('role', 'administrator')->value('email');

            $requestData = json_encode([
                'name' => $user->name,
                'email' => $user->email,
                'status' => 'pending',
                'description' => $request->description,
            ]);

            $userMeta = $user->metas()->updateOrCreate([
                'key' => 'organizer_request'
            ],[
                'value' => $requestData,
            ]);

            Mail::to($adminEmail)->queue((new OrganizerRequestMail($user))->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => 'Your Request is sent to admin.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
