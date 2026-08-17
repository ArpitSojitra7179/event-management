<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use App\Enums\SupportStatus;

class Support extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'priority',
        'department',
        'status',
    ];

    protected $casts = [
        'status' => SupportStatus::class,
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function replies() {
        return $this->hasMany(SupportReplies::class);
    }
}
