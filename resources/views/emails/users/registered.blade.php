@component('mail::message')
<img src="data:image/png;base64,{{ $logo }}" alt="CloudWage" style="display: block; margin: 20px auto;width: 250px;">

<br>
You have successfully completed the registration process. Please click the link below to activate your account.

<br>
@component('mail::button', ['url' => route('activate', encrypt($user->activation_code))])
Activate Now!
@endcomponent

or copy the following link to your browser:

{{ route('activate', encrypt($user->activation_code)) }}


Thanks,<br>
{{ config('app.name') }}.
@endcomponent
