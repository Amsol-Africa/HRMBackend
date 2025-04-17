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
            <h2>Your Business Status: {{ ucfirst($status) }}</h2>
        </div>
        <div class="body">
            <p>Your business, {{ $business->company_name }}, has been {{ $status }}.</p>
            <p><strong>Remarks:</strong> {{ $remarks }}</p>
            @if($status === 'deactivated')
            <p>Please contact the administrator for further details.</p>
            @endif
            <a href="{{ $loginUrl }}"
                style="display: inline-block; padding: 10px 20px; background: #068f6d; color: white; text-decoration: none;">Log
                In</a>
        </div>
        <div class="footer">
            <p>&copy; {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>

</html>