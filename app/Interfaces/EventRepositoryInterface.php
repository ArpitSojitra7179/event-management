<?php

namespace App\Interfaces;

interface EventRepositoryInterface
{
	public function getAllEventCategories();
	public function getAllEvent(array $data);
}