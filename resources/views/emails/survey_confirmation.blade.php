<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Thank You for Your Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
        }

        .header img {
            max-height: 50px;
        }

        .content {
            padding: 20px;
            color: #333333;
            line-height: 1.6;
        }

        .content h2 {
            color: #004aad;
        }

        .footer {
            text-align: center;
            padding: 10px 0;
            color: #666666;
            font-size: 12px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <img src="{{ config('app.url') }}/media/amsol-logo.png" alt="{{ config('app.name') }} Logo">
        </div>
        <div class="content">
            <h2>Thank You, {{ $name }}!</h2>
            <p>We appreciate your feedback for our <strong>{{ $campaign_name }}</strong> campaign. Your input helps us
                improve our services and better meet your needs.</p>
            <p>If you have any further comments or questions, please feel free to reach out.</p>
            <p>Best regards,<br>The {{ config('app.name') }} Team</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>