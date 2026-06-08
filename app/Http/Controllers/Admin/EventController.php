<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Models\Event;
use App\Models\EventCategory;
use App\Interfaces\EventRepositoryInterface;
use App\Mail\ApprovedPublishedRequest;

class EventController extends Controller
{
    protected $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function storeCategories(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        try {
            $setCategory = EventCategory::create([
                'name' => $request->name,
            ]);

            return response()->json([
                'message' => 'Events category create successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
    
    public function categoryIndex()
    {
        try {   
            return $this->eventRepository->categories();
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            return $this->eventRepository->events($request);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function toggle(Request $request, Event $event, $status)
    {
        $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            if (!isset($status) || !in_array($status, ["approved", "rejected"])) {
                return response()->json([
                    'message' => 'Invalid status',
                ], 500);
            }

            if ($event->status == 'rejected') {
                return response()->json([
                    'message' => 'This request cannot be approved as it has already been rejected. You can only approve or reject pending requests.'
                ], 500);
            }

            $eventRequest = $event->metas()->where('key', 'event_request');

            if (!$eventRequest->exists()) {
                return response()->json([
                    'message' => 'event request not found.',
                ], 404);
            }

            if ($status == "approved") {
                $event->update(['status' => $status]);
                $event->metas()->where('key', 'event_request')->delete();
            }

            $requestData = [
                'status' => 'rejected',
                'reason' => $request->reason,
            ];

            if ($status == 'rejected') {
                $eventMeta = $eventRequest->update([
                    'value' => $requestData,
                ]);

                $event->update(['status' => $status]);
            }

            Mail::to($event->user->email)
                ->queue((new ApprovedPublishedRequest($event))
                ->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => "event request has been {$status}",
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function show(Event $event) {
        try {
            return response()->json([
                'event' => $event,
            ], 200);
        } catch (\Exception $e) {
            report($e);
            
            return response()->json([
                'message' => 'Something went wrong.', 
            ], 500);
        }
    }

    public function update(Request $request, Event $event) {
        $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'location' => 'nullable|string',
            'event_date' => 'nullable|date',
            'ticket_price' => 'nullable|numeric',
            'total_tickets' => 'nullable|integer',
        ]);

        try {
            $event->update($request->only('title', 'description', 'location', 'event_date', 'ticket_price', 'total_tickets'));

            return response()->json([
                'message' => 'Event updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function destroy(Event $event) {
        try {
            $event->metas()->where('key', 'event_request')->delete();

            $event->delete();

            return response()->json([
                'message' => 'Organizers event deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
