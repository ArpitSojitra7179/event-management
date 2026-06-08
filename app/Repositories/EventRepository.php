<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use App\Models\EventCategory;

class EventRepository implements EventRepositoryInterface
{
	public function categories() {
		$category = EventCategory::orderByDesc('id')->cursorPaginate(10);

		return $category;
	}

	public function events(Request $request) {

		$search = $request->query('search');
		$category_id = $request->query('category_id');
		$location = $request->query('location');

		$events = Event::when($search, function($query) use ($search) { 
				$query->whereAny(['title', 'description'], 'like', "%$search%");
        })
		->when($category_id, function($query) use ($category_id) {
				$query->where('category_id', $category_id);
		})
		->when($location, function($query) use ($location) {
				$query->where('location', 'like', "%$location%");
		})->where('event_date', '>', now())->orderByDesc('id')->paginate();
		
		return $events;
	}
}