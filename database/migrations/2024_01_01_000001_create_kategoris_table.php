<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->timestamps();
        });

        Schema::create('kapals', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();
            $table->string('nama');
            $table->enum('tipe', ['kapal', 'tongkang', 'lainnya'])->default('kapal');
            $table->boolean('aktif')->default(true);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang')->unique();
            $table->string('nama');
            $table->string('satuan');
            $table->foreignId('kategori_id')->constrained('kategoris')->restrictOnDelete();
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_sekarang')->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->string('no_transaksi')->unique();
            $table->date('tanggal');
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->foreignId('kapal_id')->nullable()->constrained('kapals')->nullOnDelete();
            $table->text('keterangan')->nullable();
            $table->string('dibuat_oleh');
            $table->timestamps();
        });

        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->cascadeOnDelete();
            $table->foreignId('barang_id')->constrained('barangs')->restrictOnDelete();
            $table->integer('jumlah');
            $table->decimal('harga_satuan', 15, 2)->default(0);
            $table->decimal('subtotal', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_items');
        Schema::dropIfExists('transaksis');
        Schema::dropIfExists('barangs');
        Schema::dropIfExists('kapals');
        Schema::dropIfExists('kategoris');
    }
};
