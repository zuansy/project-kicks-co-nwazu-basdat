<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MasterHubController extends Controller
{
    public function index()
    {
        return view('master.hub', [
            'jumlah' => [
                'barang'   => DB::table('barang')->count(),
                'satuan'   => DB::table('satuan')->count(),
                'vendor'   => DB::table('vendor')->count(),
                'margin'   => DB::table('margin_penjualan')->count(),
                'role'     => DB::table('role')->count(),
                'pengguna' => DB::table('user')->count(),
            ],
            'marginAktif' => DB::table('margin_penjualan')->where('status', 1)->value('persen'),
        ]);
    }
}
