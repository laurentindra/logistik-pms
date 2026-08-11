<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <title>@yield('title', 'Dashboard') - Logistik PMS</title>
  <style>
    {!! file_get_contents(public_path('css/app.css')) !!}
  </style>
  @stack('styles')
</head>
<body>

{{-- MOBILE BACKDROP OVERLAY --}}
<div class="sidebar-backdrop" id="sidebar-backdrop" onclick="toggleSidebar()"></div>

{{-- SIDEBAR --}}
<aside class="sidebar" id="sidebar">
  <a href="{{ route('dashboard') }}" class="sidebar-brand">
    <div class="sidebar-brand-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
        <circle cx="12" cy="10" r="3"/>
      </svg>
    </div>
    <div>
      <div class="sidebar-brand-text">Logistik PMS</div>
      <div class="sidebar-brand-sub">PT. Panca Merak Samudera</div>
    </div>
  </a>

  <nav class="sidebar-nav">
    <div class="nav-section">Menu Utama</div>
    <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
      Dashboard
    </a>

    <div class="nav-section" style="margin-top:8px">Inventory</div>
    <a href="{{ route('barang.index') }}" class="nav-item {{ request()->routeIs('barang.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
      Data Barang
    </a>
    <a href="{{ route('transaksi.create-masuk') }}" class="nav-item {{ request()->is('transaksi/masuk*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
      Barang Masuk
    </a>
    <a href="{{ route('transaksi.create-keluar') }}" class="nav-item {{ request()->is('transaksi/keluar*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
      Barang Keluar
    </a>
    <a href="{{ route('transaksi.index') }}" class="nav-item {{ request()->routeIs('transaksi.index') || request()->routeIs('transaksi.show') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
      Histori Transaksi
    </a>

    <div class="nav-section" style="margin-top:8px">Master Data</div>
    <a href="{{ route('kapal.index') }}" class="nav-item {{ request()->routeIs('kapal.*') ? 'active' : '' }}">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 17l9 4 9-4M3 12l9 4 9-4M3 7l9-4 9 4"/></svg>
      Armada / Kapal
    </a>
  </nav>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}</div>
      <div>
        <div class="user-name">{{ Auth::user()->name ?? 'Admin' }}</div>
        <div class="user-role">Administrator</div>
      </div>
    </div>
    <form action="{{ route('logout') }}" method="POST" style="margin-top:6px">
      @csrf
      <button type="submit" class="logout-btn">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Keluar
      </button>
    </form>
  </div>
</aside>

{{-- TOPBAR --}}
<div class="topbar">
  <div class="topbar-left">
    <button class="mobile-toggle" onclick="toggleSidebar()" aria-label="Toggle Sidebar">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <div class="topbar-title">@yield('page-title', 'Dashboard')</div>
  </div>
  <div class="topbar-actions">
    <a href="{{ route('transaksi.create-masuk') }}" class="btn btn-primary btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
      <span class="btn-text">Barang Masuk</span>
    </a>
    <a href="{{ route('transaksi.create-keluar') }}" class="btn btn-secondary btn-sm">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
      <span class="btn-text">Barang Keluar</span>
    </a>
  </div>
</div>

{{-- MAIN --}}
<main class="main-content">
  <div class="page-body">

    {{-- FLASH MESSAGES --}}
    @if(session('success'))
    <div class="alert alert-success mb-4" style="margin-bottom:16px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
      {{ session('success') }}
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-error mb-4" style="margin-bottom:16px">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
      {{ session('error') }}
    </div>
    @endif

    @yield('content')
  </div>
</main>

{{-- DELETE CONFIRM MODAL --}}
<div class="modal-overlay" id="modal-delete">
  <div class="modal">
    <div class="modal-title">Konfirmasi Hapus</div>
    <div class="modal-body" id="modal-delete-text">Apakah Anda yakin ingin menghapus data ini?</div>
    <div class="modal-actions">
      <button class="btn btn-secondary" onclick="closeModal()">Batal</button>
      <form id="modal-delete-form" method="POST">
        @csrf @method('DELETE')
        <button type="submit" class="btn btn-danger">Hapus</button>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Sidebar Toggle
function toggleSidebar() {
  const sidebar  = document.getElementById('sidebar');
  const backdrop = document.getElementById('sidebar-backdrop');
  sidebar.classList.toggle('open');
  backdrop.classList.toggle('show');
}

// Modal
function confirmDelete(url, msg) {
  document.getElementById('modal-delete-form').action = url;
  document.getElementById('modal-delete-text').textContent = msg || 'Apakah Anda yakin ingin menghapus data ini?';
  document.getElementById('modal-delete').classList.add('open');
}
function closeModal() {
  document.getElementById('modal-delete').classList.remove('open');
}
document.getElementById('modal-delete').addEventListener('click', function(e) {
  if (e.target === this) closeModal();
});

// Format rupiah
const rp = n => n ? 'Rp ' + Number(n).toLocaleString('id-ID') : 'Rp 0';

// Alert auto dismiss
setTimeout(() => {
  document.querySelectorAll('.alert').forEach(el => {
    el.style.transition = 'opacity .4s';
    el.style.opacity = '0';
    setTimeout(() => el.remove(), 400);
  });
}, 4000);
</script>
@stack('scripts')
</body>
</html>
