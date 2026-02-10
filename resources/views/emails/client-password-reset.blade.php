<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your new password</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="font-size: 1.5rem;">Your password has been reset</h1>

    <p>Hi {{ $client->name }},</p>

    <p>{{ $client->trainer->name ?? 'Your trainer' }} has reset your password. Use the credentials below to sign in to your client dashboard.</p>

    <p><strong>Your new password:</strong> <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 1rem;">{{ $plainPassword }}</code></p>

    <p style="margin: 28px 0;">
        <a href="{!! $loginUrl !!}" style="display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600;">Sign in to your dashboard</a>
    </p>

    <p style="font-size: 0.875rem; color: #666;">We recommend changing this password after you sign in. If you didn't request this, contact your trainer.</p>

    <p style="margin-top: 32px;">Thanks,<br>{{ $client->trainer->name ?? config('app.name') }}</p>
</body>
</html>
