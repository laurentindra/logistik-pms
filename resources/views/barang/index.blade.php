@extends('layouts.app')
@section('title', 'Data Barang')
@section('page-title', 'Data Barang')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Katalog Barang Logistik</div>
    <div class="page-sub">Kelola seluruh data barang logistik per armada & kategori</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('barang.create') }}" class="btn btn-primary">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
      Tambah Barang
    </a>
  </div>
</div>

<div class="card">
  {{-- FILTER BAR --}}
  <form method="GET" action="{{ route('barang.index') }}" id="filter-form">
    <div class="filter-bar">
      <input type="text" name="search" class="form-control filter-search" placeholder="Cari nama atau kode barang..." value="{{ request('search') }}" />

      <select name="kategori_id" class="form-control" onchange="this.form.submit()">
        <option value="">Semua Kategori</option>
        @foreach($kategoris as $k)
        <option value="{{ $k->id }}" {{ request('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
        @endforeach
      </select>

      <select name="kapal_id" class="form-control" onchange="this.form.submit()">
        <option value="">Semua Armada / Kapal</option>
        @foreach($kapals as $ship)
        <option value="{{ $ship->id }}" {{ request('kapal_id') == $ship->id ? 'selected' : '' }}>{{ $ship->kode }} &ndash; {{ $ship->nama }}</option>
        @endforeach
      </select>

      <select name="status" class="form-control" onchange="this.form.submit()">
        <option value="">Semua Status Stok</option>
        <option value="ada" {{ in_array(request('status'), ['ada', 'active', 'ada stok']) ? 'selected' : '' }}>Ada Stok (> 0)</option>
        <option value="rendah" {{ in_array(request('status'), ['rendah', 'stok rendah']) ? 'selected' : '' }}>Stok Rendah (1–3)</option>
        <option value="habis" {{ in_array(request('status'), ['habis', 'zero', 'stok 0']) ? 'selected' : '' }}>Stok Habis (0)</option>
        <option value="keluar" {{ request('status') === 'keluar' ? 'selected' : '' }}>Ada Barang Keluar</option>
        <option value="masuk" {{ request('status') === 'masuk' ? 'selected' : '' }}>Ada Barang Masuk</option>
      </select>

      <button type="submit" class="btn btn-primary">Cari</button>

      @if(request()->hasAny(['search','kategori_id','kapal_id','status']))
        <a href="{{ route('barang.index') }}" class="btn btn-secondary">Reset Filter</a>
      @endif
    </div>
  </form>

  {{-- TABLE --}}
  <div class="table-wrapper">
    <table>
      <thead>
        <tr>
          <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'kode_barang', 'dir' => request('sort') === 'kode_barang' && request('dir') === 'asc' ? 'desc' : 'asc']) }}">Kode</a></th>
          <th><a href="{{ request()->fullUrlWithQuery(['sort' => 'nama', 'dir' => request('sort') === 'nama' && request('dir') === 'asc' ? 'desc' : 'asc']) }}">Nama Barang</a></th>
          <th>Kategori</th>
          <th>Armada Terkait</th>
          <th>Satuan</th>
          <th class="text-right"><a href="{{ request()->fullUrlWithQuery(['sort' => 'harga_satuan', 'dir' => request('sort') === 'harga_satuan' && request('dir') === 'asc' ? 'desc' : 'asc']) }}">Harga Satuan</a></th>
          <th class="text-right"><a href="{{ request()->fullUrlWithQuery(['sort' => 'stok_sekarang', 'dir' => request('sort') === 'stok_sekarang' && request('dir') === 'asc' ? 'desc' : 'asc']) }}">Stok</a></th>
          <th class="text-right">Nilai Stok</th>
          <th class="text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($barangs as $b)
        <tr>
          <td class="font-mono" style="font-size:12px">{{ $b->kode_barang }}</td>
          <td class="td-name">
            <a href="{{ route('barang.show', $b) }}" style="color:var(--text);font-weight:600">{{ $b->nama }}</a>
          </td>
          <td>
            @if($b->kategori)
              <span class="badge badge-blue">{{ $b->kategori->nama }}</span>
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td>
            @php
              $ships = $b->transaksiItems->pluck('transaksi.kapal.kode')->filter()->unique();
            @endphp
            @if($ships->count() > 0)
              @foreach($ships as $sCode)
                <span class="badge badge-teal" style="font-size:10px;margin-right:2px">{{ $sCode }}</span>
              @endforeach
            @else
              <span class="text-muted" style="font-size:11px">-</span>
            @endif
          </td>
          <td>{{ $b->satuan }}</td>
          <td class="text-right">
            @if($b->harga_satuan > 0)
              Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td class="text-right">
            @php $status = $b->status_stok; @endphp
            <span class="stok-num badge
              @if($status === 'aman') badge-green
              @elseif($status === 'rendah') badge-orange
              @else badge-red @endif">
              {{ $b->stok_sekarang }}
            </span>
          </td>
          <td class="text-right">
            @if($b->nilai_stok > 0)
              Rp {{ number_format($b->nilai_stok, 0, ',', '.') }}
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td class="text-center">
            <div class="d-flex gap-2" style="justify-content:center">
              <a href="{{ route('barang.show', $b) }}" class="btn-icon" title="Detail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </a>
              <a href="{{ route('barang.edit', $b) }}" class="btn-icon" title="Edit">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
              </a>
              <button class="btn-icon" title="Hapus" onclick="confirmDelete('{{ route('barang.destroy', $b) }}', 'Hapus barang {{ addslashes($b->nama) }}?')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--danger)"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div class="empty-state">
              <div class="empty-state-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/></svg>
              </div>
              <p>Tidak ada barang ditemukan sesuai filter</p>
              <a href="{{ route('barang.index') }}" class="btn btn-secondary">Lihat Semua Barang</a>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- PAGINATION --}}
  @if($barangs->hasPages())
  <div class="pagination-wrap">
    <span>Menampilkan {{ $barangs->firstItem() }}–{{ $barangs->lastItem() }} dari {{ $barangs->total() }} item</span>
    <ul class="pagination">
      @if($barangs->onFirstPage())
        <li class="page-item disabled"><span class="page-link">&#8249;</span></li>
      @else
        <li class="page-item"><a class="page-link" href="{{ $barangs->previousPageUrl() }}">&#8249;</a></li>
      @endif
      @foreach($barangs->getUrlRange(max(1, $barangs->currentPage()-2), min($barangs->lastPage(), $barangs->currentPage()+2)) as $page => $url)
        <li class="page-item {{ $page == $barangs->currentPage() ? 'active' : '' }}">
          <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
      @endforeach
      @if($barangs->hasMorePages())
        <li class="page-item"><a class="page-link" href="{{ $barangs->nextPageUrl() }}">&#8250;</a></li>
      @else
        <li class="page-item disabled"><span class="page-link">&#8250;</span></li>
      @endif
    </ul>
  </div>
  @endif
</div>
@endsection
