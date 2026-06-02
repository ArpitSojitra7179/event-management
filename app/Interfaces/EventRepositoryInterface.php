<?php

namespace App\Interfaces;

use Illuminate\Http\Request;
use App\Models\Event;

interface EventRepositoryInterface
{
	public function categories();
	public function events(Request $request);
}