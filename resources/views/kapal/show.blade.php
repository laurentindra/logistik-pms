@extends('layouts.app')
@section('title', 'Detail Armada - ' . $kapal->kode)
@section('page-title', 'Detail Armada')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $kapal->kode }} &ndash; {{ $kapal->nama }}</div>
    <div class="page-sub">
      Tipe: <span class="badge badge-teal">{{ $kapal->getTipeLabel() }}</span>
      &nbsp;&bull;&nbsp;
      Status:
      @if($kapal->aktif)
        <span class="badge badge-green">Aktif</span>
      @else
        <span class="badge badge-gray">Tidak Aktif</span>
      @endif
    </div>
  </div>
  <div class="page-actions">
    <a href="{{ route('transaksi.create-keluar') }}?kapal_id={{ $kapal->id }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Input Barang Keluar ke Kapal Ini
    </a>
    <a href="{{ route('kapal.edit', $kapal) }}" class="btn btn-secondary">Edit Armada</a>
    <a href="{{ route('kapal.index') }}" class="btn btn-secondary">Kembali</a>
  </div>
</div>

{{-- KPI SUMMARY FOR THIS SHIP --}}
<div class="kpi-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); margin-bottom:20px;">
  <div class="kpi-card kpi-blue">
    <div class="kpi-label">Jenis Barang Dikeluarkan</div>
    <div class="kpi-value">{{ $itemsKeluar->unique('barang_id')->count() }}</div>
    <div class="kpi-sub">Item terdaftar untuk {{ $kapal->kode }}</div>
  </div>
  <div class="kpi-card kpi-orange">
    <div class="kpi-label">Total Qty Dikeluarkan</div>
    <div class="kpi-value">{{ number_format($totalItemQty) }}</div>
    <div class="kpi-sub">Total unit barang</div>
  </div>
  <div class="kpi-card kpi-green">
    <div class="kpi-label">Total Nilai Pengeluaran</div>
    <div class="kpi-value" style="font-size:22px">Rp {{ number_format($totalNilai, 0, ',', '.') }}</div>
    <div class="kpi-sub">Nilai logistik keluar ke armada ini</div>
  </div>
</div>

{{-- TABEL BARANG KELUAR KAPAL INI --}}
<div class="card">
  <div class="card-header">
    <div class="card-title">Daftar Pengeluaran Barang Logistik ke {{ $kapal->kode }}</div>
    <a href="{{ route('barang.index', ['kapal_id' => $kapal->id]) }}" class="btn btn-secondary btn-sm">Lihat di Katalog Barang</a>
  </div>
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th>No</th>
          <th>Kode Barang</th>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th>Satuan</th>
          <th class="text-right">Harga Satuan</th>
          <th class="text-right">Qty Keluar</th>
          <th class="text-right">Total Nilai</th>
          <th>No. Transaksi</th>
          <th>Tanggal</th>
        </tr>
      </thead>
      <tbody>
        @forelse($itemsKeluar as $idx => $item)
        <tr>
          <td>{{ $idx + 1 }}</td>
          <td class="font-mono" style="font-size:12px">{{ $item->barang->kode_barang ?? '-' }}</td>
          <td class="td-name">
            <a href="{{ route('barang.show', $item->barang) }}">{{ $item->barang->nama ?? '-' }}</a>
          </td>
          <td>
            @if($item->barang && $item->barang->kategori)
              <span class="badge badge-blue">{{ $item->barang->kategori->nama }}</span>
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td>{{ $item->barang->satuan ?? '-' }}</td>
          <td class="text-right">
            @if($item->harga_satuan > 0)
              Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td class="text-right fw-700 text-danger">{{ number_format($item->jumlah) }}</td>
          <td class="text-right fw-600">
            @if($item->subtotal > 0)
              Rp {{ number_format($item->subtotal, 0, ',', '.') }}
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td>
            <a href="{{ route('transaksi.show', $item->transaksi) }}" class="font-mono" style="font-size:12px">
              {{ $item->transaksi->no_transaksi }}
            </a>
          </td>
          <td>{{ $item->transaksi->tanggal->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="10">
            <div class="empty-state">
              <p>Belum ada transaksi barang keluar yang dicatat untuk armada {{ $kapal->kode }}</p>
              <a href="{{ route('transaksi.create-keluar') }}?kapal_id={{ $kapal->id }}" class="btn btn-primary">Input Pengeluaran Pertama</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
      @if($itemsKeluar->count() > 0)
      <tfoot>
        <tr style="background:var(--primary-light)">
          <td colspan="6" class="text-right fw-600" style="padding:12px 14px">Total Pengeluaran Logistik</td>
          <td class="text-right fw-700 text-danger" style="padding:12px 14px">{{ number_format($totalItemQty) }}</td>
          <td class="text-right fw-700 text-primary" style="padding:12px 14px;font-size:15px">
            Rp {{ number_format($totalNilai, 0, ',', '.') }}
          </td>
          <td colspan="2"></td>
        </tr>
      </tfoot>
      @endif
    </table>
  </div>
</div>
@endsection
