@component('mail::message')
Hi,

We got a request to reset your password,Please use this code to complete this process.

@component('mail::button',['url' => ''])
{{$verification_code}}
@endcomponent

Thanks,<br>
{{ 'Ga3aaan' }}
@endcomponent