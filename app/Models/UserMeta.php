<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class UserMeta extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'user_id',
        'key',
        'value',
    ];

    public function user() {
        return $this->belognsTo(User::class);
    }
}
