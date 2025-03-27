<!DOCTYPE html>
<html>

<head>
    <title>Your Payslip for {{ $payrollPeriod }}</title>
    <style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 0;
    }

    .email-container {
        max-width: 600px;
        margin: 20px auto;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .email-header {
        background-color: #004a99;
        color: #ffffff;
        text-align: center;
        padding: 20px;
        font-size: 24px;
        font-weight: bold;
    }

    .email-body {
        padding: 20px;
        color: #333333;
        line-height: 1.6;
    }

    .email-body p {
        margin-bottom: 15px;
    }

    .email-footer {
        background-color: #f4f7fc;
        text-align: center;
        padding: 15px;
        font-size: 14px;
        color: #666666;
    }
    </style>
</head>

<body>
    <div class="email-container">
        <!-- Header -->
        <div class="email-header">
            Your Payslip is Ready
        </div>

        <!-- Body -->
        <div class="email-body">
            <p>Hello, <strong>{{ $employeeName }}</strong>,</p>
            <p>Your payslip for <strong>{{ $payrollPeriod }}</strong> is now available.</p>
            <p>Please find the attached document containing details of your salary and deductions.</p>
            <p>If you have any questions, feel free to reach out to HR.</p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            Best Regards, <br>
            <strong>{{ config('app.name') }} Team</strong> <br>
            <em>Ensuring your financial well-being.</em>
        </div>
    </div>
</body>

</html>