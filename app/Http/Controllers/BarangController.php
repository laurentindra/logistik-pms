<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kategori;
use App\Models\Kapal;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BarangController extends Controller
{
    public function index(Request $request)
    {
        $query = Barang::with(['kategori', 'transaksiItems.transaksi.kapal']);

        // Search text
        if ($request->filled('search')) {
            $q = trim($request->search);
            $query->where(fn($qb) => $qb->where('nama', 'like', "%{$q}%")
                ->orWhere('kode_barang', 'like', "%{$q}%")
                ->orWhere('satuan', 'like', "%{$q}%"));
        }

        // Category filter
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Kapal/Armada filter
        if ($request->filled('kapal_id')) {
            $query->whereHas('transaksiItems', function ($q) use ($request) {
                $q->whereHas('transaksi', function ($t) use ($request) {
                    $t->where('kapal_id', $request->kapal_id);
                });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $status = strtolower(trim($request->status));
            if (in_array($status, ['ada', 'active', 'ada stok', 'ada_stok'])) {
                $query->where('stok_sekarang', '>', 0);
            } elseif (in_array($status, ['habis', 'zero', 'stok 0', 'stok_habis', '0'])) {
                $query->where('stok_sekarang', '<=', 0);
            } elseif (in_array($status, ['rendah', 'stok rendah', 'stok_rendah'])) {
                $query->where('stok_sekarang', '>', 0)->where('stok_sekarang', '<=', 3);
            } elseif (in_array($status, ['keluar', 'ada keluar'])) {
                $query->whereHas('transaksiItems', function ($q) {
                    $q->whereHas('transaksi', fn($t) => $t->where('tipe', 'keluar'));
                });
            } elseif (in_array($status, ['masuk', 'ada masuk'])) {
                $query->whereHas('transaksiItems', function ($q) {
                    $q->whereHas('transaksi', fn($t) => $t->where('tipe', 'masuk'));
                });
            }
        }

        // Sorting
        $sort = $request->get('sort', 'nama');
        $dir  = strtolower($request->get('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $allowed = ['nama', 'kode_barang', 'harga_satuan', 'stok_sekarang'];
        if (in_array($sort, $allowed)) {
            $query->orderBy($sort, $dir);
        } else {
            $query->orderBy('nama', 'asc');
        }

        $barangs   = $query->paginate(25)->withQueryString();
        $kategoris = Kategori::orderBy('nama')->get();
        $kapals    = Kapal::where('aktif', true)->orderBy('kode')->get();

        return view('barang.index', compact('barangs', 'kategoris', 'kapals'));
    }

    public function create()
    {
        $kategoris = Kategori::orderBy('nama')->get();
        $kode      = $this->generateKode();
        return view('barang.create', compact('kategoris', 'kode'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode_barang'  => 'required|string|max:50|unique:barangs,kode_barang',
            'nama'         => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'kategori_id'  => 'required|exists:kategoris,id',
            'harga_satuan' => 'required|numeric|min:0',
            'stok_awal'    => 'required|integer|min:0',
            'keterangan'   => 'nullable|string',
        ]);

        $data['stok_sekarang'] = $data['stok_awal'];
        Barang::create($data);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function show(Barang $barang)
    {
        $barang->load([
            'kategori',
            'transaksiItems.transaksi.kapal',
            'transaksiItems.transaksi' => fn($q) => $q->latest()->limit(50),
        ]);
        return view('barang.show', compact('barang'));
    }

    public function edit(Barang $barang)
    {
        $kategoris = Kategori::orderBy('nama')->get();
        return view('barang.edit', compact('barang', 'kategoris'));
    }

    public function update(Request $request, Barang $barang)
    {
        $data = $request->validate([
            'kode_barang'  => ['required', 'string', 'max:50', Rule::unique('barangs', 'kode_barang')->ignore($barang->id)],
            'nama'         => 'required|string|max:255',
            'satuan'       => 'required|string|max:50',
            'kategori_id'  => 'required|exists:kategoris,id',
            'harga_satuan' => 'required|numeric|min:0',
            'stok_awal'    => 'required|integer|min:0',
            'keterangan'   => 'nullable|string',
        ]);

        $barang->update($data);

        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Barang $barang)
    {
        if ($barang->transaksiItems()->exists()) {
            return back()->with('error', 'Barang tidak bisa dihapus karena sudah ada transaksi.');
        }
        $barang->delete();
        return redirect()->route('barang.index')
            ->with('success', 'Barang berhasil dihapus.');
    }

    private function generateKode(): string
    {
        $last = Barang::withTrashed()->max('id') ?? 0;
        return 'BRG-' . str_pad($last + 1, 5, '0', STR_PAD_LEFT);
    }
}
