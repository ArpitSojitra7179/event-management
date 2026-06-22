<?php

namespace App\Http;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class helper
{
	public static function generateUniqueToken($table = null, $column = null, $length = 12) {
		do {
			$token = Str::random($length);

			if ($table && $column) {
				$exists = DB::table($table)->where($column, $token)->exists();
			} else {
				$exists = false;
			}
		} while ($exists);

		return $token;
	}
}