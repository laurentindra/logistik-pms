@extends('layouts.app')
@section('title', 'Edit Barang')
@section('page-title', 'Edit Barang')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Edit Barang</div>
    <div class="page-sub">{{ $barang->kode_barang }} &ndash; {{ $barang->nama }}</div>
  </div>
  <a href="{{ route('barang.index') }}" class="btn btn-secondary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div style="max-width:680px">
  <div class="card">
    <div class="card-header"><div class="card-title">Edit Informasi Barang</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('barang.update', $barang) }}">
        @csrf @method('PUT')

        <div class="form-row">
          <div class="form-group">
            <label class="form-label required" for="kode_barang">Kode Barang</label>
            <input type="text" id="kode_barang" name="kode_barang" class="form-control {{ $errors->has('kode_barang') ? 'is-invalid' : '' }}"
                   value="{{ old('kode_barang', $barang->kode_barang) }}" />
            @error('kode_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label required" for="kategori_id">Kategori</label>
            <select id="kategori_id" name="kategori_id" class="form-control {{ $errors->has('kategori_id') ? 'is-invalid' : '' }}">
              <option value="">-- Pilih Kategori --</option>
              @foreach($kategoris as $k)
              <option value="{{ $k->id }}" {{ old('kategori_id', $barang->kategori_id) == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
              @endforeach
            </select>
            @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label required" for="nama">Nama Barang</label>
          <input type="text" id="nama" name="nama" class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                 value="{{ old('nama', $barang->nama) }}" />
          @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label required" for="satuan">Satuan</label>
            <input type="text" id="satuan" name="satuan" class="form-control {{ $errors->has('satuan') ? 'is-invalid' : '' }}"
                   value="{{ old('satuan', $barang->satuan) }}" list="satuan-list" />
            <datalist id="satuan-list">
              <option value="Pcs"><option value="Set"><option value="Unit"><option value="Drum">
              <option value="Roll"><option value="Meter"><option value="Kaleng"><option value="Kg">
              <option value="Btl"><option value="Jrgn"><option value="Pasang"><option value="Ton">
            </datalist>
            @error('satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label required" for="harga_satuan">Harga Satuan (Rp)</label>
            <input type="number" id="harga_satuan" name="harga_satuan" class="form-control {{ $errors->has('harga_satuan') ? 'is-invalid' : '' }}"
                   value="{{ old('harga_satuan', $barang->harga_satuan) }}" min="0" step="100" />
            @error('harga_satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label required" for="stok_awal">Stok Awal</label>
            <input type="number" id="stok_awal" name="stok_awal" class="form-control {{ $errors->has('stok_awal') ? 'is-invalid' : '' }}"
                   value="{{ old('stok_awal', $barang->stok_awal) }}" min="0" />
            @error('stok_awal')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label">Stok Sekarang</label>
            <input type="text" class="form-control" value="{{ $barang->stok_sekarang }} {{ $barang->satuan }}" readonly style="background:var(--surface2);cursor:not-allowed" />
            <div class="form-hint">Stok sekarang dikelola otomatis melalui transaksi</div>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="keterangan">Keterangan</label>
          <textarea id="keterangan" name="keterangan" class="form-control" rows="3">{{ old('keterangan', $barang->keterangan) }}</textarea>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
          <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
