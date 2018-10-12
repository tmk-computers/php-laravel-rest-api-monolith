@component('mail::message')

<h2>Hi, {{$data['name']}} </h2>
<p>Your Registration is done. To Activate your Account<br>
<a href="{{url('activate-account')}}/{{$data['token']}}">Click Here</a>
</p>

Thanks,
Your Company
@endcomponent