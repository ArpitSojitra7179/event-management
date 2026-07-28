<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Interfaces\EventRepositoryInterface;
use App\Mail\EventPublishRequestMail;
use App\Models\User;
use App\Models\Event;
use App\Models\EventMeta;
use App\Enums\EventStatus;
use App\Http\helper;

class EventController extends Controller
{
    protected $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
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

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'event_date' => 'required|date',
            'ticket_price' => 'required|numeric',
            'total_tickets' => 'required|integer',
            // 'available_tickets' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'description' => 'required|string',
        ]);

        try {
            $user = auth()->user();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $fileName = helper::generateUniqueToken('events', 'image', 10) . '.' . $file->getClientOriginalExtension();
                $imagePath = $file->storeAs('events', $fileName, 'public');
            }

            $event = Event::updateOrCreate([
                'user_id' => $user->id,
                'category_id' => $request->category_id,
                'title' => $request->title,
                'description' => $request->description,
                'location' => $request->location,
                'event_date' => $request->event_date,
                'ticket_price' => $request->ticket_price,
                'total_tickets' => $request->total_tickets,
                'available_tickets' => $request->total_tickets,
                'image' => $fileName,
            ]);

            if ($event) {
                $adminEmail = User::where('role', 'administrator')->value('email');

                $requestData = [
                    'name' => $user->name,
                    'email' => $user->email,
                    'status' => 'pending',
                    'description' => $request->description,
                ];

                $eventMeta = $event->metas()->updateOrCreate([
                    'key' => 'event_request',
                ],[
                    'value' => $requestData,
                ]);

                Mail::to($adminEmail)->queue((new EventPublishRequestMail($user))->delay(now()->addSeconds(5)));
            }

            return response()->json([
                'message' => 'Event created successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function eventRequest(Request $request, Event $event) {
        $request->validate([
            'description' => 'required|string',
        ]);

        try {
            $user = auth()->user();

            if (!$user->events()->where('id', $event->id)->exists()) {
                return response()->json([
                    'message' => 'this is not your event, so you cannot send request for approval.',
                ], 500);
            }

            if ($event->status == EventStatus::APPROVED) {
                return response()->json([
                    'message' => 'this event is already approved',
                ], 500);
            }

            $record = $event->metas()->latest()->first();

            if ($record) {
                $array = json_encode($record->value);
                $value = json_decode($array);
                $status = $value->status;

                if (!isset($status) || $status == 'pending') {
                    return response()->json([
                        'message' => 'you are already send request for event approval'
                    ], 500);
                }
            }

            $requestData = [
                'status' => 'pending',
                'description' => $request->description,
            ];

            $eventMeta = $event->metas()->where('key', 'event_request')->update([
                'value' => $requestData,
            ]);

            $eventStatus = $event->update(['status' => 'pending']);

            $adminEmail = User::where('role', 'administrator')->get()->value('email');

            Mail::to($adminEmail)->queue((new EventPublishRequestMail($user))->delay(now()->addSeconds(5)));

            return response()->json([
                'message' => 'Your request has been sent to the admin.',
            ], 200);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function index(Request $request) {
        try {
            return $this->eventRepository->events($request);
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
            $user = auth()->user();

            if (!$user->events()->where('id', $event->id)->exists()) {
                return response()->json([
                    'message' => 'This is not your event, so you cannot update it.',
                ], 500);
            }

            $event->update($request->only('title', 'description', 'location', 'event_date', 'ticket_price', 'total_tickets'));

            return response()->json([
                'message' => 'Your event updated successfully.',
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
            $user = auth()->user();

            if (!$user->events()->where('id', $event->id)->exists()) {
                return response()->json([
                    'message' => 'This is not your event, so you cannot delete it.'
                ]);
            }

            $event->metas()->delete();

            $event->delete();

            return response()->json([
                'message' => 'Your event was deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function showEventTickets(Event $event)
    {
        try {
            $user = auth()->user();

            if (!$user->events()->where('id', $event->id)->exists()) {
                return response()->json([
                    'message' => 'This is not your event, so can not see its details.',
                ], 500);
            }

            $tickets = $event->tickets()->with([
                'user:id,name,email,phone',
                'event:id,title,event_date'
            ])->orderByDesc('id')->get();

            return response()->json([
                'event all tickets' => $tickets
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}