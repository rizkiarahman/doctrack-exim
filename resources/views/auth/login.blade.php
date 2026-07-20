<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - EXIM Track Dokumen PT. Detpak Indonesia</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📑</text></svg>">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Custom CSS (using shared styles + login specific styles) -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    <style>
        .login-page {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 10;
        }

        .login-card {
            width: 100%;
            max-width: 450px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-xl);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            animation: card-appear 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--color-primary), var(--color-accent));
        }

        @keyframes card-appear {
            0% { opacity: 0; transform: translateY(20px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        .login-logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-logo .logo-icon {
            margin: 0 auto 12px auto;
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .login-logo h1 {
            font-family: var(--font-heading);
            font-size: 24px;
            font-weight: 800;
        }

        .login-logo p {
            font-size: 13px;
            color: var(--text-secondary);
        }

        /* Demo Credentials Helper Box */
        .demo-box {
            background: rgba(99, 102, 241, 0.06);
            border: 1px solid rgba(99, 102, 241, 0.15);
            border-radius: var(--radius-md);
            padding: 16px;
            margin-bottom: 24px;
            font-size: 12px;
        }

        .demo-box h4 {
            font-weight: 600;
            color: var(--color-accent);
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .demo-accounts {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .btn-demo {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            padding: 8px 10px;
            cursor: pointer;
            text-align: left;
            transition: var(--transition-smooth);
            font-family: var(--font-body);
        }

        .btn-demo:hover {
            background: rgba(99, 102, 241, 0.12);
            border-color: rgba(99, 102, 241, 0.3);
            color: var(--text-primary);
        }

        .btn-demo strong {
            display: block;
            font-size: 11px;
            color: var(--text-primary);
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            font-size: 12px;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <!-- Background Ambient Glows -->
    <div class="bg-glow bg-glow-1" style="width: 500px; height: 500px; top: 10%; left: 10%;"></div>
    <div class="bg-glow bg-glow-2" style="width: 500px; height: 500px; bottom: 10%; right: 10%;"></div>

    <div class="login-page">
        <div class="login-card">
            
            <div class="login-logo">
                <div class="logo-icon">
                    <i class="bi bi-file-earmark-arrow-up-fill"></i>
                </div>
                <h1 style="font-family: var(--font-heading); font-size: 22px; font-weight: 800; line-height: 1.3;">EXIM Track Dokumen <br><span style="color: var(--color-accent); font-size: 15px;">PT. Detpak Indonesia</span></h1>
                <p style="margin-top: 8px;">Sistem Monitoring Tanda Tangan & Sunting Dokumen</p>
            </div>

            <form action="{{ route('login') }}" method="POST">
                @csrf

                <!-- Session Status / Alert -->
                @if(session('success'))
                    <div class="alert alert-success" style="padding: 10px 16px; font-size: 12px; margin-bottom: 16px; border-radius: var(--radius-sm);">
                        <div class="alert-content">
                            <i class="bi bi-check-circle-fill"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Email Input -->
                <div class="form-group">
                    <label for="email">Alamat Email</label>
                    <div style="position: relative;">
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" placeholder="nama@perusahaan.com" value="{{ old('email') }}" required autofocus style="padding-left: 45px;">
                        <i class="bi bi-envelope" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
                    </div>
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Input -->
                <div class="form-group" style="margin-bottom: 15px;">
                    <label for="password">Kata Sandi</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" placeholder="Masukkan kata sandi..." required style="padding-left: 45px;">
                        <i class="bi bi-lock" style="position: absolute; left: 16px; top: 50%; transform: translateY(-50%); color: var(--text-muted); font-size: 16px;"></i>
                    </div>
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-group" style="display: flex; align-items: center; gap: 8px; margin-bottom: 25px;">
                    <input type="checkbox" name="remember" id="remember" style="accent-color: var(--color-primary); cursor: pointer;">
                    <label for="remember" style="margin-bottom: 0; font-size: 12px; font-weight: 500; cursor: pointer;">Ingat Saya di Perangkat Ini</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 12px;">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk ke Sistem
                </button>
            </form>

            <div class="login-footer">
                <p>&copy; 2026 EXIM Track Dokumen PT. Detpak Indonesia. All rights reserved.</p>
            </div>
        </div>
    </div>

</body>
</html>
