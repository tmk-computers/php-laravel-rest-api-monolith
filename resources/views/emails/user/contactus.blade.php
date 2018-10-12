@component('mail::message')

<h3>New Enquiry From </h3>

<p>Full Name: {{$data['name']}} </p>
<p>Email ID : {{$data['email']}} </p>
<p>Mobile No: {{$data['mobile']}} </p>
<p>Subject: {{$data['subject']}} </p>
<p>Message: {{$data['message']}} </p>



Thanks,
Your Company
@endcomponent