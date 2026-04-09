<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventPublishRequestMail;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\Event;

class EventApproveController extends Controller
{
    public function eventPublishRequest(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        try {
            $user = auth()->user();

            if (!$user->events()->exists()) {
                return response()->json([
                    'message' => 'You have not organize any event so you cannot send request for approvel.',
                ], 500);
            }
            
            $adminEmail = User::where('role', 'administrator')->value('email');

            $requestData = json_encode([
                'name' => $user->name,
                'email' => $user->email,
                'status' => 'pending',
                'description' => $request->description,
            ]);

            $userMeta = $user->metas()->updateOrCreate([
                'key' => 'event_approve_request',
            ],[
                'value' => $requestData,
            ]);

            Mail::to($adminEmail)->queue((new EventPublishRequestMail($user))->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => 'Your request sent to admin.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
