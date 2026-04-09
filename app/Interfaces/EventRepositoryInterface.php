<?php

namespace App\Interfaces;

use Illuminate\Http\Request;

interface EventRepositoryInterface
{
	public function getCategories();
	public function getEvents(Request $request);
}