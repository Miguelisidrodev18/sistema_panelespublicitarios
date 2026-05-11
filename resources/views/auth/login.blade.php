<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BÚHO — Iniciar sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #DC1E2E;
            --primary-dark: #B01825;
            --primary-light: #E94855;
            --bg-dark: #1A1D29;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #1A1D29 0%, #2D1B1F 50%, #1A1D29 100%);
            display: flex; align-items: center; justify-content: center;
            padding: 32px 16px;
        }

        /* Animated background circles */
        .bg-circle {
            position: fixed; border-radius: 50%;
            pointer-events: none;
        }
        .bg-circle-1 {
            width: 600px; height: 600px;
            background: radial-gradient(circle, rgba(220,30,46,.18) 0%, transparent 70%);
            top: -200px; left: -200px;
            animation: float1 18s ease-in-out infinite;
        }
        .bg-circle-2 {
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(220,30,46,.12) 0%, transparent 70%);
            bottom: -150px; right: -100px;
            animation: float2 14s ease-in-out infinite;
        }
        .bg-circle-3 {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(220,30,46,.08) 0%, transparent 70%);
            top: 40%; left: 55%;
            animation: float1 22s ease-in-out infinite reverse;
        }

        @keyframes float1 {
            0%,100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(40px, 60px) scale(1.05); }
            66%      { transform: translate(-30px, 30px) scale(.95); }
        }
        @keyframes float2 {
            0%,100% { transform: translate(0,0) scale(1); }
            50%      { transform: translate(-50px, -40px) scale(1.08); }
        }

        /* Card */
        .login-card {
            position: relative; z-index: 10;
            background: rgba(26,29,41,.92);
            backdrop-filter: blur(24px);
            border: 1.5px solid rgba(220,30,46,.25);
            border-radius: 24px;
            padding: 52px 48px;
            width: 100%; max-width: 460px;
            box-shadow:
                0 24px 64px rgba(0,0,0,.55),
                0 0 0 1px rgba(220,30,46,.12),
                inset 0 1px 0 rgba(255,255,255,.05);
            animation: fadeUp .6s ease both;
        }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(28px); }
            to   { opacity:1; transform:translateY(0); }
        }

        /* Logo */
        .login-logo-wrap {
            display: flex; flex-direction: column; align-items: center;
            margin-bottom: 36px;
        }
        .login-logo {
            width: 76px; height: 76px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            border-radius: 20px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 16px;
            box-shadow:
                0 8px 28px rgba(220,30,46,.55),
                0 0 0 4px rgba(220,30,46,.18);
            animation: glow 2.5s ease-in-out infinite;
        }
        .login-logo i { font-size: 34px; color: #fff; }

        @keyframes glow {
            0%,100% { box-shadow: 0 8px 28px rgba(220,30,46,.55), 0 0 0 4px rgba(220,30,46,.18); }
            50%      { box-shadow: 0 8px 36px rgba(220,30,46,.75), 0 0 0 6px rgba(220,30,46,.25); }
        }

        .login-brand { font-size: 26px; font-weight: 900; color: #fff; letter-spacing: -1px; }
        .login-brand span { color: var(--primary-light); }
        .login-tagline { font-size: 12px; font-weight: 500; color: rgba(255,255,255,.35); margin-top: 4px; text-transform: uppercase; letter-spacing: 1.5px; }

        /* Error alert */
        .alert-error {
            background: rgba(220,30,46,.12);
            border: 1px solid rgba(220,30,46,.35);
            border-radius: 12px;
            padding: 12px 16px;
            color: #FCA5A5;
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 24px;
            animation: shake .5s ease;
        }
        @keyframes shake {
            0%,100% { transform:translateX(0); }
            20%      { transform:translateX(-8px); }
            40%      { transform:translateX(8px); }
            60%      { transform:translateX(-5px); }
            80%      { transform:translateX(5px); }
        }

        /* Form */
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 12.5px; font-weight: 600; color: rgba(255,255,255,.5); text-transform: uppercase; letter-spacing: .8px; margin-bottom: 8px; }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 16px; top: 50%; transform: translateY(-50%);
            color: rgba(255,255,255,.3); font-size: 16px; pointer-events: none;
        }
        .form-input {
            width: 100%;
            background: rgba(0,0,0,.3);
            border: 1.5px solid rgba(220,30,46,.25);
            border-radius: 12px;
            padding: 14px 16px 14px 46px;
            color: #fff;
            font-size: 14px; font-weight: 500;
            font-family: inherit;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }
        .form-input::placeholder { color: rgba(255,255,255,.2); }
        .form-input:focus {
            border-color: var(--primary);
            background: rgba(0,0,0,.4);
            box-shadow: 0 0 0 3px rgba(220,30,46,.18);
        }
        .form-input:focus + .input-icon,
        .input-wrap:focus-within .input-icon { color: var(--primary-light); }

        /* Submit button */
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            color: #fff;
            border: none; border-radius: 12px;
            font-size: 15px; font-weight: 700;
            font-family: inherit;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            box-shadow: 0 8px 24px rgba(220,30,46,.35);
            transition: all .18s ease;
            margin-top: 8px;
        }
        .btn-submit:hover {
            background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 32px rgba(220,30,46,.45);
        }
        .btn-submit:active { transform: translateY(0); }

        /* Footer note */
        .login-footer { text-align: center; margin-top: 28px; }
        .login-footer p { font-size: 11px; font-weight: 500; color: rgba(255,255,255,.2); }

        /* Toggle registro */
        .toggle-registro {
            text-align: center; margin-top: 20px;
        }
        .toggle-registro button {
            background: none; border: none; cursor: pointer;
            font-size: 12px; font-weight: 500;
            color: rgba(255,255,255,.25);
            font-family: inherit;
            transition: color .15s;
        }
        .toggle-registro button:hover { color: rgba(220,30,46,.7); }

        /* Panel de registro */
        .registro-panel {
            display: none;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid rgba(220,30,46,.2);
            animation: fadeUp .3s ease both;
        }
        .registro-panel.visible { display: block; }
        .registro-titulo {
            font-size: 13px; font-weight: 700;
            color: rgba(255,255,255,.5);
            text-transform: uppercase; letter-spacing: 1px;
            margin-bottom: 16px; text-align: center;
        }
        .alert-success {
            background: rgba(34,197,94,.12);
            border: 1px solid rgba(34,197,94,.35);
            border-radius: 12px;
            padding: 12px 16px;
            color: #86efac;
            font-size: 13px; font-weight: 500;
            display: flex; align-items: center; gap: 10px;
            margin-bottom: 24px;
        }

        @media (max-width: 520px) {
            .login-card { padding: 36px 24px; margin: 16px; }
        }
    </style>
