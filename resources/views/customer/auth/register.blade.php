<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Brew & Bean Co.</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f5f0eb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 450px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        .register-card .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-card .logo i {
            font-size: 48px;
            color: #6F4E37;
        }
        .register-card .logo h2 {
            color: #6F4E37;
            font-weight: 700;
            margin-top: 10px;
        }
        .btn-coffee {
            background: #6F4E37;
            color: white;
            border: none;
            padding: 10px;
            font-weight: 600;
        }
        .btn-coffee:hover {
            background: #5a3d2b;
            color: white;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="logo">
            <i class="fas fa-mug-hot"></i>
            <h2>Brew & Bean Co.</h2>
            <p>Create your account</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <form method="POST" action="{{ route('customer.register') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-bold">Full Name</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Phone Number</label>
                <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone') }}">
                @error('phone')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-coffee w-100">
                <i class="fas fa-user-plus me-2"></i> Create Account
            </button>
        </form>

        <div class="text-center mt-3">
            <p class="text-muted">Already have an account? <a href="{{ route('customer.login') }}" class="text-decoration-none fw-bold" style="color: #6F4E37;">Login here</a></p>
        </div>
    </div>
</body>
</html>