<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventReminderMail;
use App\Models\Ticket;

class SendEventReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $ticket;
    public $leftDays;

    public function __construct($ticket, $leftDays)
    {
        $this->ticket = $ticket;
        $this->leftDays = $leftDays;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $user = $this->ticket->user;

        if ($this->leftDays == 2) {
            $reminder = 'Your event is after 2 days';
        } elseif ($this->leftDays == 1) {
            $reminder = 'Your event is tomorrow';
        } else {
            $reminder = 'Your event is today';
        }

        Mail::to($user->email)->send(new EventReminderMail($this->ticket, $this->leftDays, $reminder));
    }
}
