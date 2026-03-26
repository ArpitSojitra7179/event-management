<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Interfaces\EventRepositoryInterface;

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
}
