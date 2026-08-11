<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Transaksi;
use App\Models\Kategori;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBarang    = Barang::count();
        $barangAdaStok  = Barang::where('stok_sekarang', '>', 0)->count();
        $totalNilai     = Barang::sum(\DB::raw('stok_sekarang * harga_satuan'));

        $transaksiMasuk  = Transaksi::where('tipe', 'masuk')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        $transaksiKeluar = Transaksi::where('tipe', 'keluar')
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->count();

        // Nilai stok per kategori
        $nilaiPerKategori = Kategori::with('barangs')->get()->map(fn($k) => [
            'nama'  => $k->nama,
            'nilai' => $k->barangs->sum(fn($b) => $b->stok_sekarang * $b->harga_satuan),
            'count' => $k->barangs->count(),
        ]);

        // Transaksi terakhir
        $transaksiTerbaru = Transaksi::with(['kapal', 'items.barang'])
            ->latest()
            ->limit(10)
            ->get();

        // Top 10 barang keluar bulan ini
        $topKeluar = \App\Models\TransaksiItem::with('barang')
            ->whereHas('transaksi', fn($q) => $q->where('tipe', 'keluar')
                ->whereMonth('tanggal', now()->month)
                ->whereYear('tanggal', now()->year))
            ->selectRaw('barang_id, SUM(jumlah) as total_keluar, SUM(subtotal) as total_nilai')
            ->groupBy('barang_id')
            ->orderByDesc('total_nilai')
            ->limit(10)
            ->get();

        // Stok rendah / habis
        $stokRendah = Barang::where('stok_sekarang', '<=', 3)
            ->where('stok_sekarang', '>=', 0)
            ->with('kategori')
            ->orderBy('stok_sekarang')
            ->limit(10)
            ->get();

        return view('dashboard', compact(
            'totalBarang', 'barangAdaStok', 'totalNilai',
            'transaksiMasuk', 'transaksiKeluar',
            'nilaiPerKategori', 'transaksiTerbaru',
            'topKeluar', 'stokRendah'
        ));
    }
}
