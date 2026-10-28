<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Password - Pedro's Artisan Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/theme.css') }}?v={{ filemtime(public_path('assets/css/theme.css')) }}">
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: var(--background);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        .bg-glow {
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(60px);
        }

        .login-card {
            background: var(--sidebar);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
        }

        .brand-logo {
            font-family: var(--font-bebas);
            font-size: 2.5rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 0.5rem;
            letter-spacing: 2px;
        }

        .brand-sub {
            font-family: var(--font-montserrat);
            font-size: 0.8rem;
            color: var(--muted-foreground);
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 4px;
            margin-bottom: 2.5rem;
        }

        .form-label {
            font-family: var(--font-montserrat);
            font-weight: 600;
            font-size: 0.85rem;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: white;
            padding: 0.75rem 1rem;
            font-family: var(--font-montserrat);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
            color: white;
        }

        .btn-login {
            background: var(--primary);
            color: black;
            font-family: var(--font-bebas);
            font-size: 1.2rem;
            letter-spacing: 1px;
            padding: 0.75rem;
            border-radius: 12px;
            width: 100%;
            margin-top: 1.5rem;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: #e5c060;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px -10px rgba(212, 175, 55, 0.5);
        }

        .footer-links {
            text-align: center;
            margin-top: 2rem;
            font-family: var(--font-montserrat);
            font-size: 0.85rem;
            color: var(--muted-foreground);
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .footer-links a:hover {
            color: #e5c060;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border-left: 4px solid #ef4444;
            color: #fca5a5;
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        .success-message {
            background: rgba(34, 197, 94, 0.1);
            border-left: 4px solid #22c55e;
            color: #86efac;
            padding: 1rem;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="bg-glow" style="top: -10%; left: -10%;"></div>
    <div class="bg-glow" style="bottom: -10%; right: -10%;"></div>

    <div class="login-card">
        <div class="text-center mb-4">
            <img src="{{ asset('assets/img/logo.jpg') }}?v={{ time() }}" alt="Pedro's Artisan Logo" style="height: 80px; border-radius: 50%; border: 3px solid var(--primary); box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);">
        </div>
        <div class="brand-logo">PEDRO'S ARTISAN</div>
        <div class="brand-sub">Create New Password</div>

        @if(session('success'))
            <div class="success-message">{{ session('success') }}</div>
        @endif

        @if($errors->any())
            <div class="error-message">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            
            <!-- Hidden Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="mb-3">
                <label for="email" class="form-label">Verify Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $email ?? old('email') }}" required readonly>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <div class="password-group">
                    <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required autofocus>
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)" tabindex="-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="password-group">
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                    <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" tabindex="-1">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login">Change Password</button>
        </form>
    </div>

    <script>
        function togglePasswordVisibility(inputId, btn) {
            const input = document.getElementById(inputId);
            const eyeIcon = btn.querySelector('.eye-icon');
            const eyeOffIcon = btn.querySelector('.eye-off-icon');
            
            if (input.type === 'password') {
                input.type = 'text';
                eyeIcon.classList.add('d-none');
                eyeOffIcon.classList.remove('d-none');
            } else {
                input.type = 'password';
                eyeIcon.classList.remove('d-none');
                eyeOffIcon.classList.add('d-none');
            }
        }
    </script>

</body>
</html>
