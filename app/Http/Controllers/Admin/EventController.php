<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EventCategory;
use App\Interfaces\EventRepositoryInterface;

class EventController extends Controller
{
    protected $eventRepo;

    public function __construct(EventRepositoryInterface $eventRepo)
    {
        $this->eventRepo = $eventRepo;
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

    public function index(Request $request)
    {
        return $this->eventRepo->getAllEvent($request->all());
    }
}
