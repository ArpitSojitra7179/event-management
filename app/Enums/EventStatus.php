<?php

namespace App\Enums;

enum EventStatus: string
{
	case PENDING = 'pending';
	case APPROVED = 'approved';
	case REJECTED = 'rejected';
}