<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UserMeta;
use App\Models\EventCategory;
use App\Interfaces\EventRepositoryInterface;

class AdminController extends Controller
{
    
    public function requestList(Request $request) {
        try {
            $user = auth()->user();

            $requestList = UserMeta::query()->when($request->key, function ($query, $key) {
                return $query->where('key', $key);
            })->get();

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
}
