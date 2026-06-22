<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Stripe\Customer;
use Stripe\Charge;
use Stripe\PaymentIntent;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Http\helper;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccessOrFailMail;

class PaymentController extends Controller
{
    public function execute($key)
    {
        try {
            $transaction = Transaction::where('key', $key)->latest()->first();

            if (!$transaction) {
                return response()->json([
                    'message' => 'transaction key not found.',
                ], 404);
            }

            if ($transaction->payment_status == 'completed') {
                return response()->json([
                    'message' => 'Your payment has already been successfully processed, so no further action or payment is required.'
                ], 500);
            }

            $user = auth()->user();

            if (!$user->tickets()->where('id', $transaction->ticket_id)->exists()) {
                return response()->json([
                    'message' => 'Since these tickets do not belong to you, payment cannot be processed. Please double-check your booking details.',
                ], 500);
            }

            $user->createOrGetStripeCustomer();
            $user->updateStripeCustomer([
                'address' => [
                    'state' => $user->region_name,
                    'country' => $user->country_code,
                ],
                'shipping' => [
                    'name' => $user->name,
                    'address' => [
                        'line1' => $user->country_name,
                        'country' => $user->country_code,
                    ]
                ],
            ]);

            $checkoutCharge = $user->checkoutCharge($transaction->amount, $transaction->services, 1, [
                'mode' => 'payment',
                'currency' => 'usd',
                'success_url' => url(route('payment.success') . '?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => route('payment.cancel') . '?session_id={CHECKOUT_SESSION_ID}',
            ]);

            if ($checkoutCharge) {
                $transaction->update([
                    'payment_link' => $checkoutCharge->url,
                ]);
            }

        return response()->json([
            'success' => true,
            'checkout_url' => $checkoutCharge->url,
        ], 200);

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function verify(Request $request, $key)
    {
        try {
            $transaction = Transaction::where('key', $key)->latest()->first();

            if (!$transaction) {
                return response()->json([
                    'message' => 'transaction key not found.',
                ], 404);
            }

            if ($transaction->payment_status == 'completed') {
                return response()->json([
                    'message' => 'Your payment has already been successfully processed, so no further action or payment is required.',
                ], 500);
            }

            $sessionId = $request->query('session_id');

            if (!$sessionId) {
                return response()->json([
                    'message' => 'session_id parameter is required.'
                ], 400);
            }

            $email = $transaction->ticket->user->email;

            Stripe::setApiKey(env('STRIPE_SECRET'));

            $session = $transaction->ticket->user->stripe()->checkout->sessions->retrieve($sessionId); //Session::retrieve($sessionId);

            if ($session->payment_status == 'paid') {
                $transaction->update([
                    'transaction_id' => $session->payment_intent,
                    'payment_link' => null,
                    'payment_status' => 'completed',
                    'paid_at' => now(),
                ]);

                $transaction->ticket()->update([
                    'booking_status' => 'booked',
                ]);

                Mail::to($email)->queue((new PaymentSuccessOrFailMail($transaction))->delay(now()->addSeconds(5)));

                return response()->json([
                    'status' => true,
                    'message' => 'payment successful.'
                ], 200);
            } elseif ($session->payment_status == 'unpaid') {
                $transaction->update([
                    'payment_status' => 'cancelled',
                ]);

                Mail::to($email)->queue((new PaymentSuccessOrFailMail($transaction))->delay(now()->addSeconds(5)));

                return response()->json([
                    'status' => false,
                    'message' => 'payment cancelled.',
                ]);
            }

        } catch (\Exception $e) {
            report($e);

            return response()->json([
                'message' => 'Something went wrong.',
            ], 500);
        }
    }

    public function success()
    {
        return view('payment.success');
    }

    public function cancel()
    {
        return view('payment.cancel');
    }
   
}
