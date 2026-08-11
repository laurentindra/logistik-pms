@extends('layouts.app')
@section('title', $transaksi->no_transaksi)
@section('page-title', 'Detail Transaksi')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">{{ $transaksi->no_transaksi }}</div>
    <div class="page-sub">
      {{ $transaksi->tanggal->format('d F Y') }}
      &nbsp;&ndash;&nbsp;
      @if($transaksi->tipe === 'masuk')
        <span class="badge badge-green">Barang Masuk</span>
      @else
        <span class="badge badge-orange">Barang Keluar</span>
      @endif
    </div>
  </div>
  <div class="page-actions">
    <button class="btn btn-danger btn-sm"
      onclick="confirmDelete('{{ route('transaksi.destroy', $transaksi) }}', 'Batalkan transaksi ini? Stok akan dikembalikan.')">
      Batalkan Transaksi
    </button>
    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-sm">Kembali</a>
  </div>
</div>

<div style="display:grid;grid-template-columns:280px 1fr;gap:20px">
  {{-- Info --}}
  <div class="card" style="height:fit-content">
    <div class="card-header"><div class="card-title">Informasi Transaksi</div></div>
    <div class="card-body">
      @php
        $info = [
          'No. Transaksi' => $transaksi->no_transaksi,
          'Tipe'          => ucfirst($transaksi->tipe),
          'Tanggal'       => $transaksi->tanggal->format('d/m/Y'),
          'Dibuat Oleh'   => $transaksi->dibuat_oleh,
          'Armada'        => $transaksi->kapal ? $transaksi->kapal->kode . ' – ' . $transaksi->kapal->nama : '-',
          'Keterangan'    => $transaksi->keterangan ?: '-',
        ];
      @endphp
      @foreach($info as $label => $val)
      <div style="display:flex;flex-direction:column;padding:8px 0;border-bottom:1px solid var(--border)">
        <span style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600">{{ $label }}</span>
        <span style="font-size:13.5px;font-weight:500;margin-top:2px">{{ $val }}</span>
      </div>
      @endforeach

      <div style="margin-top:16px;padding-top:8px">
        <div style="font-size:11px;color:var(--text-muted);text-transform:uppercase;letter-spacing:.05em;font-weight:600;margin-bottom:4px">Total Nilai</div>
        <div style="font-size:22px;font-weight:800;color:var(--primary)">
          Rp {{ number_format($transaksi->items->sum('subtotal'), 0, ',', '.') }}
        </div>
        <div style="font-size:12px;color:var(--text-muted)">{{ $transaksi->items->sum('jumlah') }} item total qty</div>
      </div>
    </div>
  </div>

  {{-- Items --}}
  <div class="card">
    <div class="card-header"><div class="card-title">Detail Barang</div></div>
    <div class="table-wrapper">
      <table>
        <thead><tr>
          <th>#</th>
          <th>Nama Barang</th>
          <th>Kategori</th>
          <th class="text-right">Qty</th>
          <th>Satuan</th>
          <th class="text-right">Harga Satuan</th>
          <th class="text-right">Subtotal</th>
        </tr></thead>
        <tbody>
          @foreach($transaksi->items as $i => $item)
          <tr>
            <td>{{ $i + 1 }}</td>
            <td class="td-name">
              <a href="{{ route('barang.show', $item->barang) }}">{{ $item->barang->nama }}</a>
            </td>
            <td><span class="badge badge-blue">{{ $item->barang->kategori->nama ?? '-' }}</span></td>
            <td class="text-right fw-700
              {{ $transaksi->tipe === 'masuk' ? 'text-success' : 'text-danger' }}">
              {{ $item->jumlah }}
            </td>
            <td>{{ $item->barang->satuan }}</td>
            <td class="text-right">
              @if($item->harga_satuan > 0)
                Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}
              @else <span class="text-muted">-</span> @endif
            </td>
            <td class="text-right fw-600">
              @if($item->subtotal > 0)
                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
              @else <span class="text-muted">-</span> @endif
            </td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr style="background:var(--primary-light)">
            <td colspan="6" class="text-right fw-600" style="padding:12px 14px;font-size:13px;color:var(--text-mid)">Total Nilai Transaksi</td>
            <td class="text-right fw-700" style="padding:12px 14px;font-size:15px;color:var(--primary)">
              Rp {{ number_format($transaksi->items->sum('subtotal'), 0, ',', '.') }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>
</div>
@endsection
