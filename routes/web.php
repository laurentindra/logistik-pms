<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\KapalController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

// Guest Auth routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Redirect root
Route::get('/', fn() => redirect()->route('dashboard'));

// Protected routes
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barang
    Route::resource('barang', BarangController::class);

    // Transaksi
    Route::get('/transaksi', [TransaksiController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/masuk/create', [TransaksiController::class, 'createMasuk'])->name('transaksi.create-masuk');
    Route::post('/transaksi/masuk', [TransaksiController::class, 'storeMasuk'])->name('transaksi.store-masuk');
    Route::get('/transaksi/keluar/create', [TransaksiController::class, 'createKeluar'])->name('transaksi.create-keluar');
    Route::post('/transaksi/keluar', [TransaksiController::class, 'storeKeluar'])->name('transaksi.store-keluar');
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::delete('/transaksi/{transaksi}', [TransaksiController::class, 'destroy'])->name('transaksi.destroy');

    // Armada/Kapal
    Route::resource('kapal', KapalController::class);

    // API endpoint untuk data barang
    Route::get('/api/barang/{barang}', function (App\Models\Barang $barang) {
        return response()->json([
            'id'           => $barang->id,
            'nama'         => $barang->nama,
            'satuan'       => $barang->satuan,
            'harga_satuan' => $barang->harga_satuan,
            'stok_sekarang'=> $barang->stok_sekarang,
        ]);
    })->name('api.barang');
});
