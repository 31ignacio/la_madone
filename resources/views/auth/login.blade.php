<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion | SuperMarché</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
* { margin:0; padding:0; box-sizing:border-box; }

body {
    min-height: 100vh;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    display: flex;
    background: #f0f4f8;
}

/* ══ SPLIT LAYOUT ══ */
.left-panel {
     flex: 1;
   background: url('/image/login.jpg') center center / cover no-repeat;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px 50px;
    position: relative;
    overflow: hidden;
}

.left-panel::before {
    content: '';
    position: absolute;
    width: 500px; height: 500px;
    border-radius: 50%;
   inset: 0;
    background: linear-gradient(
        160deg,
        rgba(0,0,0,0.55) 0%,
        rgba(0,0,0,0.35) 50%,
        rgba(0,0,0,0.25) 100%
    );
    z-index: 1;
    top: -150px; right: -150px;
}
.left-panel::after {
    content: '';
    position: absolute;
    width: 350px; height: 350px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
    bottom: -80px; left: -80px;
}

.left-illustration {
    position: relative; z-index: 2;
    text-align: center;
    max-width: 420px;
}



.store-icon-wrap {
    width: 110px; height: 110px;
    background: rgba(255,255,255,.15);
    border-radius: 32px;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 32px;
    border: 2px solid rgba(255,255,255,.2);
    backdrop-filter: blur(10px);
}
.store-icon-wrap i { font-size: 52px; color: #fff; }

.left-title {
    font-size: 38px; font-weight: 900;
    color: #fff; letter-spacing: -1px;
    line-height: 1.1; margin-bottom: 16px;
    text-shadow: 0 4px 20px rgba(0,0,0,0.6);
}
.left-title span {
    color: rgba(255,255,255,.65);
    font-weight: 400;
}
.left-sub{
    color:#fff;
    text-shadow:0 4px 20px rgba(0,0,0,0.8);
    background: rgba(0,0,0,0.25);
    backdrop-filter: blur(6px);
    padding:10px 14px;
    border-radius:12px;
    display:inline-block;
}

/* ══ STATS MINI ══ */
.stats-row {
    display: flex; gap: 12px;
    width: 100%;
}
.stat-chip {
    flex: 1;
    background: rgba(255,255,255,.12);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 14px;
    padding: 14px 12px;
    text-align: center;
    backdrop-filter: blur(8px);
}
.stat-chip-val {
    font-size: 22px; font-weight: 900; color: #fff;
    display: block; margin-bottom: 3px;
}
.stat-chip-lbl {
    font-size: 10px; font-weight: 600;
    color: rgba(255,255,255,.6);
    text-transform: uppercase; letter-spacing: .6px;
}

/* ══ DOTS DECO ══ */
.dots-deco {
    position: absolute; z-index: 1;
    bottom: 40px; right: 40px;
    display: grid; grid-template-columns: repeat(6,1fr); gap: 8px;
}
.dots-deco span {
    width: 4px; height: 4px; border-radius: 50%;
    background: rgba(255,255,255,.25);
    display: block;
}

/* ══ RIGHT PANEL ══ */
.right-panel {
    width: 460px; flex-shrink: 0;
    background: #fff;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    padding: 60px 50px;
}

.login-header { text-align: center; margin-bottom: 36px; }
.login-header-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: #eef4ff; border: 1px solid #c7d9fb;
    border-radius: 30px; padding: 5px 14px;
    font-size: 12px; font-weight: 700;
    color: #1a6fec; margin-bottom: 18px;
    letter-spacing: .3px;
}
.login-header-badge i { font-size: 11px; }
.login-title {
    font-size: 26px; font-weight: 900;
    color: #0f1729; letter-spacing: -.5px; margin-bottom: 6px;
}
.login-sub { font-size: 14px; color: #6b7a99; }

/* ══ ALERTS ══ */
.alert-err {
    background: #fff5f5; border: 1px solid #fccfcf;
    border-radius: 10px; padding: 11px 14px;
    display: flex; align-items: center; gap: 9px;
    font-size: 13px; color: #c0392b;
    margin-bottom: 22px;
}
.alert-ok {
    background: #f0fff4; border: 1px solid #c3e6cb;
    border-radius: 10px; padding: 11px 14px;
    font-size: 13px; color: #1e7e34;
    margin-bottom: 22px;
}

/* ══ FORM ══ */
.form-box { width: 100%; }

.field { margin-bottom: 20px; }
.field-label {
    display: block;
    font-size: 12px; font-weight: 800;
    text-transform: uppercase; letter-spacing: .7px;
    color: #8895b0; margin-bottom: 8px;
}
.field-wrap {
    display: flex; align-items: center;
    background: #f7f9fc;
    border: 1.5px solid #e4e9f2;
    border-radius: 12px;
    overflow: hidden;
    transition: all .2s;
}
.field-wrap:focus-within {
    border-color: #1a6fec;
    background: #fff;
    box-shadow: 0 0 0 4px rgba(26,111,236,.1);
}
.field-ico {
    width: 46px; display: flex;
    align-items: center; justify-content: center;
    color: #b0bcd4; font-size: 14px; flex-shrink: 0;
}
.field-wrap:focus-within .field-ico { color: #1a6fec; }
.field-input {
    flex: 1; border: none; background: transparent;
    padding: 13px 14px 13px 0;
    font-size: 14px; color: #0f1729; outline: none;
}
.field-input::placeholder { color: #b0bcd4; }
.field-toggle {
    width: 44px; display: flex;
    align-items: center; justify-content: center;
    cursor: pointer; color: #b0bcd4; flex-shrink: 0;
    font-size: 13px; transition: color .15s;
}
.field-toggle:hover { color: #1a6fec; }

/* ══ REMEMBER ══ */
.remember-row {
    display: flex; align-items: center;
    margin-bottom: 26px;
}
.custom-check {
    width: 17px; height: 17px; margin-right: 9px;
    accent-color: #1a6fec; cursor: pointer; flex-shrink: 0;
    border-radius: 5px;
}
.remember-lbl { font-size: 13px; color: #6b7a99; cursor: pointer; }

/* ══ BUTTON ══ */
.btn-login {
    width: 100%; padding: 15px;
    border-radius: 13px; border: none;
    background: linear-gradient(135deg, #0f3460 0%, #1a6fec 100%);
    color: #fff; font-size: 15px; font-weight: 800;
    cursor: pointer; transition: all .22s;
    display: flex; align-items: center; justify-content: center; gap: 10px;
    box-shadow: 0 6px 24px rgba(26,111,236,.32);
    letter-spacing: .3px;
}
.btn-login:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 12px 32px rgba(26,111,236,.42);
}
.btn-login:active:not(:disabled) {
    transform: translateY(0);
    box-shadow: 0 4px 14px rgba(26,111,236,.25);
}
.btn-login:disabled {
    background: #b0bcd4; cursor: not-allowed;
    box-shadow: none; transform: none;
}

.spinner {
    display: none;
    width: 18px; height: 18px;
    border: 2.5px solid rgba(255,255,255,.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin .7s linear infinite; flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }
.loading .spinner { display: block; }
.loading .btn-icon { display: none; }
.loading .btn-label { opacity: .85; }

/* ══ FOOTER ══ */
.login-foot {
    text-align: center; margin-top: 28px;
    font-size: 12px; color: #b0bcd4;
}

/* ══ RESPONSIVE ══ */
@media(max-width: 860px) {
    .left-panel { display: none; }
    .right-panel { width: 100%; padding: 40px 28px; }
}
    </style>
</head>
<body>

{{-- ════ PANNEAU GAUCHE ════ --}}
<div class="left-panel">

    <div class="left-illustration">

        <div class="store-icon-wrap">
            <i class="fas fa-store"></i>
        </div>

        <h1 class="left-title">
            LaMadone<span>Supermaché</span>
        </h1>
        <p class="left-sub readable-text">
            Gérez votre stock, vos ventes et votre caisse<br>
            depuis une seule plateforme intelligente.
        </p><br><br>

        <div class="stats-row">
            <div class="stat-chip">
                <span class="stat-chip-val">100%</span>
                <span class="stat-chip-lbl">Sécurisé</span>
            </div>
            <div class="stat-chip">
                <span class="stat-chip-val">24/7</span>
                <span class="stat-chip-lbl">Disponible</span>
            </div>
            <div class="stat-chip">
                <span class="stat-chip-val">3</span>
                <span class="stat-chip-lbl">Rôles</span>
            </div>
        </div>

    </div>

    {{-- Dots déco --}}
    <div class="dots-deco">
        @for($i = 0; $i < 24; $i++)
            <span></span>
        @endfor
    </div>

</div>

{{-- ════ PANNEAU DROIT ════ --}}
<div class="right-panel">

    <div class="login-header">
        <div class="login-header-badge">
            <i class="fas fa-shield-halved"></i>
            Accès sécurisé
        </div>
        <h2 class="login-title">Bon retour 👋</h2>
        <p class="login-sub">Connectez-vous à votre espace de gestion</p>
    </div>

    {{-- Erreurs --}}
    @if($errors->any())
        <div class="alert-err" style="width:100%">
            <i class="fas fa-circle-exclamation" style="font-size:15px; flex-shrink:0"></i>
            <span>{{ $errors->first() }}</span>
        </div>
    @endif

    @if(session('status'))
        <div class="alert-ok" style="width:100%">
            <i class="fas fa-circle-check mr-2"></i>{{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm" class="form-box">
        @csrf

        {{-- Prenom --}}
        <div class="field">
            <label class="field-label" for="prenom">
                Prenom
            </label>
            <div class="field-wrap">
                <div class="field-ico"><i class="fas fa-user"></i></div>
                <select id="prenom" name="prenom" class="field-input" required autofocus>
                    <option value="">Selectionnez votre prenom</option>
                    @foreach(($prenoms ?? collect()) as $prenomOption)
                        <option value="{{ $prenomOption }}" @selected(old('prenom') === $prenomOption)>
                            {{ $prenomOption }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Mot de passe --}}
        <div class="field">
            <label class="field-label" for="password">
                Mot de passe
            </label>
            <div class="field-wrap">
                <div class="field-ico"><i class="fas fa-lock"></i></div>
                <input type="password" id="password" name="password"
                       class="field-input"
                       placeholder="••••••••"
                       required>
                <div class="field-toggle" onclick="togglePwd()">
                    <i class="fas fa-eye" id="pwdIcon"></i>
                </div>
            </div>
        </div>

        {{-- Remember --}}
        <div class="remember-row">
            <input type="checkbox" name="remember" id="remember" class="custom-check">
            <label for="remember" class="remember-lbl">Se souvenir de moi</label>
        </div>

        {{-- Submit --}}
        <button type="submit" class="btn-login" id="btnLogin">
            <div class="spinner" id="btnSpinner"></div>
            <i class="fas fa-right-to-bracket btn-icon" id="btnIcon" style="font-size:16px"></i>
            <span class="btn-label" id="btnLabel">Se connecter</span>
        </button>

    </form>

    <div class="login-foot">
        SuperMarché &copy; {{ date('Y') }} &nbsp;·&nbsp; Tous droits réservés
    </div>

</div>

<script>
function togglePwd() {
    const inp  = document.getElementById('password');
    const icon = document.getElementById('pwdIcon');
    inp.type   = inp.type === 'password' ? 'text' : 'password';
    icon.className = inp.type === 'text' ? 'fas fa-eye-slash' : 'fas fa-eye';
}

document.getElementById('loginForm').addEventListener('submit', function () {
    const prenom = document.getElementById('prenom').value.trim();
    const pwd   = document.getElementById('password').value.trim();
    if (!prenom || !pwd) return;

    const btn   = document.getElementById('btnLogin');
    const label = document.getElementById('btnLabel');

    btn.disabled = true;
    btn.classList.add('loading');
    label.textContent = 'Connexion en cours...';
});
</script>

</body>
</html>
