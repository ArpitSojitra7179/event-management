@component('mail::message')

Hello {{ $user->name }},

Your organizer request has been reviewed by our team.

@if($status === 'rejected')
Unfortunately, your organizer request was not approved by admin. <br>
## Reason :
{{ $reason ?? 'No reason was provided.' }}
@else

Congratulations! Your organizer request has been approved. You can now access organizer features and start managing your events.
@endif

Thanks,
{{ config('app.name') }}

@component('mail::subcopy')
This is an automated email. Please do not reply to this message.
@endcomponent

@endcomponent