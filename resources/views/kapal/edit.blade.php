@extends('layouts.app')
@section('title', 'Edit Armada')
@section('page-title', 'Edit Armada')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Edit Armada</div>
    <div class="page-sub">{{ $kapal->kode }} &ndash; {{ $kapal->nama }}</div>
  </div>
  <a href="{{ route('kapal.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<div style="max-width:580px">
  <div class="card">
    <div class="card-header"><div class="card-title">Edit Informasi Armada</div></div>
    <div class="card-body">
      <form method="POST" action="{{ route('kapal.update', $kapal) }}">
        @csrf @method('PUT')

        <div class="form-row">
          <div class="form-group">
            <label class="form-label required" for="kode">Kode Armada</label>
            <input type="text" id="kode" name="kode" class="form-control {{ $errors->has('kode') ? 'is-invalid' : '' }}"
                   value="{{ old('kode', $kapal->kode) }}" required />
            @error('kode')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="form-group">
            <label class="form-label required" for="tipe">Tipe</label>
            <select id="tipe" name="tipe" class="form-control {{ $errors->has('tipe') ? 'is-invalid' : '' }}">
              <option value="kapal" {{ old('tipe', $kapal->tipe) === 'kapal' ? 'selected' : '' }}>Kapal</option>
              <option value="tongkang" {{ old('tipe', $kapal->tipe) === 'tongkang' ? 'selected' : '' }}>Tongkang / BG</option>
              <option value="lainnya" {{ old('tipe', $kapal->tipe) === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('tipe')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
        </div>

        <div class="form-group">
          <label class="form-label required" for="nama">Nama Armada</label>
          <input type="text" id="nama" name="nama" class="form-control {{ $errors->has('nama') ? 'is-invalid' : '' }}"
                 value="{{ old('nama', $kapal->nama) }}" required />
          @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="keterangan">Keterangan</label>
          <textarea id="keterangan" name="keterangan" class="form-control" rows="3">{{ old('keterangan', $kapal->keterangan) }}</textarea>
        </div>

        <div class="form-group" style="display:flex;align-items:center;gap:8px">
          <input type="checkbox" id="aktif" name="aktif" value="1" {{ old('aktif', $kapal->aktif) ? 'checked' : '' }} style="width:auto">
          <label for="aktif" style="margin:0;font-size:13.5px">Status Aktif</label>
        </div>

        <div style="display:flex;gap:10px;justify-content:flex-end">
          <a href="{{ route('kapal.index') }}" class="btn btn-secondary">Batal</a>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
