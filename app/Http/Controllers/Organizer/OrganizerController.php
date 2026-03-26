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
    protected $eventRepo;

    public function __construct(EventRepositoryInterface $eventRepo)
    {
        $this->eventRepo = $eventRepo;
    }

    public function index()
    {
        return $this->eventRepo->getAllEventCategories();
    }

    public function createEvent(Request $request, User $user)
    {
        $validated = $request->validate([
            'category_id' => 'required|integer',
            'title' => 'required|string',
            'description' => 'required|string',
            'location' => 'required|string',
            'event_date' => 'required|date',
            'ticket_price' => 'required|numeric',
            'total_tickets' => 'required|integer',
            'available_tickets' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,svg|max:2048',
        ]);

        try {
            $user = auth()->user();

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('events', 'public');
                $validated['image'] = $imagePath;
            }

            $event = Event::updateOrCreate([
                'user_id' => $user->id,
                'category_id' => $validated['category_id'],
                'title' => $validated['title'],
                'description' => $validated['description'],
                'location' => $validated['location'],
                'event_date' => $validated['event_date'],
                'ticket_price' => $validated['ticket_price'],
                'total_tickets' => $validated['total_tickets'],
                'available_tickets' => $validated['available_tickets'],
                'image' => $validated['image'],
            ]);

            return response()->json([
                'message' => 'Event created successfully.',
                'event' => $event,
                'image_url' => asset('storage/' . $event->image),
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something Went Wrong.',
            ], 500);
        }
    }
}
