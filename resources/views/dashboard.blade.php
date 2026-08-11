@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- KPI --}}
<div class="kpi-grid">
  <div class="kpi-card kpi-blue">
    <div class="kpi-label">Total Katalog Barang</div>
    <div class="kpi-value">{{ number_format($totalBarang) }}</div>
    <div class="kpi-sub">Item terdaftar</div>
    <div class="kpi-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/>
      </svg>
    </div>
  </div>
  <div class="kpi-card kpi-green">
    <div class="kpi-label">Barang Ada Stok</div>
    <div class="kpi-value">{{ number_format($barangAdaStok) }}</div>
    <div class="kpi-sub">Dari {{ $totalBarang }} item</div>
    <div class="kpi-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
    </div>
  </div>
  <div class="kpi-card kpi-orange">
    <div class="kpi-label">Transaksi Masuk</div>
    <div class="kpi-value">{{ number_format($transaksiMasuk) }}</div>
    <div class="kpi-sub">Bulan {{ now()->isoFormat('MMMM YYYY') }}</div>
    <div class="kpi-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 5v14M5 12l7 7 7-7"/>
      </svg>
    </div>
  </div>
  <div class="kpi-card kpi-teal">
    <div class="kpi-label">Transaksi Keluar</div>
    <div class="kpi-value">{{ number_format($transaksiKeluar) }}</div>
    <div class="kpi-sub">Bulan {{ now()->isoFormat('MMMM YYYY') }}</div>
    <div class="kpi-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M12 19V5M5 12l7-7 7 7"/>
      </svg>
    </div>
  </div>
  <div class="kpi-card kpi-red">
    <div class="kpi-label">Total Nilai Stok</div>
    <div class="kpi-value" style="font-size:20px">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
    <div class="kpi-sub">Nilai sisa stok seluruh item</div>
    <div class="kpi-icon">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
      </svg>
    </div>
  </div>
</div>

{{-- CHARTS ROW --}}
<div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:20px">
  <div class="card">
    <div class="card-header"><div class="card-title">Nilai Stok per Kategori</div></div>
    <div class="card-body"><div class="chart-wrap"><canvas id="chartKategori"></canvas></div></div>
  </div>
  <div class="card">
    <div class="card-header"><div class="card-title">Top 10 Barang Keluar (Nilai)</div></div>
    <div class="card-body"><div class="chart-wrap"><canvas id="chartTopKeluar"></canvas></div></div>
  </div>
</div>

{{-- BOTTOM ROW --}}
<div class="grid-2col" style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
  {{-- Transaksi Terbaru --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Transaksi Terbaru</div>
      <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrapper">
      <table>
        <thead><tr>
          <th>No. Transaksi</th>
          <th>Tanggal</th>
          <th>Tipe</th>
          <th>Dibuat Oleh</th>
        </tr></thead>
        <tbody>
          @forelse($transaksiTerbaru as $t)
          <tr>
            <td><a href="{{ route('transaksi.show', $t) }}" class="font-mono fw-600">{{ $t->no_transaksi }}</a></td>
            <td>{{ $t->tanggal->format('d/m/Y') }}</td>
            <td>
              @if($t->tipe === 'masuk')
                <span class="badge badge-green">Masuk</span>
              @else
                <span class="badge badge-orange">Keluar</span>
              @endif
            </td>
            <td>{{ $t->dibuat_oleh }}</td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted" style="padding:24px">Belum ada transaksi</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  {{-- Stok Rendah --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Perhatian: Stok Rendah / Habis</div>
      <a href="{{ route('barang.index', ['status' => 'rendah']) }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrapper">
      <table>
        <thead><tr>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th class="text-right">Stok</th>
        </tr></thead>
        <tbody>
          @forelse($stokRendah as $b)
          <tr>
            <td><a href="{{ route('barang.show', $b) }}" class="td-name">{{ $b->nama }}</a></td>
            <td><span class="badge badge-gray">{{ $b->kategori->nama ?? '-' }}</span></td>
            <td class="text-right">
              <span class="stok-num {{ $b->stok_sekarang <= 0 ? 'stok-habis badge-red' : 'stok-rendah badge-orange' }}">
                {{ $b->stok_sekarang }}
              </span>
            </td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-center text-muted" style="padding:24px">Semua stok aman</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const C = ['#2563EB','#16A34A','#D97706','#DC2626','#0891B2','#7C3AED','#DB2777','#059669'];

// Chart kategori
const katData = @json($nilaiPerKategori);
new Chart(document.getElementById('chartKategori'), {
  type: 'doughnut',
  data: {
    labels: katData.map(x => x.nama),
    datasets: [{ data: katData.map(x => x.nilai), backgroundColor: C, borderWidth: 2, borderColor: '#fff', hoverOffset: 6 }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom', labels: { boxWidth: 12, padding: 12, font: { size: 11, family: 'Inter' } } },
      tooltip: { callbacks: { label: ctx => ' Rp ' + Number(ctx.parsed).toLocaleString('id-ID') } }
    }
  }
});

// Chart top keluar
const topData = @json($topKeluar);
new Chart(document.getElementById('chartTopKeluar'), {
  type: 'bar', indexAxis: 'y',
  data: {
    labels: topData.map(x => x.barang ? (x.barang.nama.length > 24 ? x.barang.nama.slice(0,22)+'..' : x.barang.nama) : '-'),
    datasets: [{ data: topData.map(x => x.total_nilai), backgroundColor: '#2563EB99', borderColor: '#2563EB', borderWidth: 1.5, borderRadius: 4 }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ' Rp ' + Number(ctx.parsed.x).toLocaleString('id-ID') } } },
    scales: {
      x: { grid: { color: '#E2E8F0' }, ticks: { callback: v => 'Rp '+ (v/1000000).toFixed(1)+'jt', font: { size: 10 } } },
      y: { grid: { display: false }, ticks: { font: { size: 11, family: 'Inter' } } }
    }
  }
});
</script>
@endpush
