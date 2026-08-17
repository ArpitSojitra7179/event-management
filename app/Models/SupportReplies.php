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
        'agent_replies_id',
        'message',
        'attachment',
    ];

    public function support() {
        return $this->belongsTo(Support::class);
    }

    public function agent() {
        return $this->belongsTo(User::class, 'agent_replies_id');
    }
}
