@component('mail::message')

Hello {{ $event->user->name ?? 'User' }},

Your event request has been {{ ucfirst($event->status) }} by the administrator.

@component('mail::panel')
Status: {{ ucfirst($event->status) }}
@endcomponent

@if($event->status === 'rejected' && !empty($reason))
## Rejection Reason <br>
{{ $reason }}
@endif

@if($event->status === 'approved')
Your event is now available according to the platform's publishing process.
@endif

Thank you for using our platform.

@component('mail::subcopy')
This is an automated email. Please do not reply to this message.
@endcomponent

Regards,
{{ config('app.name') }}
@endcomponent