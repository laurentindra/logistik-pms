<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kapal extends Model
{
    protected $fillable = ['kode', 'nama', 'tipe', 'aktif', 'keterangan'];

    protected $casts = ['aktif' => 'boolean'];

    public function transaksis(): HasMany
    {
        return $this->hasMany(Transaksi::class, 'kapal_id');
    }

    public function getTipeLabel(): string
    {
        return match ($this->tipe) {
            'kapal'    => 'Kapal',
            'tongkang' => 'Tongkang',
            default    => 'Lainnya',
        };
    }
}
