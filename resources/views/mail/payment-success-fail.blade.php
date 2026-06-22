@component('mail::message')

Hello {{ $transaction->ticket->user->name }}

Your transaction is {{ $transaction->payment_status }}

@if($transaction->payment_status == 'completed')
Your transaction was completed and your ticket was booked.
@endif

@if($transaction->payment_status == 'cancelled')
Your transaction was faild.
@endif

Thank you for using our platform.

@component('mail::subcopy')
This is an automated email. Please do not reply to this message.
@endcomponent

Regards,
{{ config('app.name') }}
@endcomponent