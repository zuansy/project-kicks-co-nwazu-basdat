<?php

namespace App\Support;

use Illuminate\Database\QueryException;

class Sp
{
    /**
     * Ambil pesan yang dikirim SP/trigger lewat SIGNAL SQLSTATE '45000'.
     * Mengembalikan null jika errornya bukan dari SIGNAL (biarkan dilempar ulang).
     */
    public static function pesan(QueryException $e): ?string
    {
        if ((string) $e->getCode() !== '45000') {
            return null;
        }

        if (preg_match('/: 1644 (.*?) \(Connection:/s', $e->getMessage(), $m)) {
            return trim($m[1]);
        }

        return 'Proses ditolak oleh database.';
    }
}
