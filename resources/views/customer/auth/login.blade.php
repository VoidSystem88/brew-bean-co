<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Customer Login - Brew & Bean Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #f5f0eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-container {
            background: white;
            border-radius: 24px;
            padding: 40px 30px;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(111, 78, 55, 0.15);
            position: relative;
            overflow: hidden;
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #6F4E37, #8B6B4A, #6F4E37);
        }
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo i {
            font-size: 48px;
            color: #6F4E37;
            background: #f5f0eb;
            padding: 15px;
            border-radius: 50%;
        }
        .logo h2 {
            color: #6F4E37;
            font-weight: 700;
            margin-top: 12px;
            font-size: 24px;
        }
        .logo p {
            color: #999;
            font-size: 14px;
            margin-top: 4px;
        }
        .form-label {
            font-weight: 600;
            font-size: 14px;
            color: #333;
        }
        .form-control {
            border-radius: 12px;
            padding: 12px 16px;
            border: 2px solid #e8e8e8;
            font-size: 15px;
            transition: all 0.3s;
        }
        .form-control:focus {
            border-color: #6F4E37;
            box-shadow: 0 0 0 3px rgba(111, 78, 55, 0.1);
        }
        .form-control.is-invalid {
            border-color: #dc3545;
        }
        .btn-coffee {
            background: #6F4E37;
            color: white;
            border: none;
            padding: 14px;
            font-weight: 700;
            font-size: 16px;
            border-radius: 12px;
            width: 100%;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-coffee:hover {
            background: #5a3d2b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(111, 78, 55, 0.3);
        }
        .btn-coffee:active {
            transform: scale(0.98);
        }
        .btn-coffee i {
            margin-right: 8px;
        }
        .divider {
            display: flex;
            align-items: center;
            margin: 20px 0;
            color: #999;
            font-size: 13px;
        }
        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e8e8e8;
        }
        .divider::before {
            margin-right: 15px;
        }
        .divider::after {
            margin-left: 15px;
        }
        .alert {
            border-radius: 12px;
            font-size: 14px;
            padding: 12px 16px;
        }
        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #666;
        }
        .register-link a {
            color: #6F4E37;
            font-weight: 700;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        .back-home {
            text-align: center;
            margin-top: 15px;
        }
        .back-home a {
            color: #999;
            text-decoration: none;
            font-size: 13px;
        }
        .back-home a:hover {
            color: #6F4E37;
        }
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e8e8e8;
            border-right: none;
            border-radius: 12px 0 0 12px;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
            .logo i {
                font-size: 36px;
                padding: 12px;
            }
            .logo h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-mug-hot"></i>
            <h2>Brew & Bean Co.</h2>
            <p>Customer Portal</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle me-2"></i> Please check your inputs.
            </div>
        @endif

        <form method="POST" action="{{ route('customer.login') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-envelope me-1"></i> Email Address
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope text-muted"></i></span>
                    <input type="email" 
                           name="email" 
                           class="form-control @error('email') is-invalid @enderror" 
                           placeholder="you@example.com"
                           value="{{ old('email') }}"
                           required 
                           autofocus>
                </div>
                @error('email')
                    <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <i class="fas fa-lock me-1"></i> Password
                </label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                    <input type="password" 
                           name="password" 
                           class="form-control @error('password') is-invalid @enderror" 
                           placeholder="Enter your password"
                           required>
                </div>
                @error('password')
                    <div class="text-danger mt-1" style="font-size: 13px;">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn-coffee">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
        </form>

        <div class="divider">or</div>

        <div class="register-link">
            Don't have an account? <a href="{{ route('customer.register') }}">Create Account</a>
        </div>

        <div class="back-home">
            <a href="{{ route('login') }}">
                <i class="fas fa-arrow-left me-1"></i> Back to Staff Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>