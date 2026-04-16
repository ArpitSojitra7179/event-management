@component('mail::message')
# Hello {{ $user->name }}

@component('mail::panel')
Your request was approved by admin, now you premoted to organizer, so now can create events.
@endcomponent

@component('mail::subcopy')
This email is sent automatically. Please do not reply.
@endcomponent

@endcomponent