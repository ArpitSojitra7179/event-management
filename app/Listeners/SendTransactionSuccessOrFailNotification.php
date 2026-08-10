<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use App\Events\TransactionSuccessOrFail;
use App\Notifications\TransactionNotification;

class SendTransactionSuccessOrFailNotification
{
    
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransactionSuccessOrFail $event): void
    {
        $user = $event->user;
        $transaction = $event->transaction;

        $user->notify(new TransactionNotification($user, $transaction));
    }
}
