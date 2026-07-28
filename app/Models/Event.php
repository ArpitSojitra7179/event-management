<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Enums\EventStatus;

class Event extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'location',
        'event_date',
        'ticket_price',
        'total_tickets',
        'available_tickets',
        'image',
        'status',
    ];

    protected $casts = [
        'status' => EventStatus::class,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function category() {
        return $this->belongsTo(EventCategory::class);
    }

    public function tickets() {
        return $this->hasMany(Ticket::class);
    }

    public function metas() {
        return $this->hasMany(EventMeta::class);
    }
}
