<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Barang extends Model
{
    /** Kode jenis (barang.jenis CHAR(1)) => label tampilan. */
    public const JENIS = [
        'S' => 'Sepatu',
        'A' => 'Aksesoris',
    ];

    protected $table = 'barang';
    protected $primaryKey = 'idbarang';

    // Tabel barang tidak punya created_at / updated_at.
    public $timestamps = false;

    // Stok TIDAK ada di sini: stok dikelola lewat tabel kartu_stok.
    protected $fillable = ['nama', 'jenis', 'idsatuan', 'status', 'harga'];

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class, 'idsatuan', 'idsatuan');
    }
}
