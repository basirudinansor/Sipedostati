<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceChangePassword
{
    /**
     * Kalau akun user masih ditandai must_change_password (misal baru dibuat
     * admin, atau baru direset), paksa lewat halaman ganti password dulu
     * sebelum bisa mengakses halaman lain.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->must_change_password) {
            return redirect()->route('password.change.form');
        }

        return $next($request);
    }
}
