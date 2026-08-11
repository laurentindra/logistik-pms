@extends('layouts.app')
@section('title', 'Tambah Armada')
@section('page-title', 'Tambah Armada')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Tambah Armada Baru</div>
    <div class="page-sub">Daftarkan kapal atau tongkang baru</div>
  </div>
  <a href="{{ route('kapal.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div style="max-width:580px">
  <div class="card">
    <div class="card-header"><div class="card-title">Informasi Armada</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('kapal.store') }}">
        @csrf

        <div class="form-row">
          <div class="form-group">
            <label class="form-label required" for="kode">Kode Armada</label>
            <input type="text" id="kode" name="kode" class="form-control {{ $errors->has('kode') ? 'is-invalid' : '' }}"
                   value="{{ old('kode') }}" placeholder="Contoh: H.101, BG. DLL" required />
            @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label required" for="tipe">Tipe</label>
            <select id="tipe" name="tipe" class="form-control {{ $errors->has('tipe') ? 'is-invalid' : '' }}">
              <option value="kapal" {{ old('tipe') === 'kapal' ? 'selected' : '' }}>Kapal</option>
              <option value="tongkang" {{ old('tipe') === 'tongkang' ? 'selected' : '' }}>Tongkang / BG</option>
              <option value="lainnya" {{ old('tipe') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('tipe')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label required" for="nama">Nama Armada</label>
          <input type="text" id="nama" name="nama" class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                 value="{{ old('nama') }}" placeholder="Contoh: KM. CATLEYA, Tugboat H.101" required />
          @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="keterangan">Keterangan</label>
          <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Catatan armada...">{{ old('keterangan') }}</textarea>
        </div>

        <div class="form-group" style="display:flex;align-items:center;gap:8px">
          <input type="checkbox" id="aktif" name="aktif" value="1" {{ old('aktif', '1') ? 'checked' : '' }} style="width:auto">
          <label for="aktif" style="margin:0;font-size:13.5px">Status Aktif</label>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
          <a href="{{ route('kapal.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Armada</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
