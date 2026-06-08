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

            if ($user->role == 'organizer') {
                return response()->json([
                    'message' => 'you cannot send request to become a organizer, because you are already organizer.',
                ], 500);
            }

            $record = $user->metas()->where('key', 'organizer_request')->latest()->first();

            if ($record) {
                $array = json_encode($record->value);
                $value = json_decode($array);
                $status = $value->status;

                if (!isset($status) || $status == 'pending') {
                    return response()->json([
                        'message' => 'You are already send request to become a organizer.'
                    ], 500);
                }
            }

            $adminEmail = User::where('role', 'administrator')->get()->value('email');

            $requestData = [
                'status' => 'pending',
                'description' => $request->description,
            ];

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
