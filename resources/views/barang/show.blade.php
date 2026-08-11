@extends('layouts.app')
@section('title', $barang->nama)
@section('page-title', 'Detail Barang')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $barang->nama }}</div>
    <div class="page-sub">{{ $barang->kode_barang }}</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('barang.edit', $barang) }}" class="btn btn-primary btn-sm">Edit Barang</a>
    <a href="{{ route('barang.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
  </div>
</div>

<div class="detail-grid" style="display:grid;grid-template-columns:320px 1fr;gap:20px">
  {{-- Info Card --}}
  <div>
    <div class="card mb-4" style="margin-bottom:16px">
      <div class="card-header"><div class="card-title">Informasi Barang</div></div>
      <div class="card-body">
        <table style="width:100%;border-collapse:collapse">
          @php
            $rows = [
              'Kode Barang'    => $barang->kode_barang,
              'Kategori'       => $barang->kategori->nama ?? '-',
              'Satuan'         => $barang->satuan,
              'Harga Satuan'   => $barang->harga_satuan > 0 ? 'Rp ' . number_format($barang->harga_satuan, 0, ',', '.') : '-',
              'Stok Awal'      => $barang->stok_awal,
            ];
          @endphp
          @foreach($rows as $label => $val)
          <tr>
            <td style="padding:7px 0;color:var(--text-muted);font-size:12.5px;width:110px">{{ $label }}</td>
            <td style="padding:7px 0;font-weight:500;font-size:13px">{{ $val }}</td>
          </tr>
          @endforeach
        </table>

        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
          <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px">Stok Sekarang</div>
          @php $status = $barang->status_stok; @endphp
          <div style="font-size:36px;font-weight:800;
            color:{{ $status === 'aman' ? 'var(--success)' : ($status === 'rendah' ? 'var(--warning)' : 'var(--danger)') }}">
            {{ $barang->stok_sekarang }}
          </div>
          <div style="font-size:13px;color:var(--text-muted)">{{ $barang->satuan }}</div>
          <div style="margin-top:8px">
            <span class="badge
              @if($status === 'aman') badge-green
              @elseif($status === 'rendah') badge-orange
              @else badge-red @endif">
              {{ ucfirst($status) }}
            </span>
          </div>
        </div>

        @if($barang->nilai_stok > 0)
        <div style="margin-top:16px;padding-top:16px;border-top:1px solid var(--border)">
          <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:4px">Nilai Stok</div>
          <div style="font-size:18px;font-weight:700;color:var(--primary)">
            Rp {{ number_format($barang->nilai_stok, 0, ',', '.') }}
          </div>
        </div>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <div style="display:flex;gap:8px;flex-direction:column">
          <a href="{{ route('transaksi.create-masuk') }}?barang_id={{ $barang->id }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 5v14M5 12l7 7 7-7"/></svg>
            Input Barang Masuk
          </a>
          <a href="{{ route('transaksi.create-keluar') }}?barang_id={{ $barang->id }}" class="btn btn-secondary">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
            Input Barang Keluar
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Histori Transaksi --}}
  <div class="card">
    <div class="card-header"><div class="card-title">Histori Transaksi</div></div>
    <div class="table-wrapper">
      <table>
        <thead><tr>
          <th>No. Transaksi</th>
          <th>Tanggal</th>
          <th>Tipe</th>
          <th>Armada</th>
          <th class="text-right">Qty</th>
          <th class="text-right">Nilai</th>
          <th>Oleh</th>
        </tr></thead>
        <tbody>
          @forelse($barang->transaksiItems->sortByDesc('created_at') as $item)
          <tr>
            <td>
              <a href="{{ route('transaksi.show', $item->transaksi) }}" class="font-mono fw-600" style="font-size:12px">
                {{ $item->transaksi->no_transaksi }}
              </a>
            </td>
            <td>{{ $item->transaksi->tanggal->format('d/m/Y') }}</td>
            <td>
              @if($item->transaksi->tipe === 'masuk')
                <span class="badge badge-green">Masuk</span>
              @else
                <span class="badge badge-orange">Keluar</span>
              @endif
            </td>
            <td>
              @if($item->transaksi->kapal)
                <span class="badge badge-blue">{{ $item->transaksi->kapal->kode }}</span>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td class="text-right fw-600
              {{ $item->transaksi->tipe === 'masuk' ? 'text-success' : 'text-danger' }}">
              {{ $item->transaksi->tipe === 'masuk' ? '+' : '-' }}{{ $item->jumlah }}
            </td>
            <td class="text-right">
              @if($item->subtotal > 0)
                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>{{ $item->transaksi->dibuat_oleh }}</td>
          </tr>
          @empty
          <tr>
            <td colspan="7">
              <div class="empty-state">
                <p>Belum ada transaksi untuk barang ini</p>
              </div>
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
