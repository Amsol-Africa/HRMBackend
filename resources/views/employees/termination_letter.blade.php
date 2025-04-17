<!DOCTYPE html>
<html>

<head>
    <title>Termination Letter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background-color: #ffffff;
            color: #333333;
        }

        .letter-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid #e0e0e0;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
        }

        .letter-header {
            text-align: center;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .logo {
            max-height: 80px;
            margin-bottom: 10px;
        }

        .letter-body {
            line-height: 1.6;
            font-size: 16px;
        }

        .letter-body p {
            margin-bottom: 16px;
        }

        .subject {
            font-weight: bold;
            font-size: 18px;
            color: #007BFF;
        }

        .signature {
            margin-top: 50px;
        }

        .signature p {
            margin: 4px 0;
        }
    </style>
</head>

<body>
    <div class="letter-container">
        <div class="letter-header">
            @if($business->logo)
            <img src="{{ $business->logo }}" alt="Company Logo" class="logo">
            @endif
            <h2 style="color: #007BFF;">{{ $business->company_name }}</h2>
            <p>{{ $business->address }}</p>
        </div>

        <div class="letter-body">
            <p>{{ now()->format('F d, Y') }}</p>

            <p>
                {{ $employee->user->name }}<br>
                {{ $employee->address ?? $employee->permanent_address }}
            </p>

            <p>Dear {{ $employee->user->name }},</p>

            <p class="subject">Subject: Termination of Employment</p>

            <p>We regret to inform you that your employment with <strong>{{ $business->company_name }}</strong> will be
                terminated effective <strong>{{ $action_date->format('F d, Y') }}</strong>.</p>

            <p><strong>Reason for Termination:</strong> {{ $reason }}</p>

            @if($description)
            <p><strong>Details:</strong> {{ $description }}</p>
            @endif

            <p>Please return any company property in your possession by the termination date. Your final paycheck,
                including any accrued benefits, will be processed per company policy.</p>

            <p>We thank you for your contributions and wish you the best in your future endeavors.</p>

            <p>Sincerely,</p>

            <div class="signature">
                <p><strong>{{ auth()->user()->name }}</strong></p>
                <p>Human Resources</p>
                <p>{{ $business->company_name }}</p>
            </div>
        </div>
    </div>
</body>

</html>