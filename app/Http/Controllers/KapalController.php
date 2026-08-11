<?php

namespace App\Http\Controllers;

use App\Models\Kapal;
use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KapalController extends Controller
{
    public function index()
    {
        $kapals = Kapal::withCount('transaksis')->orderBy('kode')->paginate(20);
        return view('kapal.index', compact('kapals'));
    }

    public function show(Kapal $kapal)
    {
        $kapal->load(['transaksis.items.barang.kategori']);

        // Items issued specifically to this ship
        $itemsKeluar = TransaksiItem::whereHas('transaksi', function ($q) use ($kapal) {
            $q->where('kapal_id', $kapal->id)->where('tipe', 'keluar');
        })->with(['barang.kategori', 'transaksi'])->get();

        $totalItemQty = $itemsKeluar->sum('jumlah');
        $totalNilai   = $itemsKeluar->sum('subtotal');

        return view('kapal.show', compact('kapal', 'itemsKeluar', 'totalItemQty', 'totalNilai'));
    }

    public function create()
    {
        return view('kapal.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'kode'       => 'required|string|max:20|unique:kapals,kode',
            'nama'       => 'required|string|max:255',
            'tipe'       => 'required|in:kapal,tongkang,lainnya',
            'aktif'      => 'boolean',
            'keterangan' => 'nullable|string',
        ]);
        $data['aktif'] = $request->boolean('aktif', true);
        Kapal::create($data);
        return redirect()->route('kapal.index')->with('success', 'Armada berhasil ditambahkan.');
    }

    public function edit(Kapal $kapal)
    {
        return view('kapal.edit', compact('kapal'));
    }

    public function update(Request $request, Kapal $kapal)
    {
        $data = $request->validate([
            'kode'       => ['required', 'string', 'max:20', Rule::unique('kapals', 'kode')->ignore($kapal->id)],
            'nama'       => 'required|string|max:255',
            'tipe'       => 'required|in:kapal,tongkang,lainnya',
            'aktif'      => 'boolean',
            'keterangan' => 'nullable|string',
        ]);
        $data['aktif'] = $request->boolean('aktif', true);
        $kapal->update($data);
        return redirect()->route('kapal.index')->with('success', 'Armada berhasil diperbarui.');
    }

    public function destroy(Kapal $kapal)
    {
        if ($kapal->transaksis()->exists()) {
            return back()->with('error', 'Armada tidak bisa dihapus karena memiliki riwayat transaksi.');
        }
        $kapal->delete();
        return redirect()->route('kapal.index')->with('success', 'Armada berhasil dihapus.');
    }
}
