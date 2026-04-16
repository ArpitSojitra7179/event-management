@component('mail::message')
# Hello {{ $user->name }}

@component('mail::panel')
Your request was approved by admin, now your event is pubished.
@endcomponent

@component('mail::subcopy')
This email is sent automatically. Please do not reply.
@endcomponent

Thanks.
@endcomponent