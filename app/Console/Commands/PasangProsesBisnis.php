<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class PasangProsesBisnis extends Command
{
    protected $signature = 'db:pasang-proses-bisnis';

    protected $description = 'Memasang function, stored procedure, dan trigger untuk proses pengadaan - penerimaan - retur - penjualan';

    public function handle(): int
    {
        $path = database_path('perubahan-proses-bisnis.sql');

        if (! is_file($path)) {
            $this->error('File tidak ditemukan: '.$path);

            return self::FAILURE;
        }

        // Tiap blok diawali baris "-- @@ judul"; hasil split: [header, judul1, sql1, judul2, sql2, ...]
        $bagian = preg_split('/^-- @@\s*(.*)$/m', (string) file_get_contents($path), -1, PREG_SPLIT_DELIM_CAPTURE);

        $berhasil = 0;

        for ($i = 1; $i < count($bagian); $i += 2) {
            $judul = trim($bagian[$i]);
            $sql = trim($bagian[$i + 1] ?? '');

            if (trim(preg_replace('/^\s*--.*$/m', '', $sql)) === '') {
                continue;
            }

            try {
                DB::unprepared($sql);
                $this->line('  OK     '.$judul);
                $berhasil++;
            } catch (QueryException $e) {
                $this->error('  GAGAL  '.$judul);
                $this->line('         '.($e->getPrevious()?->getMessage() ?? $e->getMessage()));

                return self::FAILURE;
            }
        }

        $this->info("Selesai: {$berhasil} blok berhasil dipasang.");

        return self::SUCCESS;
    }
}
