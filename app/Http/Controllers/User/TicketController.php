<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Http\helper;

class TicketController extends Controller
{
    public function book(Request $request, Event $event) {
        $request->validate([
            'quantity' => 'required|integer',
        ]);

        try {
            $user = auth()->user();

            if ($event->status !== 'approved') {
                return response()->json([
                    'message' => 'This event is currently unapproved; therefore, tickets cannot be booked at this time.',
                ], 500);
            }

            if ($event->event_date < now()) {
                return response()->json([
                    'message' => 'this event is alraedy organized, so cannot booked ticket.',
                ], 500);
            }

            $total_price = $event->ticket_price * $request->quantity;

            $ticket = Ticket::create([
                'user_id' => $user->id,
                'event_id' => $event->id,
                'quantity' => $request->quantity,
                'total_price' => $total_price,
            ]);

            $key = helper::generateUniqueToken('transactions', 'key', 32);

            $transaction = Transaction::create([
                'ticket_id' => $ticket->id,
                'amount' => $total_price,
                'key' => $key,
                'services' => 'events tickets payment',
                'gateway' => 'stripe',
                'payment_status' => 'pending',
            ]);

            return response()->json([
                'message' => 'Data store successfully.',
            ], 200);
        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
}
