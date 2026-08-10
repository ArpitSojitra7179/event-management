<?php

namespace App\Enums;

enum SupportStatus: string
{
	case PENDING = 'pending';
	case OPEN = 'open';
	case WORKING = 'working';
	case CLOSE = 'close';
}