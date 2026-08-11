<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'kode_barang', 'nama', 'satuan', 'kategori_id',
        'harga_satuan', 'stok_awal', 'stok_sekarang', 'keterangan',
    ];

    protected $casts = [
        'harga_satuan'  => 'decimal:2',
        'stok_awal'     => 'integer',
        'stok_sekarang' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function transaksiItems(): HasMany
    {
        return $this->hasMany(TransaksiItem::class, 'barang_id');
    }

    public function getNilaiStokAttribute(): float
    {
        return $this->stok_sekarang * $this->harga_satuan;
    }

    public function getStatusStokAttribute(): string
    {
        if ($this->stok_sekarang <= 0) return 'habis';
        if ($this->stok_sekarang <= 2) return 'rendah';
        return 'aman';
    }
}
