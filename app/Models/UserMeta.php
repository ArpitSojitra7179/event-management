<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class UserMeta extends Model
{
    use HasApiTokens, HasFactory;

    protected $table = 'user_meta';

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'value' => 'array',
    ];
}
