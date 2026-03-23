@component('mail::message')
# Hello I am {{ $user->name }}

@component('mail::panel')
Dear Admin pls approve my request
@endcomponent

@component('mail::table')
| Name              | Email              |
|-------------------|--------------------|
| {{ $user->name }} | {{ $user->email }} |
@endcomponent

@component('mail::button', ['url' => url('http://localhost:8000/admin/organizer-request-list'), 'color' => 'success'])
Show Request List
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent