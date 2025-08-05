<!DOCTYPE html>
<html>

<head>
    <style>
        .container {
            max-width: 600px;
            margin: auto;
            font-family: Arial, sans-serif;
        }

        .header {
            background: #068f6d;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .body {
            padding: 20px;
            background: #f9f9f9;
        }

        .footer {
            background: #e0e0e0;
            padding: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h2>Invitation to Join {{ $business->company_name }}</h2>
        </div>
        <div class="body">
            <p>You have been invited to join {{ $business->company_name }} on {{ config('app.name') }}.</p>
            <p>Please register to gain access.</p>
            <a href="{{ $registerUrl }}"
                style="display: inline-block; padding: 10px 20px; background: #068f6d; color: white; text-decoration: none;">Register
                Now</a>
        </div>
        <div class="footer">
            <p>&copy; {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>