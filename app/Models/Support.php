<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Support extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'user_id',
        'description',
        'priority',
        'department',
        'status',
    ];
}
