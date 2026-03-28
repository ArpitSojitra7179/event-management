<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserMeta;
use App\Models\EventCategory;
use App\Interfaces\EventRepositoryInterface;

class AdminController extends Controller
{
    protected $eventRepo;

    public function __construct(EventRepositoryInterface $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    public function requestList(Request $request) {
        try {
            $user = auth()->user();

            $requestList = UserMeta::all();

            return response()->json([
                'request_list' => $requestList,
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.'
            ], 500);
        }
    }

    public function approveRequest(User $user) {
        try {
            $user->update(['role' => 'organizer']);

            UserMeta::where([
            [
                'user_id', $user->id
            ],[
                'key', 'organizer_request'
            ],
        ])->delete();

            return response()->json([
                'message' => 'Your request is approved now you promoted to organizer.'
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function setEventCategories(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $setCategory = EventCategory::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Event category set successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }
    
    public function eventCategoryIndex()
    {
        return $this->eventRepo->getAllEventCategories();
    }
}   
