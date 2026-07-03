<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Login - Brew & Bean Co.</title>
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
            padding: 45px 35px;
            max-width: 420px;
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
            height: 4px;
            background: linear-gradient(90deg, #6F4E37, #8B6B4A, #6F4E37);
        }
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .logo .icon {
            font-size: 42px;
            color: #6F4E37;
            background: #f5f0eb;
            padding: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-bottom: 10px;
        }
        .logo h2 {
            color: #6F4E37;
            font-weight: 700;
            font-size: 22px;
            margin: 0;
        }
        .logo p {
            color: #999;
            font-size: 13px;
            margin-top: 2px;
        }
        .tab-group {
            display: flex;
            background: #f5f0eb;
            border-radius: 12px;
            padding: 4px;
            margin-bottom: 25px;
        }
        .tab-group .tab {
            flex: 1;
            padding: 10px;
            text-align: center;
            border-radius: 10px;
            border: none;
            background: transparent;
            font-weight: 600;
            font-size: 14px;
            color: #666;
            cursor: pointer;
            transition: 0.3s;
        }
        .tab-group .tab.active {
            background: white;
            color: #6F4E37;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .tab-group .tab i {
            margin-right: 6px;
        }
        .tab-group .tab:hover:not(.active) {
            color: #6F4E37;
        }
        .login-form {
            display: block;
        }
        .login-form.hidden {
            display: none;
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
        .input-group-text {
            background: #f8f9fa;
            border: 2px solid #e8e8e8;
            border-right: none;
            border-radius: 12px 0 0 12px;
            color: #999;
        }
        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }
        .btn-login {
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
        .btn-login:hover {
            background: #5a3d2b;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(111, 78, 55, 0.3);
        }
        .btn-login:active {
            transform: scale(0.98);
        }
        .btn-login i {
            margin-right: 8px;
        }
        .alert {
            border-radius: 12px;
            font-size: 14px;
            padding: 12px 16px;
        }
        .forgot-link {
            text-align: right;
            margin-top: 8px;
        }
        .forgot-link a {
            color: #999;
            font-size: 13px;
            text-decoration: none;
        }
        .forgot-link a:hover {
            color: #6F4E37;
        }
        .register-link {
            text-align: center;
            margin-top: 15px;
            font-size: 14px;
            color: #666;
        }
        .register-link a {
            color: #6F4E37;
            font-weight: 600;
            text-decoration: none;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
        @media (max-width: 480px) {
            .login-container {
                padding: 25px 20px;
            }
            .tab-group .tab {
                font-size: 12px;
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <div class="icon">
                <i class="fas fa-mug-hot"></i>
            </div>
            <h2>Brew & Bean Co.</h2>
            <p>Sign in to your account</p>
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
                <i class="fas fa-exclamation-circle me-2"></i> Invalid credentials. Please try again.
            </div>
        @endif

        <!-- Tabs -->
        <div class="tab-group">
            <button class="tab active" id="tabStaff" onclick="switchTab('staff')">
                <i class="fas fa-user-tie"></i> Staff
            </button>
            <button class="tab" id="tabCustomer" onclick="switchTab('customer')">
                <i class="fas fa-user"></i> Customer
            </button>
        </div>

        <!-- Staff Login Form -->
        <div class="login-form" id="staffForm">
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope me-1"></i> Email Address
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="staff@brewbeanco.com"
                               value="{{ old('email') }}"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-lock me-1"></i> Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Enter your password"
                               required>
                    </div>
                </div>

                <div class="forgot-link">
                    <a href="#"><i class="fas fa-question-circle me-1"></i> Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login as Staff
                </button>
            </form>
        </div>

        <!-- Customer Login Form -->
        <div class="login-form hidden" id="customerForm">
            <form method="POST" action="{{ route('customer.login') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-envelope me-1"></i> Email Address
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" 
                               name="email" 
                               class="form-control" 
                               placeholder="you@example.com"
                               required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">
                        <i class="fas fa-lock me-1"></i> Password
                    </label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" 
                               name="password" 
                               class="form-control" 
                               placeholder="Enter your password"
                               required>
                    </div>
                </div>

                <div class="forgot-link">
                    <a href="#"><i class="fas fa-question-circle me-1"></i> Forgot password?</a>
                </div>

                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login as Customer
                </button>
            </form>

            <div class="register-link">
                Don't have an account? <a href="{{ route('customer.register') }}">Register here</a>
            </div>
        </div>
    </div>

    <script>
        function switchTab(type) {
            // Update tabs
            document.getElementById('tabStaff').classList.toggle('active', type === 'staff');
            document.getElementById('tabCustomer').classList.toggle('active', type === 'customer');
            
            // Update forms
            document.getElementById('staffForm').classList.toggle('hidden', type !== 'staff');
            document.getElementById('customerForm').classList.toggle('hidden', type !== 'customer');
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>