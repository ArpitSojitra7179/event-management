<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class EventMeta extends Model
{
    use HasFactory, HasApiTokens;

    protected $fillable = [
        'event_id',
        'key',
        'value',
    ];

    public function event() {
        return $this->belongsTo(Event::class);
    }

    protected $casts = [
        'value' => 'array'
    ];
}
