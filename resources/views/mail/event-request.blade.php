@component('mail::message')
# Hello admin i am {{ $user->name }} 

@component('mail::panel')
Dear Admin, please review and publish my event. Thank you for your time!
@endcomponent

@component('mail::button', ['url' => url('http://eventmanagement.local/admin/event-list'), 'color' => 'success'])
Show request list
@endcomponent

@component('mail::subcopy')
This email was sent automatically. Please do not reply.
@endcomponent

@endcomponent