@component('mail::message')

# Hello {{ $user->name }}
Welcome to **{{ config('app.name') }}**!

@component('mail::panel')
You are registered successfullly.
@endcomponent

@component('mail::table')
| ID              | Name              | Role              |
|-----------------|-------------------|-------------------|
| {{ $user->id }} | {{ $user->name }} | {{ $user->role }} |
@endcomponent

@component('mail::subcopy')
This email was sent automatically. Please do not reply.
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent