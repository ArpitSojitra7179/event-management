<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\EventMeta;
use App\Models\EventCategory;
use App\Interfaces\EventRepositoryInterface;
use App\Mail\ApprovedOrganizerMail;

class AdminController extends Controller
{ 
    public function organizerRequests(Request $request) {
        try {
            $requests = UserMeta::where('key', 'organizer_request')
                ->with('user:id,name,email')
                ->orderByDesc('id')
                ->cursorPaginate(20);

            return response()->json([
                'requests' => $requests,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function eventRequests(Request $request)
    {
        try {
            $requests = EventMeta::where('key', 'event_request')
                ->orderByDesc('id')
                ->with([
                    'event:id,title,user_id',
                    'event.user:id,name,email',
                ])
                ->cursorPaginate(20);

            return response()->json([
                'requests' => $requests,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function toggle(Request $request, User $user, $status) {
        $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            if (!isset($status) || !in_array($status, ["approved", "rejected"])) {
                return response()->json([
                    'message' => 'Invalid status',
                ], 500);
            }

            $record = $user->metas()->where('key', 'organizer_request')->latest()->first();
            $array = json_encode($record->value);
            $value = json_decode($array);
            $checkStatus = $value->status;

            if ($checkStatus == 'rejected') {
                return response()->json([
                    'message' => 'This request cannot be approved as it has already been rejected. You can only approve or reject pending requests.'
                ], 500);
            }

            $organizerRequest = $user->metas()->where('key', 'organizer_request');

            if (!$organizerRequest->exists()) {
                return response()->json([
                    'message' => 'user request not found',
                ], 404);
            }
            
            if ($status == 'approved') {
                $user->update(['role' => 'organizer']);
                $organizerRequest->delete();
            }

            $requestData = json_encode([
                'status' => 'rejected',
                'reason' => $request->reason,
            ]);

            if ($status == 'rejected') {
                $userMeta = $organizerRequest->update([
                    'value' => $requestData,
                ]);
            }

            Mail::to($user->email)->queue((new ApprovedOrganizerMail($user))->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => "Your request is {$status}.",
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
