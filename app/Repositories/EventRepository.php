<?php

namespace App\Repositories;

use App\Interfaces\EventRepositoryInterface;
use App\Models\Event;
use App\Models\EventCategory;

class EventRepository implements EventRepositoryInterface
{
	public function getAllEventCategories() {
		return EventCategory::all();
	}

	public function getAllEvent(array $data) {
		return Event::query()->when($data['category_id'] ?? null, function ($query, $categoryId) {
			return $query->where('category_id', $categoryId);
		})->where('status', 'publish')->get();
	}
}