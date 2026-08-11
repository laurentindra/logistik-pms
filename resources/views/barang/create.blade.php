@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('page-title', 'Tambah Barang')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Tambah Barang Baru</div>
    <div class="page-sub">Daftarkan item baru ke katalog logistik</div>
  </div>
  <a href="{{ route('barang.index') }}" class="btn btn-secondary">
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
    Kembali
  </a>
</div>

<div style="max-width:680px">
  <div class="card">
    <div class="card-header"><div class="card-title">Informasi Barang</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('barang.store') }}">
        @csrf

        <div class="form-row">
          <div class="form-group">
            <label class="form-label required" for="kode_barang">Kode Barang</label>
            <input type="text" id="kode_barang" name="kode_barang" class="form-control {{ $errors->has('kode_barang') ? 'is-invalid' : '' }}"
                   value="{{ old('kode_barang', $kode) }}" placeholder="BRG-00001" />
            @error('kode_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label required" for="kategori_id">Kategori</label>
            <select id="kategori_id" name="kategori_id" class="form-control {{ $errors->has('kategori_id') ? 'is-invalid' : '' }}">
              <option value="">-- Pilih Kategori --</option>
              @foreach($kategoris as $k)
              <option value="{{ $k->id }}" {{ old('kategori_id') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
              @endforeach
            </select>
            @error('kategori_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label required" for="nama">Nama Barang</label>
          <input type="text" id="nama" name="nama" class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                 value="{{ old('nama') }}" placeholder="Contoh: Filter Oli Donaldson P550587" />
          @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label required" for="satuan">Satuan</label>
            <input type="text" id="satuan" name="satuan" class="form-control {{ $errors->has('satuan') ? 'is-invalid' : '' }}"
                   value="{{ old('satuan') }}" placeholder="Pcs, Set, Drum, Roll..." list="satuan-list" />
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
                   value="{{ old('harga_satuan', 0) }}" min="0" step="100" />
            @error('harga_satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label required" for="stok_awal">Stok Awal</label>
          <input type="number" id="stok_awal" name="stok_awal" class="form-control {{ $errors->has('stok_awal') ? 'is-invalid' : '' }}"
                 value="{{ old('stok_awal', 0) }}" min="0" />
          <div class="form-hint">Stok saat ini akan diset sama dengan stok awal</div>
          @error('stok_awal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="keterangan">Keterangan</label>
          <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Keterangan tambahan (opsional)">{{ old('keterangan') }}</textarea>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
          <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Barang</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
