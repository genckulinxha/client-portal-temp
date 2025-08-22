<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientGuest
{
    public function handle(Request $request, Closure $next)
    {
        $clientId = session('client_id');

        if ($clientId) {
            return redirect()->route('client.dashboard');
        }

        return $next($request);
    }
}