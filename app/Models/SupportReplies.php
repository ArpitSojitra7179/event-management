<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class SupportReplies extends Model
{
    use  HasApiTokens, HasFactory;

    protected $fillable = [
        'support_id',
        'reply_by_user',
        'message',
        'attachment',
    ];
}
