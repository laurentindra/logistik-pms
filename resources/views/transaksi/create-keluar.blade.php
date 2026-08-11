@extends('layouts.app')
@section('title', 'Barang Keluar')
@section('page-title', 'Input Barang Keluar')

@section('content')
<div class="page-header">
  <div>
    <div class="page-title">Input Barang Keluar</div>
    <div class="page-sub">Catat pengeluaran barang dari gudang ke armada</div>
  </div>
  <a href="{{ route('transaksi.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<form method="POST" action="{{ route('transaksi.store-keluar') }}" id="form-keluar">
@csrf
<div class="detail-grid" style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start">

  {{-- Items --}}
  <div class="card">
    <div class="card-header">
      <div class="card-title">Daftar Barang Keluar</div>
      <button type="button" class="btn btn-primary btn-sm" onclick="addRow()">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Tambah Baris
      </button>
    </div>
    <div class="card-body" style="padding:0">
      <div style="display:grid;grid-template-columns:1fr 110px 140px 120px 36px;gap:8px;padding:10px 16px;background:var(--surface2);border-bottom:1px solid var(--border);font-size:11px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.04em">
        <span>Nama Barang (Stok Tersedia)</span><span>Qty</span><span>Harga Satuan</span><span>Subtotal</span><span></span>
      </div>
      <div id="items-container" style="padding:8px 16px"></div>
    </div>
    <div class="card-footer">
      <div style="display:flex;justify-content:flex-end;align-items:center;gap:16px">
        <span style="font-size:13px;color:var(--text-muted)">Total Nilai:</span>
        <span style="font-size:20px;font-weight:800;color:var(--primary)" id="grand-total">Rp 0</span>
      </div>
    </div>
  </div>

  {{-- Info --}}
  <div>
    <div class="card">
      <div class="card-header"><div class="card-title">Informasi Transaksi</div></div>
      <div class="card-body">
        <div class="form-group">
          <label class="form-label">No. Transaksi</label>
          <input type="text" class="form-control" value="{{ $no }}" readonly style="background:var(--surface2);font-family:monospace;font-weight:600" />
        </div>
        <div class="form-group">
          <label class="form-label required" for="tanggal">Tanggal Keluar</label>
          <input type="date" id="tanggal" name="tanggal" class="form-control {{ $errors->has('tanggal') ? 'is-invalid' : '' }}"
                 value="{{ old('tanggal', date('Y-m-d')) }}" required />
          @error('tanggal')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="kapal_id">Tujuan Armada</label>
          <select id="kapal_id" name="kapal_id" class="form-control">
            <option value="">-- Tidak Ada / Umum --</option>
            @foreach($kapals as $k)
            <option value="{{ $k->id }}" {{ old('kapal_id', request('kapal_id')) == $k->id ? 'selected' : '' }}>{{ $k->kode }} &ndash; {{ $k->nama }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label class="form-label required" for="dibuat_oleh">Dibuat Oleh</label>
          <input type="text" id="dibuat_oleh" name="dibuat_oleh" class="form-control {{ $errors->has('dibuat_oleh') ? 'is-invalid' : '' }}"
                 value="{{ old('dibuat_oleh', Auth::user()->name) }}" required />
          @error('dibuat_oleh')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="form-group">
          <label class="form-label" for="keterangan">Keterangan</label>
          <textarea id="keterangan" name="keterangan" class="form-control" rows="3" placeholder="Tujuan penggunaan, catatan...">{{ old('keterangan') }}</textarea>
        </div>

        @if($errors->any())
        <div class="alert alert-error" style="margin-bottom:14px">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
          <div>
            @foreach($errors->all() as $err)
            <div>{{ $err }}</div>
            @endforeach
          </div>
        </div>
        @endif

        <button type="submit" class="btn btn-warning btn-lg" style="width:100%;justify-content:center;background:var(--warning);color:#fff;border-color:var(--warning)">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 19V5M5 12l7-7 7 7"/></svg>
          Simpan Barang Keluar
        </button>
      </div>
    </div>

    {{-- Stok Info Panel --}}
    <div class="card" style="margin-top:16px" id="stok-panel" style="display:none">
      <div class="card-header"><div class="card-title">Info Stok Barang</div></div>
      <div class="card-body" id="stok-info" style="font-size:13px;color:var(--text-mid)">
        Pilih barang untuk melihat stok tersedia.
      </div>
    </div>
  </div>

</div>
</form>
@endsection

@push('scripts')
<script>
const barangs = @json($barangs);
let rowIdx = 0;

function addRow(barangId = '') {
  const container = document.getElementById('items-container');
  const idx = rowIdx++;
  const options = barangs.map(b =>
    `<option value="${b.id}" data-harga="${b.harga_satuan}" data-stok="${b.stok_sekarang}" data-satuan="${b.satuan}" ${b.id == barangId ? 'selected' : ''}>
      ${b.nama} [Stok: ${b.stok_sekarang} ${b.satuan}]
    </option>`
  ).join('');

  const row = document.createElement('div');
  row.className = 'item-row';
  row.id = `row-${idx}`;
  row.innerHTML = `
    <select name="items[${idx}][barang_id]" class="form-control" onchange="onBarangChange(this, ${idx})" required>
      <option value="">-- Pilih Barang --</option>
      ${options}
    </select>
    <input type="number" name="items[${idx}][jumlah]" id="qty-${idx}" class="form-control" placeholder="Qty" min="1" value="1" oninput="recalc(${idx})" required />
    <input type="number" name="items[${idx}][harga_satuan]" id="hrg-${idx}" class="form-control" placeholder="Harga" min="0" step="100" value="0" oninput="recalc(${idx})" required />
    <span id="sub-${idx}" style="font-weight:600;font-size:13px;color:var(--text-mid)">Rp 0</span>
    <button type="button" onclick="removeRow(${idx})" style="background:none;border:none;cursor:pointer;color:var(--danger);padding:4px">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
    </button>`;
  container.appendChild(row);

  if (barangId) {
    const sel = row.querySelector('select');
    onBarangChange(sel, idx);
  }
}

function onBarangChange(sel, idx) {
  const opt = sel.options[sel.selectedIndex];
  const harga = opt?.dataset?.harga || 0;
  const stok  = parseInt(opt?.dataset?.stok || 0);
  document.getElementById(`hrg-${idx}`).value = harga;
  document.getElementById(`qty-${idx}`).max   = stok;
  recalc(idx);

  const panel = document.getElementById('stok-panel');
  const info  = document.getElementById('stok-info');
  if (opt?.value) {
    panel.style.display = 'block';
    info.innerHTML = `Stok tersedia: <strong style="color:${stok>0?'var(--success)':'var(--danger)'}">${stok} ${opt.dataset.satuan}</strong>`;
  }
}

function recalc(idx) {
  const qty   = parseFloat(document.querySelector(`[name="items[${idx}][jumlah]"]`)?.value) || 0;
  const harga = parseFloat(document.getElementById(`hrg-${idx}`)?.value) || 0;
  const sub   = qty * harga;
  const subEl = document.getElementById(`sub-${idx}`);
  if (subEl) subEl.textContent = 'Rp ' + sub.toLocaleString('id-ID');
  updateGrandTotal();
}

function removeRow(idx) {
  document.getElementById(`row-${idx}`)?.remove();
  updateGrandTotal();
}

function updateGrandTotal() {
  let total = 0;
  document.querySelectorAll('[id^="sub-"]').forEach(el => {
    total += parseFloat(el.textContent.replace(/[^0-9]/g,'')) || 0;
  });
  document.getElementById('grand-total').textContent = 'Rp ' + total.toLocaleString('id-ID');
}

const initId = new URLSearchParams(location.search).get('barang_id') || '';
addRow(initId);
</script>
@endpush
