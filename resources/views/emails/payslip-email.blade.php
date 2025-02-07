<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invitation to Join</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 30px auto; background: #ffffff; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #333333; text-align: center; margin-bottom: 20px;">You’re Invited to Join {{ config('app.name') }}</h2>
        <p style="color: #555555; font-size: 16px;">
            Hello,
        </p>
        <p style="color: #555555; font-size: 16px;">
            You have been invited to join <strong>{{ config('app.name') }}</strong>, a platform designed to help you manage your HR, Finance your Employees and collaborate seamlessly.
        </p>
        <p style="color: #555555; font-size: 16px;">
            To get started, click the button below to register your account and your business:
        </p>
        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ $url }}" style="background-color: #007bff; color: #ffffff; font-size: 16px; padding: 12px 25px; text-decoration: none; border-radius: 5px; display: inline-block; box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);">
                Register Now
            </a>
        </div>
        <p style="color: #555555; font-size: 14px; line-height: 1.5;">
            If the button above doesn’t work, copy and paste the following link into your browser:
        </p>
        <p style="word-break: break-all; color: #007bff; font-size: 14px;">
            <a href="{{ $url }}" style="color: #007bff; text-decoration: none;">{{ $url }}</a>
        </p>
        <p style="color: #999999; font-size: 12px; text-align: center; margin-top: 30px;">
            If you did not expect this email, you can safely ignore it.
        </p>
    </div>
</body>
</html>
