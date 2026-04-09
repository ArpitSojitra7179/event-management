@component('mail::message')
# Hello admin i am {{ $user->name }} 

@component('mail::panel')
Dear Admin, I will request that you publish my event, so please approve my request and publish my event.
@endcomponent

@component('mail::button', ['url' => url('http://localhost:8000/admin/request-list'), 'color' => 'success'])
Show request list
@endcomponent

@endcomponent