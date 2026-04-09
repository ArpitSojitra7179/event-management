<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use App\Models\EventCategory;

class EventRepository implements EventRepositoryInterface
{
	public function getCategories() {
		$category = EventCategory::all();

		return $category;
	}

	public function getEvents(Request $request) {

		$events = Event::when($request['category_id'] ?? null, function ($query, $categoryId) {
			return $query->where('category_id', $categoryId);
		})->where('status', 'publish')->get();
		
		return $events;
	}
}