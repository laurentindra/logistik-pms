<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Logistik PMS</title>
  <link rel="stylesheet" href="{{ asset('css/app.css') }}" />
</head>
<body>
<div class="auth-page">
  <div class="auth-card">
    <div class="auth-logo">
      <div class="auth-logo-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
        </svg>
      </div>
      <div class="auth-logo-text">
        <h1>Logistik PMS</h1>
        <p>PT. Panca Merak Samudera</p>
      </div>
    </div>

    <div class="auth-title">Selamat Datang</div>
    <div class="auth-sub">Silakan masuk untuk melanjutkan</div>

    @if($errors->any())
    <div class="alert alert-error" style="margin-bottom:16px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      {{ $errors->first() }}
    </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf
      <div class="form-group">
        <label class="form-label required" for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
               value="{{ old('email') }}" placeholder="admin@pms.co.id" autofocus required />
      </div>

      <div class="form-group">
        <label class="form-label required" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required />
      </div>

      <div class="form-group" style="display:flex;align-items:center;gap:8px">
        <input type="checkbox" id="remember" name="remember" style="width:auto">
        <label for="remember" style="margin:0;font-size:13px;color:var(--text-mid)">Ingat saya</label>
      </div>

      <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center">
        Masuk
      </button>
    </form>
  </div>
</div>
</body>
</html>
