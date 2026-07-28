@component('mail::message')
# Hello {{ $event->user->name }}

reminder for your {{ $event->title }} event.

{{ $reminder }}

@component('mail::subcopy')
This email was sent automatically. Please do not reply.
@endcomponent

@endcomponent