</head>
<body>
    <div class="bg-circle bg-circle-1"></div>
    <div class="bg-circle bg-circle-2"></div>
    <div class="bg-circle bg-circle-3"></div>

    <div class="login-card">
        <div class="login-logo-wrap">
            <div class="login-logo"><i class="bi bi-eye-fill"></i></div>
            <div class="login-brand">BÚ<span>HO</span></div>
            <div class="login-tagline">Publicidad con Calle</div>
        </div>

        @if($errors->any())
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ $errors->first() }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert-error">
            <i class="bi bi-exclamation-circle-fill"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @if(session('success'))
        <div class="alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <form action="{{ route('login') }}" method="POST" autocomplete="off">
            @csrf
            <div class="form-group">
                <label class="form-label" for="username">Usuario</label>
                <div class="input-wrap">
                    <input
                        id="username" name="username"
                        type="text"
                        class="form-input"
                        value="{{ old('username') }}"
                        placeholder="Tu nombre de usuario"
                        autofocus required>
                    <i class="bi bi-person-fill input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Contraseña</label>
                <div class="input-wrap">
                    <input
                        id="password" name="password"
                        type="password"
                        class="form-input"
                        placeholder="••••••••"
                        required>
                    <i class="bi bi-lock-fill input-icon"></i>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="bi bi-box-arrow-in-right"></i>
                Ingresar al sistema
            </button>
        </form>

        <div class="toggle-registro">
            <button type="button" onclick="toggleRegistro()">
                <i class="bi bi-shield-lock"></i> Crear usuario con clave maestra
            </button>
        </div>

        <div class="registro-panel" id="registroPanel">
            <div class="registro-titulo"><i class="bi bi-person-plus"></i> Nuevo usuario administrador</div>

            @if($errors->has('clave_maestra'))
            <div class="alert-error">
                <i class="bi bi-exclamation-circle-fill"></i>
                <span>{{ $errors->first('clave_maestra') }}</span>
            </div>
            @endif

            <form action="{{ route('registro.maestro') }}" method="POST" autocomplete="off">
                @csrf
                <div class="form-group">
                    <label class="form-label">Clave maestra</label>
                    <div class="input-wrap">
                        <input name="clave_maestra" type="password" class="form-input" placeholder="••••••••" required>
                        <i class="bi bi-shield-fill input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre completo</label>
                    <div class="input-wrap">
                        <input name="nombre_completo" type="text" class="form-input" placeholder="Nombre y apellido" value="{{ old('nombre_completo') }}" required>
                        <i class="bi bi-person-badge-fill input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Usuario</label>
                    <div class="input-wrap">
                        <input name="username" type="text" class="form-input" placeholder="nombre_usuario" value="{{ old('username') }}" required>
                        <i class="bi bi-person-fill input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <div class="input-wrap">
                        <input name="password" type="password" class="form-input" placeholder="Mínimo 6 caracteres" required>
                        <i class="bi bi-lock-fill input-icon"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Repetir contraseña</label>
                    <div class="input-wrap">
                        <input name="password_confirmation" type="password" class="form-input" placeholder="••••••••" required>
                        <i class="bi bi-lock-fill input-icon"></i>
                    </div>
                </div>
                <button type="submit" class="btn-submit">
                    <i class="bi bi-person-check-fill"></i>
                    Crear usuario administrador
                </button>
            </form>
        </div>

        <div class="login-footer">
            <p>BÚHO Panel Administrativo &copy; {{ date('Y') }}</p>
        </div>
    </div>

    <script>
        function toggleRegistro() {
            const panel = document.getElementById('registroPanel');
            panel.classList.toggle('visible');
        }
        @if($errors->has('clave_maestra') || $errors->has('username') || $errors->has('nombre_completo'))
        document.getElementById('registroPanel').classList.add('visible');
        @endif
    </script>
</body>
</html>
