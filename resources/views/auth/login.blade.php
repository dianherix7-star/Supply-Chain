<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SCM Global</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg-color: #f8fafc;
        }

        * {
            font-family: 'Plus Jakarta Sans', sans-serif;
            letter-spacing: -0.015em;
        }

        body {
            background-color: var(--bg-color);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #0f172a;
            position: relative;
            overflow: hidden;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            background: #4f46e5;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 auto 16px;
            box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2);
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
        }

        .login-card h3 {
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            color: #0f172a;
        }

        .login-card p {
            color: #64748b;
            font-size: 0.88rem;
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: #334155;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 6px;
        }

        .input-group-custom {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group-custom i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.1rem;
            transition: all 0.2s;
            z-index: 10;
        }

        .form-control {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 10px 14px 10px 42px;
            color: #0f172a;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            background: #ffffff;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.15);
            color: #0f172a;
        }

        .form-control:focus + i {
            color: var(--primary);
        }

        .btn-submit {
            background: #4f46e5;
            border: 1px solid #4338ca;
            border-radius: 10px;
            padding: 11px;
            font-weight: 700;
            font-size: 0.95rem;
            color: white;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .btn-submit:hover {
            background: #4338ca;
            border-color: #3730a3;
            color: white;
        }

        .auth-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 0.88rem;
            color: #64748b;
        }

        .auth-footer a {
            color: #4f46e5;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .auth-footer a:hover {
            color: #4338ca;
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            font-size: 0.85rem;
            border: 1px solid;
            padding: 12px 16px;
        }
        .alert-success {
            background: #f0fdf4;
            color: #16a34a;
            border-color: rgba(22, 163, 74, 0.12);
        }
        .alert-danger {
            background: #fef2f2;
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.12);
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="brand-logo text-white">
        <i class="bi bi-globe2"></i>
    </div>

    <div class="login-card">
        <div class="text-center">
            <h3>SCM Global</h3>
            <p>Supply Chain Risk Intelligence</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger mb-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div>
                <label class="form-label">Email</label>
                <div class="input-group-custom">
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" value="{{ old('email') }}" required autocomplete="email">
                    <i class="bi bi-envelope"></i>
                </div>
            </div>

            <div>
                <label class="form-label">Password</label>
                <div class="input-group-custom">
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required autocomplete="current-password">
                    <i class="bi bi-shield-lock"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-submit w-100 mt-2">
                Masuk
            </button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="{{ route('register') }}">Registrasi Sekarang</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>