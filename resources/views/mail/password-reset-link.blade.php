<!DOCTYPE html>
<html lang="en">
<body style="font-family: Arial, sans-serif; color: #333; line-height: 1.5;">
    <p>Hello,</p>
    <p>We received a request to reset the password for your {{ config('app.name') }} account.</p>
    <p>
        <a href="{{ $resetUrl }}" style="display: inline-block; padding: 10px 18px; background: #0da487; color: #fff; text-decoration: none; border-radius: 4px;">Reset Password</a>
    </p>
    <p>Or copy this link into your browser:<br><a href="{{ $resetUrl }}">{{ $resetUrl }}</a></p>
    <p>This link expires in 1 hour and can be used only once. If you did not ask for a reset, you can ignore this email.</p>
</body>
</html>
