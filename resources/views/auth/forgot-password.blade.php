<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Pedro's Artisan Shop</title>
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
        <div class="brand-sub">Reset Password</div>

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

        <p style="color: var(--muted-foreground); font-family: var(--font-montserrat); font-size: 0.85rem; text-align: center; margin-bottom: 1.5rem;">
            Enter your email address to receive a secure link to reset your password.
        </p>

        <form action="{{ route('password.email') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required autofocus>
            </div>

            <button type="submit" class="btn-login">Send Reset Link</button>
        </form>

        <div class="footer-links">
            <p>Remembered your password? <a href="{{ route('login') }}">Back to Login</a></p>
        </div>
    </div>

</body>
</html>
