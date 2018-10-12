@component('mail::message')

<h2>Hi, {{$data['name']}} </h2>
<p>Your account is created. You need to verify your email.</p>

Your email verification code is : {{$data['code']}}

Thanks,
Your Company
@endcomponent
