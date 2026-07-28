<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;
use App\Enums\TransactionStatus;

class Transaction extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'ticket_id',
        'payment_method',
        'transaction_id',
        'amount',
        'key',
        'services',
        'gateway',
        'payment_link',
        'payment_status',
        'paid_at',
    ];

    protected $casts = [
        'payment_status' => TransactionStatus::class,
    ];

    public function ticket() {
        return $this->belongsTo(Ticket::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
