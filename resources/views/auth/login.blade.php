<!DOCTYPE html>
<html lang="id" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — SCM Global</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #dc2626;
            --primary-hover: #b91c1c;
            --primary-light: rgba(220, 38, 38, 0.1);
            --bg-color: #000000;
            --card-bg: #121212;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border-color: #262626;
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
            color: var(--text-main);
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
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin: 0 auto 16px;
            box-shadow: 0 4px 6px -1px var(--primary-light);
        }

        .login-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 40px 32px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.5), 0 4px 6px -4px rgba(0, 0, 0, 0.5);
        }

        .login-card h3 {
            font-weight: 800;
            font-size: 1.35rem;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
            color: var(--text-main);
        }

        .login-card p {
            color: var(--text-muted);
            font-size: 0.88rem;
            margin-bottom: 28px;
        }

        .form-label {
            font-weight: 600;
            font-size: 0.8rem;
            color: var(--text-main);
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
            color: var(--text-muted);
            font-size: 1.1rem;
            transition: all 0.2s;
            z-index: 10;
        }

        .form-control {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 10px 14px 10px 42px;
            color: var(--text-main);
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            background: var(--card-bg);
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--primary-light);
            color: var(--text-main);
        }

        .form-control:focus + i {
            color: var(--primary);
        }

        .btn-submit {
            background: var(--primary);
            border: 1px solid var(--primary-hover);
            border-radius: 10px;
            padding: 11px;
            font-weight: 700;
            font-size: 0.95rem;
            color: white;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            border-color: #991b1b;
            color: white;
        }

        .auth-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 0.88rem;
            color: var(--text-muted);
        }

        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
        }

        .auth-footer a:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .alert {
            border-radius: 10px;
            font-size: 0.85rem;
            border: 1px solid;
            padding: 12px 16px;
        }
        .alert-success {
            background: rgba(22, 163, 74, 0.1);
            color: #4ade80;
            border-color: rgba(22, 163, 74, 0.2);
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border-color: rgba(239, 68, 68, 0.2);
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