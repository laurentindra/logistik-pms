@extends('layouts.app')
@section('title', 'Armada / Kapal')
@section('page-title', 'Armada / Kapal')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Data Armada / Kapal</div>
    <div class="page-sub">Kelola dan lihat rincian barang logistik per armada milik PT. Panca Merak Samudera</div>
  </div>
  <a href="{{ route('kapal.create') }}" class="btn btn-primary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
    Tambah Armada
  </a>
</div>

<div class="card">
  <div class="table-wrapper">
    <table>
      <thead><tr>
        <th>Kode</th>
        <th>Nama Armada</th>
        <th>Tipe</th>
        <th class="text-right">Total Transaksi</th>
        <th>Status</th>
        <th>Keterangan</th>
        <th class="text-center">Aksi</th>
      </tr></thead>
      <tbody>
        @forelse($kapals as $k)
        <tr>
          <td class="font-mono fw-700">
            <a href="{{ route('kapal.show', $k) }}" style="color:var(--primary)">{{ $k->kode }}</a>
          </td>
          <td class="td-name">
            <a href="{{ route('kapal.show', $k) }}" style="color:var(--text);font-weight:600">{{ $k->nama }}</a>
          </td>
          <td><span class="badge badge-teal">{{ $k->getTipeLabel() }}</span></td>
          <td class="text-right">
            <a href="{{ route('kapal.show', $k) }}" class="badge badge-blue">
              {{ number_format($k->transaksis_count) }} transaksi (Detail Barang)
            </a>
          </td>
          <td>
            @if($k->aktif)
              <span class="badge badge-green">Aktif</span>
            @else
              <span class="badge badge-gray">Tidak Aktif</span>
            @endif
          </td>
          <td style="max-width:180px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">{{ $k->keterangan ?: '-' }}</td>
          <td class="text-center">
            <div class="d-flex gap-2" style="justify-content:center">
              <a href="{{ route('kapal.show', $k) }}" class="btn-icon" title="Detail Barang Kapal Ini">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </a>
              <a href="{{ route('kapal.edit', $k) }}" class="btn-icon" title="Edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <button class="btn-icon" title="Hapus"
                onclick="confirmDelete('{{ route('kapal.destroy', $k) }}', 'Hapus armada {{ addslashes($k->kode) }}?')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--danger)"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7">
            <div class="empty-state">
              <p>Belum ada data armada</p>
              <a href="{{ route('kapal.create') }}" class="btn btn-primary">Tambah Armada Pertama</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
