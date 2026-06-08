@component('mail::message')
# Hello I am {{ $user->name }}

@component('mail::panel')
Dear Admin, I kindly request that my account be promoted to organizer role.
@endcomponent

@component('mail::table')
| Name              | Email              |
|-------------------|--------------------|
| {{ $user->name }} | {{ $user->email }} |
@endcomponent

@component('mail::button', ['url' => url('http://eventmanagement.local/admin/organizer-list'), 'color' => 'success'])
Show Request List
@endcomponent

@component('mail::subcopy')
This email was sent automatically. Please do not reply.
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent