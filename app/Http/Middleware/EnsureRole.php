<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Pemakaian: ->middleware('peran:Administrator,Pemilik') */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! in_array($request->session()->get('nama_role'), $roles, true)) {
            return redirect()->route('dashboard')->with(
                'error',
                'Akses ditolak: halaman ini hanya untuk '.implode(', ', $roles).'.'
            );
        }

        return $next($request);
    }
}
