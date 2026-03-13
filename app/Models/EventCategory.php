<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class EventCategory extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'name',
    ];

    public function events() {
        return $this->hasMany(Event::class);
    }
}
