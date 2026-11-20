<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Hanya karyawan (semua role selain Pelanggan) yang boleh masuk panel admin. */
class EnsureStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->session()->get('staff')) {
            return redirect()->route('toko')
                ->with('error', 'Halaman itu khusus karyawan Nwazu Kicks.');
        }

        return $next($request);
    }
}
