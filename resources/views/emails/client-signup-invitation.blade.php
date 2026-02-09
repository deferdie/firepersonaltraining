<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete your setup</title>
</head>
<body style="font-family: sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="font-size: 1.5rem;">Welcome to the platform</h1>

    <p>Hi {{ $client->name }},</p>

    <p>{{ $client->trainer->name ?? 'Your trainer' }} has invited you to join their platform. Complete your setup to access your client dashboard and stay connected.</p>

    <p style="margin: 28px 0;">
        <a href="{!! $signedUrl !!}" style="display: inline-block; background: #2563eb; color: #fff; text-decoration: none; padding: 12px 24px; border-radius: 8px; font-weight: 600;">Complete setup</a>
    </p>

    <p style="font-size: 0.875rem; color: #666;">This link expires in 7 days. If you didn't expect this email, you can ignore it.</p>

    <p style="margin-top: 32px;">Thanks,<br>{{ $client->trainer->name ?? config('app.name') }}</p>
</body>
</html>
