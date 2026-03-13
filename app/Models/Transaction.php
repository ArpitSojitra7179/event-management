<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Transaction extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'ticket_id',
        'payment_method',
        'transaction_id',
        'amount',
        'payment_status',
    ];
}
