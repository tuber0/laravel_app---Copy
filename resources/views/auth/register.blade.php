<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - Pedro's Artisan Shop</title>
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
            padding: 2rem 0;
            overflow-x: hidden;
        }

        .bg-glow {
            position: absolute;
            width: 800px;
            height: 800px;
            background: radial-gradient(circle, rgba(212, 175, 55, 0.05) 0%, rgba(15, 23, 42, 0) 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(80px);
        }

        .register-card {
            background: var(--sidebar);
            border: 1px solid var(--border);
            border-radius: 24px;
            padding: 3.5rem;
            width: 100%;
            max-width: 550px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(15px);
            position: relative;
        }

        .register-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 20px;
            right: 20px;
            height: 4px;
            background: linear-gradient(90deg, transparent, var(--primary), transparent);
            border-radius: 0 0 100px 100px;
        }

        .brand-logo {
            font-family: var(--font-bebas);
            font-size: 2.5rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 0.5rem;
            letter-spacing: 3px;
        }

        .brand-sub {
            font-family: var(--font-montserrat);
            font-size: 0.8rem;
            color: var(--muted-foreground);
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 5px;
            margin-bottom: 3rem;
        }

        .form-label {
            font-family: var(--font-montserrat);
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--muted-foreground);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.6rem;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--border);
            border-radius: 14px;
            color: white;
            padding: 0.85rem 1.25rem;
            font-family: var(--font-montserrat);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.08);
            color: white;
        }

        .btn-register {
            background: var(--primary);
            color: black;
            font-family: var(--font-bebas);
            font-size: 1.3rem;
            letter-spacing: 1.5px;
            padding: 0.9rem;
            border-radius: 14px;
            width: 100%;
            margin-top: 2rem;
            border: none;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-register:hover {
            background: #e5c060;
            transform: translateY(-3px);
            box-shadow: 0 15px 30px -10px rgba(212, 175, 55, 0.4);
        }

        .footer-links {
            text-align: center;
            margin-top: 2.5rem;
            font-family: var(--font-montserrat);
            font-size: 0.9rem;
            color: var(--muted-foreground);
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 700;
        }

        .error-message {
            background: rgba(239, 68, 68, 0.08);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .error-message li {
            color: #fca5a5;
            font-size: 0.85rem;
            font-family: var(--font-montserrat);
        }
    </style>
</head>
<body>

    <div class="bg-glow" style="top: 0; right: -20%;"></div>
    <div class="bg-glow" style="bottom: 0; left: -20%;"></div>

    <div class="register-card">
        <div class="text-center mb-4">
            <img src="{{ asset('assets/img/logo.jpg') }}?v={{ time() }}" alt="Pedro's Artisan Logo" style="height: 80px; border-radius: 50%; border: 3px solid var(--primary); box-shadow: 0 0 20px rgba(212, 175, 55, 0.2);">
        </div>
        <div class="brand-logo">PEDRO'S ARTISAN</div>
        <div class="brand-sub">New Member Registration</div>

        @if($errors->any())
            <div class="error-message">
                <ul class="mb-0 list-unstyled">
                    @foreach($errors->all() as $error)
                        <li>× {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.submit') }}" method="POST">
            @csrf
            
            <div class="row g-3">
                <div class="col-12">
                    <label for="name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" required autofocus>
                </div>

                <div class="col-12">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <div class="password-group">
                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password', this)" tabindex="-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                    <div class="password-group">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                        <button type="button" class="toggle-password" onclick="togglePasswordVisibility('password_confirmation', this)" tabindex="-1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-icon"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="eye-off-icon d-none"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="terms" required style="background-color: transparent; border-color: var(--border);">
                    <label class="form-check-label text-muted" for="terms" style="font-size: 0.75rem; font-family: var(--font-montserrat);">
                        I agree to the <a href="#" class="text-white text-decoration-none">Terms of Service</a> and <a href="#" class="text-white text-decoration-none">Privacy Policy</a>.
                    </label>
                </div>
            </div>

            <button type="submit" class="btn-register">Create Account</button>
        </form>

        <div class="footer-links">
            <p>Already a member? <a href="{{ route('login') }}">Sign In</a></p>
            <p class="mt-4"><a href="{{ route('public.index') }}" style="color: var(--muted-foreground); font-weight: 400; font-size: 0.8rem;">← Back to Home</a></p>
        </div>
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
