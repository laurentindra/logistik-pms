<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login - Logistik PMS</title>
  <style>
    {!! file_get_contents(public_path('css/app.css')) !!}
  </style>
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

    @if(session('error'))
    <div class="alert alert-error" style="margin-bottom:18px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      <div>{{ session('error') }}</div>
    </div>
    @endif

    <form method="POST" action="/login">
      @csrf
      <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control {{ $errors->has('email') ? 'is-invalid' : '' }}"
               value="{{ old('email', 'admin@pms.co.id') }}" placeholder="admin@pms.co.id" required autofocus />
        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control {{ $errors->has('password') ? 'is-invalid' : '' }}"
               placeholder="••••••••" required />
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px">
        <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-mid);cursor:pointer">
          <input type="checkbox" name="remember" style="accent-color:var(--primary)" /> Ingat saya
        </label>
      </div>

      <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center">
        Masuk
      </button>
    </form>
  </div>
</div>
</body>
</html>
