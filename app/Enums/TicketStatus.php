<?php

namespace App\Enums;

enum TicketStatus: string
{
	case PENDING = 'pending';
	case BOOKED = 'booked';
}