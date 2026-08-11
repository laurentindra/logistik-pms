<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $fillable = [
        'no_transaksi', 'tanggal', 'tipe', 'kapal_id', 'keterangan', 'dibuat_oleh',
    ];

    protected $casts = ['tanggal' => 'date'];

    public function kapal(): BelongsTo
    {
        return $this->belongsTo(Kapal::class, 'kapal_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransaksiItem::class, 'transaksi_id');
    }

    public function getTotalNilaiAttribute(): float
    {
        return $this->items->sum('subtotal');
    }

    public function getTotalItemAttribute(): int
    {
        return $this->items->sum('jumlah');
    }

    public static function generateNoTransaksi(string $tipe): string
    {
        $prefix = $tipe === 'masuk' ? 'MSK' : 'KLR';
        $date   = now()->format('Ymd');
        $last   = static::where('no_transaksi', 'like', "{$prefix}-{$date}-%")->count();
        $seq    = str_pad($last + 1, 4, '0', STR_PAD_LEFT);
        return "{$prefix}-{$date}-{$seq}";
    }
}
