@component('mail::message')

<h2>Hi, {{$data['name']}} </h2>
<p>Your Password is changed. Your new password is: {{$data['randomNewPassword']}}
</p>

Thanks,
Your Company
@endcomponent
