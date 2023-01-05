<x-mail::message>
# Hi,

You are receiving this email because we received a password reset request for your account.

<x-mail::button :url="$url" color="success">
    Reset Password
</x-mail::button>

If you did not request a password reset, no further action is required.

Thanks,<br>
{{config('app.name')}}

____

If you're having trouble clicking the "Reset Password" button, copy and paste the URL below into your web browser: <{{$url}}>

</x-mail::message>
