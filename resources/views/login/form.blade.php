<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <title>Login</title>
    <style>
        body {
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .login-container {
            background-color: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        .login-container h3 {
            text-align: center;
            margin-bottom: 30px;
        }

        .form-outline input {
            border-radius: 8px;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            font-size: 16px;
        }

        .forgot-password {
            font-size: 14px;
        }

        .social-buttons i {
            font-size: 20px;
            margin: 5px;
        }

        .social-buttons button {
            width: 45px;
            height: 45px;
            padding: 5px;
        }

        /* Styling for logo */
        .logo {
            display: block;
            margin: 0 auto 20px;
            width: 60px; /* Adjust size as needed */
            height: auto;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <!-- Logo -->
        <img src=" {{ asset('images/icon.png') }} " alt="User Logo" class="logo">
        
        <h3>Login to W Mart</h3>
        <form action="{{ route('login') }}" method="POST">
            @csrf <!-- Tambahkan CSRF token untuk keamanan -->
            <!-- Tampilkan error jika ada -->
            @if ($errors->any())
                <div class="alert alert-danger">
                    {{ $errors->first() }}
                </div>
            @endif

            @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
        
            <!-- Email input -->
            <div class="form-outline mb-4">
                <i class="bi bi-person-fill"></i>
                <input type="email" name="email" class="form-control" placeholder="Email Address" required value="{{ old('email') }}" />
            </div>
        
            <!-- Password input -->
            <div class="form-outline mb-4">
                <i class="bi bi-lock-fill"></i>
                <input type="password" name="password" class="form-control" placeholder="Password" required />
            </div>
        
            <!-- Submit button -->
            <button type="submit" class="btn btn-primary mb-4">Sign in</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script> <!-- FontAwesome Icons -->
</body>
</html>
