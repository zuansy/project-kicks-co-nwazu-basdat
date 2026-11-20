<?php

namespace App\Support;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Stok
{
    /**
     * Saldo stok terakhir tiap barang (kolom: idbarang, stock) dari kartu_stok.
     * Barang yang belum punya riwayat tidak muncul -> pakai LEFT JOIN + COALESCE.
     */
    public static function saldoTerakhir(): Builder
    {
        return DB::table('kartu_stok as k')
            ->joinSub(
                DB::table('kartu_stok')
                    ->select('idbarang', DB::raw('MAX(idkartu_stok) as last_id'))
                    ->groupBy('idbarang'),
                'm',
                'm.last_id',
                '=',
                'k.idkartu_stok'
            )
            ->select('k.idbarang', 'k.stock');
    }
}
