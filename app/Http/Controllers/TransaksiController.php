<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Kapal;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaksi::with(['kapal', 'items']);

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }
        if ($request->filled('kapal_id')) {
            $query->where('kapal_id', $request->kapal_id);
        }
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(fn($qb) => $qb->where('no_transaksi', 'like', "%{$q}%")
                ->orWhere('dibuat_oleh', 'like', "%{$q}%")
                ->orWhere('keterangan', 'like', "%{$q}%"));
        }

        $transaksis = $query->latest()->paginate(20)->withQueryString();
        $kapals     = Kapal::where('aktif', true)->orderBy('kode')->get();

        return view('transaksi.index', compact('transaksis', 'kapals'));
    }

    public function createMasuk()
    {
        $barangs = Barang::with('kategori')->orderBy('nama')->get();
        $no      = Transaksi::generateNoTransaksi('masuk');
        return view('transaksi.create-masuk', compact('barangs', 'no'));
    }

    public function createKeluar()
    {
        $barangs = Barang::with('kategori')->where('stok_sekarang', '>', 0)->orderBy('nama')->get();
        $kapals  = Kapal::where('aktif', true)->orderBy('kode')->get();
        $no      = Transaksi::generateNoTransaksi('keluar');
        return view('transaksi.create-keluar', compact('barangs', 'kapals', 'no'));
    }

    public function storeMasuk(Request $request)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'keterangan'     => 'nullable|string',
            'dibuat_oleh'    => 'required|string|max:100',
            'items'          => 'required|array|min:1',
            'items.*.barang_id'   => 'required|exists:barangs,id',
            'items.*.jumlah'      => 'required|integer|min:1',
            'items.*.harga_satuan'=> 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request) {
            $trx = Transaksi::create([
                'no_transaksi' => Transaksi::generateNoTransaksi('masuk'),
                'tanggal'      => $request->tanggal,
                'tipe'         => 'masuk',
                'keterangan'   => $request->keterangan,
                'dibuat_oleh'  => $request->dibuat_oleh,
            ]);

            foreach ($request->items as $item) {
                $harga    = (float) $item['harga_satuan'];
                $jumlah   = (int)   $item['jumlah'];
                $subtotal = $harga * $jumlah;

                TransaksiItem::create([
                    'transaksi_id' => $trx->id,
                    'barang_id'    => $item['barang_id'],
                    'jumlah'       => $jumlah,
                    'harga_satuan' => $harga,
                    'subtotal'     => $subtotal,
                ]);

                Barang::where('id', $item['barang_id'])
                    ->increment('stok_sekarang', $jumlah);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi masuk berhasil disimpan.');
    }

    public function storeKeluar(Request $request)
    {
        $request->validate([
            'tanggal'        => 'required|date',
            'kapal_id'       => 'nullable|exists:kapals,id',
            'keterangan'     => 'nullable|string',
            'dibuat_oleh'    => 'required|string|max:100',
            'items'          => 'required|array|min:1',
            'items.*.barang_id'   => 'required|exists:barangs,id',
            'items.*.jumlah'      => 'required|integer|min:1',
            'items.*.harga_satuan'=> 'required|numeric|min:0',
        ]);

        // Validate sufficient stock for all items
        $errors = [];
        foreach ($request->items as $idx => $item) {
            $barang = Barang::find($item['barang_id']);
            if ($barang && $barang->stok_sekarang < $item['jumlah']) {
                $errors["items.{$idx}.jumlah"] = "Stok {$barang->nama} tidak mencukupi. Stok saat ini: {$barang->stok_sekarang}";
            }
        }
        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        DB::transaction(function () use ($request) {
            $trx = Transaksi::create([
                'no_transaksi' => Transaksi::generateNoTransaksi('keluar'),
                'tanggal'      => $request->tanggal,
                'tipe'         => 'keluar',
                'kapal_id'     => $request->kapal_id ?: null,
                'keterangan'   => $request->keterangan,
                'dibuat_oleh'  => $request->dibuat_oleh,
            ]);

            foreach ($request->items as $item) {
                $harga    = (float) $item['harga_satuan'];
                $jumlah   = (int)   $item['jumlah'];
                $subtotal = $harga * $jumlah;

                TransaksiItem::create([
                    'transaksi_id' => $trx->id,
                    'barang_id'    => $item['barang_id'],
                    'jumlah'       => $jumlah,
                    'harga_satuan' => $harga,
                    'subtotal'     => $subtotal,
                ]);

                Barang::where('id', $item['barang_id'])
                    ->decrement('stok_sekarang', $jumlah);
            }
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi keluar berhasil disimpan.');
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load(['kapal', 'items.barang.kategori']);
        return view('transaksi.show', compact('transaksi'));
    }

    public function destroy(Transaksi $transaksi)
    {
        DB::transaction(function () use ($transaksi) {
            $transaksi->load('items');
            foreach ($transaksi->items as $item) {
                if ($transaksi->tipe === 'masuk') {
                    Barang::where('id', $item->barang_id)
                        ->decrement('stok_sekarang', $item->jumlah);
                } else {
                    Barang::where('id', $item->barang_id)
                        ->increment('stok_sekarang', $item->jumlah);
                }
            }
            $transaksi->items()->delete();
            $transaksi->delete();
        });

        return redirect()->route('transaksi.index')
            ->with('success', 'Transaksi berhasil dibatalkan dan stok dikembalikan.');
    }
}
