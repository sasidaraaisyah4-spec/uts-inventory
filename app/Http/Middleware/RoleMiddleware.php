<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  mixed  ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $userRole = auth()->user()->role;

        if (!in_array($userRole, $roles)) {
            // Berikan respons pengalihan dengan pesan error yang jelas
            return redirect()->route('dashboard')->with(
                'error',
                'Akses Ditolak! Akun dengan role \'' . ucfirst($userRole) . '\' tidak memiliki hak akses untuk halaman/tindakan tersebut.'
            );
        }

        return $next($request);
    }
}
