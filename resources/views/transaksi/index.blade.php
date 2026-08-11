@extends('layouts.app')
@section('title', 'Histori Transaksi')
@section('page-title', 'Histori Transaksi')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Histori Transaksi</div>
    <div class="page-sub">Rekap seluruh transaksi masuk dan keluar</div>
  </div>
  <div class="page-actions">
    <a href="{{ route('transaksi.create-masuk') }}" class="btn btn-primary">Barang Masuk</a>
    <a href="{{ route('transaksi.create-keluar') }}" class="btn btn-secondary">Barang Keluar</a>
  </div>
</div>

<div class="card">
  <form method="GET">
    <div class="filter-bar">
      <input type="text" name="search" class="form-control filter-search" placeholder="Cari no. transaksi / operator..." value="{{ request('search') }}" />
      <select name="tipe" class="form-control">
        <option value="">Semua Tipe</option>
        <option value="masuk"  {{ request('tipe') === 'masuk'  ? 'selected' : '' }}>Masuk</option>
        <option value="keluar" {{ request('tipe') === 'keluar' ? 'selected' : '' }}>Keluar</option>
      </select>
      <select name="kapal_id" class="form-control">
        <option value="">Semua Armada</option>
        @foreach($kapals as $k)
        <option value="{{ $k->id }}" {{ request('kapal_id') == $k->id ? 'selected' : '' }}>{{ $k->kode }}</option>
        @endforeach
      </select>
      <input type="date" name="dari" class="form-control" value="{{ request('dari') }}" title="Dari tanggal" />
      <input type="date" name="sampai" class="form-control" value="{{ request('sampai') }}" title="Sampai tanggal" />
      <button type="submit" class="btn btn-primary">Cari</button>
      @if(request()->hasAny(['search','tipe','kapal_id','dari','sampai']))
        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Reset</a>
      @endif
    </div>
  </form>

  <div class="table-wrapper">
    <table>
      <thead><tr>
        <th>No. Transaksi</th>
        <th>Tanggal</th>
        <th>Tipe</th>
        <th>Armada Tujuan</th>
        <th class="text-right">Jml Item</th>
        <th class="text-right">Total Nilai</th>
        <th>Dibuat Oleh</th>
        <th>Keterangan</th>
        <th class="text-center">Aksi</th>
      </tr></thead>
      <tbody>
        @forelse($transaksis as $t)
        <tr>
          <td>
            <a href="{{ route('transaksi.show', $t) }}" class="font-mono fw-600" style="font-size:12.5px">
              {{ $t->no_transaksi }}
            </a>
          </td>
          <td>{{ $t->tanggal->format('d/m/Y') }}</td>
          <td>
            @if($t->tipe === 'masuk')
              <span class="badge badge-green">Masuk</span>
            @else
              <span class="badge badge-orange">Keluar</span>
            @endif
          </td>
          <td>
            @if($t->kapal)
              <span class="badge badge-blue">{{ $t->kapal->kode }}</span>
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td class="text-right">{{ $t->items->count() }} jenis</td>
          <td class="text-right">
            @if($t->total_nilai > 0)
              Rp {{ number_format($t->total_nilai, 0, ',', '.') }}
            @else
              <span class="text-muted">-</span>
            @endif
          </td>
          <td>{{ $t->dibuat_oleh }}</td>
          <td style="max-width:160px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis" title="{{ $t->keterangan }}">
            {{ $t->keterangan ?: '-' }}
          </td>
          <td class="text-center">
            <div class="d-flex gap-2" style="justify-content:center">
              <a href="{{ route('transaksi.show', $t) }}" class="btn-icon" title="Detail">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              </a>
              <button class="btn-icon" title="Batalkan Transaksi"
                onclick="confirmDelete('{{ route('transaksi.destroy', $t) }}', 'Batalkan transaksi {{ $t->no_transaksi }}? Stok akan dikembalikan ke kondisi sebelumnya.')">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color:var(--danger)"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6m3 0V4a1 1 0 011-1h4a1 1 0 011 1v2"/></svg>
              </button>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9">
            <div class="empty-state"><p>Belum ada transaksi</p></div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($transaksis->hasPages())
  <div class="pagination-wrap">
    <span>Menampilkan {{ $transaksis->firstItem() }}–{{ $transaksis->lastItem() }} dari {{ $transaksis->total() }} transaksi</span>
    <ul class="pagination">
      @if($transaksis->onFirstPage())
        <li class="page-item disabled"><span class="page-link">&#8249;</span></li>
      @else
        <li class="page-item"><a class="page-link" href="{{ $transaksis->previousPageUrl() }}">&#8249;</a></li>
      @endif
      @foreach($transaksis->getUrlRange(max(1, $transaksis->currentPage()-2), min($transaksis->lastPage(), $transaksis->currentPage()+2)) as $page => $url)
        <li class="page-item {{ $page == $transaksis->currentPage() ? 'active' : '' }}">
          <a class="page-link" href="{{ $url }}">{{ $page }}</a>
        </li>
      @endforeach
      @if($transaksis->hasMorePages())
        <li class="page-item"><a class="page-link" href="{{ $transaksis->nextPageUrl() }}">&#8250;</a></li>
      @else
        <li class="page-item disabled"><span class="page-link">&#8250;</span></li>
      @endif
    </ul>
  </div>
  @endif
</div>
@endsection
