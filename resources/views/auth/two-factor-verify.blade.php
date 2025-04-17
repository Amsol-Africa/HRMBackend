<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Two-Factor Authentication</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
        background: #f4f7fa;
        margin: 0;
        font-family: Arial, sans-serif;
    }

    .container {
        text-align: center;
        max-width: 600px;
        padding: 20px;
    }

    h1 {
        font-size: 24px;
        color: #333;
        margin-bottom: 10px;
    }

    p {
        font-size: 16px;
        color: #666;
        margin-bottom: 20px;
    }

    .code-inputs {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 20px;
    }

    .code-input {
        width: 50px;
        height: 50px;
        text-align: center;
        font-size: 24px;
        border: 2px solid #ccc;
        border-radius: 8px;
        outline: none;
        transition: border-color 0.3s, background-color 0.3s;
    }

    .code-input:focus {
        border-color: #007bff;
    }

    .code-input.success {
        border-color: #28a745;
        background-color: #e6f4ea;
    }

    .code-input.error {
        border-color: #dc3545;
        background-color: #f8d7da;
    }

    .verify-btn {
        background-color: #007bff;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .verify-btn:hover {
        background-color: #0056b3;
    }

    .resend-link {
        display: inline-block;
        margin-top: 15px;
        color: #007bff;
        text-decoration: none;
        font-size: 14px;
    }

    .resend-link:hover {
        text-decoration: underline;
    }

    .alert {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 5px;
    }

    .alert-success {
        background-color: #d4edda;
        color: #155724;
    }

    .alert-danger {
        background-color: #f8d7da;
        color: #721c24;
    }
    </style>
</head>

<body>
    <div class="container">
        <h1>{{ $page }}</h1>
        <p>{{ $description }}</p>

        @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
        @endif

        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('2fa.verify') }}" id="2fa-form">
            @csrf
            <div class="code-inputs">
                <input type="text" class="code-input" maxlength="1" required autocomplete="off">
                <input type="text" class="code-input" maxlength="1" required autocomplete="off">
                <input type="text" class="code-input" maxlength="1" required autocomplete="off">
                <input type="text" class="code-input" maxlength="1" required autocomplete="off">
                <input type="text" class="code-input" maxlength="1" required autocomplete="off">
                <input type="text" class="code-input" maxlength="1" required autocomplete="off">
            </div>
            <button type="submit" class="verify-btn">Verify</button>
        </form>

        <p>
            Didn't receive a code? <a href="#" class="resend-link" id="resend-code">Resend Code</a>
        </p>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const inputs = document.querySelectorAll('.code-input');
        const form = document.getElementById('2fa-form');
        const resendLink = document.getElementById('resend-code');

        // Handle input navigation and pasting
        inputs.forEach((input, index) => {
            // input.addEventListener('input', () => {
            //     input.value = input.value.replace(/\D/g, '');
            //     if (input.value.length === 1 && index < inputs.length - 1) {
            //         inputs[index + 1].focus();
            //     }
            // });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && input.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = e.clipboardData.getData('text').replace(/\D/g, '');
                if (pasted.length > 0) {
                    for (let i = 0; i < Math.min(pasted.length, inputs.length); i++) {
                        inputs[i].value = pasted[i];
                        inputs[i].classList.remove('success', 'error');
                    }
                    const focusIndex = Math.min(pasted.length, inputs.length - 1);
                    inputs[focusIndex].focus();
                    if (pasted.length === 6) {
                        form.dispatchEvent(new Event('submit'));
                    }
                }
            });
        });

        // Handle form submission
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const code = Array.from(inputs).map(input => input.value).join('');
            if (code.length !== 6) {
                inputs.forEach(input => input.classList.add('error'));
                await Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please enter a 6-digit code.'
                });
                inputs.forEach(input => input.classList.remove('error'));
                return;
            }

            try {
                console.log('Submitting 2FA code:', code);
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        code
                    })
                });

                console.log('2FA Response Status:', response.status);
                const data = await response.json();
                console.log('2FA Response Data:', data);

                if (response.ok) {
                    console.log('Success path entered');
                    inputs.forEach(input => {
                        input.classList.add('success');
                        input.classList.remove('error');
                    });
                    await Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        confirmButtonText: 'OK'
                    });
                    if (data.data?.redirect_url) {
                        console.log('Redirecting to:', data.data.redirect_url);
                        window.location.href = data.data.redirect_url;
                        return;
                    } else {
                        console.log('No redirect_url provided');
                        await Swal.fire({
                            icon: 'warning',
                            title: 'Warning',
                            text: 'No redirect URL provided.'
                        });
                    }
                } else {
                    console.log('Error path entered:', data.message);
                    inputs.forEach(input => {
                        input.classList.add('error');
                        input.classList.remove('success');
                    });
                    await Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'Invalid or expired verification code.'
                    });
                    inputs.forEach(input => input.classList.remove('error'));
                }
            } catch (error) {
                console.error('2FA Submission Error:', error);
                inputs.forEach(input => {
                    input.classList.add('error');
                    input.classList.remove('success');
                });
                await Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to verify code. Please try again.'
                });
                inputs.forEach(input => input.classList.remove('error'));
            }
        });

        // Handle resend code
        resendLink.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                console.log('Requesting resend code');
                const response = await fetch('{{ route("2fa.resend") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                console.log('Resend Response Status:', response.status);
                const data = await response.json();
                console.log('Resend Response Data:', data);
                await Swal.fire({
                    icon: response.ok ? 'success' : 'error',
                    title: response.ok ? 'Success' : 'Error',
                    text: data.message
                });
                if (response.ok) {
                    inputs.forEach(input => {
                        input.value = '';
                        input.classList.remove('success', 'error');
                    });
                    inputs[0].focus();
                }
            } catch (error) {
                console.error('Resend Error:', error);
                await Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to resend code.'
                });
            }
        });
    });
    </script>
</body>

</html>