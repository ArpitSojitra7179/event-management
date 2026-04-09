@component('mail::message')

# Hello {{ $user->name }}


@component('mail::panel')
You can requested to reset password pls click on below button.
@endcomponent

@component('mail::button', ['url' => url('http://eventmanagement.local/api/reset-password/{token}?email={$email}'), 'color' => 'success'])
Reset Password
@endcomponent

Thanks,<br>
{{ config('app.name') }}

@endcomponent