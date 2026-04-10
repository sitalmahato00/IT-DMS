@component('mail::message')

<div style="text-align:center;">
	<img src="{{ asset('build/icons/it-logo.png') }}" alt="Manmohan Memorial Polytechnic" style="height:56px;margin:0 auto 8px;" />
</div>

# Manmohan Memorial Polytechnic

Hello!

A sign-in link has been requested for your account. This link expires in {{ $expiresMinutes }} minutes.

@component('mail::button', ['url' => $url])
Confirm Sign in
@endcomponent

@component('mail::button', ['url' => $cancelUrl, 'color' => 'danger'])
Cancel sign-in
@endcomponent

If you did not request this link, you can safely ignore this email.

Regards,

Manmohan Memorial Polytechnic

@endcomponent

