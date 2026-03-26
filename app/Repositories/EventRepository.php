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
}