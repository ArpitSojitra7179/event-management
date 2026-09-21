<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use App\Models\EventCategory;

class EventRepository implements EventRepositoryInterface
{
	public function categories() {
		$category = EventCategory::orderByDesc('created_at')->cursorPaginate(10);

		return $category;
	}

	public function event(Event $event) {
		$days = now()->startOfDay()
        ->diffInDays($event->event_date->startOfDay());

	    $event->event_date_human = $days <= 7
	        ? match ($days) {
	            0 => 'Today',
	            1 => '1 day to go',
	            default => "{$days} days to go",
	        }
	        : $event->event_date;

	    return $event;
	}

	public function events(Request $request) {

		$search = $request->query('search');
		$category_id = $request->query('category_id');
		$location = $request->query('location');
		$order = $request->query('order', 'desc');

		$events = Event::when($search, function($query) use ($search) { 
				$query->whereAny(['title', 'description'], 'like', "%$search%");
        })
		->when($category_id, function($query) use ($category_id) {
				$query->where('category_id', $category_id);
		})
		->when($location, function($query) use ($location) {
				$query->where('location', 'like', "%$location%");
		})->whereDate('event_date', '>=', now())->orderBy('created_at', $order)->paginate(10)
		->through(function ($event) {

	    	$days = now()->startOfDay()
	        ->diffInDays($event->event_date->startOfDay());

	    	$event->event_date_human = $days <= 7
		        ? match ($days) {
		            0 => 'Today',
		            1 => '1 day to go',
		            default => "{$days} days to go",
		        }
		        : $event->event_date;

	    	return $event;
		});
		
		return $events;
	}
}