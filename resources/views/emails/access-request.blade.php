<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Request</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; background-color: #f9f9f9; margin: 0; padding: 0;">
    <div style="max-width: 600px; margin: 30px auto; background: #ffffff; padding: 20px; border: 1px solid #e0e0e0; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);">
        <h2 style="color: #333333; text-align: center; margin-bottom: 20px;">Access Request</h2>
        <p style="color: #555555; font-size: 16px;">
            Hello,
        </p>
        <p style="color: #555555; font-size: 16px;">
            <strong>{{ $accessRequest->requester->name }}</strong> has requested access to your account on <strong>{{ config('app.name') }}</strong>.
        </p>
        <p style="color: #555555; font-size: 16px;">
            Please log in to your account to review and respond to the request.
        </p>
        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('login') }}" style="background-color: #007bff; color: #ffffff; text-decoration: none; font-size: 16px; padding: 10px 20px; border-radius: 5px; display: inline-block;">
                Log In Now
            </a>
        </div>
        <p style="color: #999999; font-size: 12px; text-align: center; margin-top: 30px;">
            If you did not expect this email, you can safely ignore it.
        </p>
    </div>
</body>
</html>
