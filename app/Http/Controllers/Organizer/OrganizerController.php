<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Interfaces\EventRepositoryInterface;
use App\Models\User;
use App\Models\Event;

class OrganizerController extends Controller
{
    protected $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function index()
    {
        try {
            return $this->eventRepository->getCategories();
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong',
            ], 500);
        }
    }

    public function createEvent(Request $request, User $user)
    {
        $request->validate([
            'category_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'event_date' => 'required|date',
            'ticket_price' => 'required|numeric',
            'total_tickets' => 'required|integer',
            'available_tickets' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,svg,webp|max:2048',
        ]);

        try {
            $user = auth()->user();

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
                $validated['image'] = $imagePath;
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
                'available_tickets' => $request->available_tickets,
                'image' => $request->image,
            ]);

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
}
