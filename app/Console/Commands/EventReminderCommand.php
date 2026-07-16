<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Event;
use App\Jobs\SendEventReminderJob;
use App\Enums\TicketStatus;

class EventReminderCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:event-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reminderDays = [
            $beforeTwoDay = 2,
            $beforeOneDay = 1,
            $sameDay = 0,
        ];

        $events = Event::whereDate('event_date', '>=', today())
            ->with([
                'tickets' => function ($query) {
                    $query->where('booking_status', TicketStatus::BOOKED);
                },
                'tickets.user'
            ])
            ->chunk(50, function ($events) use ($reminderDays) {
                foreach ($events as $event) {

                    $leftDays = today()->diffInDays($event->event_date);

                    if (!in_array($leftDays, $reminderDays)) {
                        continue;
                    }

                    foreach ($event->tickets as $ticket) {
                        $user = $ticket->user;

                        SendEventReminderJob::dispatch($ticket, $leftDays)->delay(now()->addSeconds(3))->onQueue('default');
                    }

                    $this->info("Reminder send for {$event->title}");
                }
            });

        return Command::SUCCESS;
    }
}
