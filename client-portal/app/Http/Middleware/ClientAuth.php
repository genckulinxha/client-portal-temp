<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;

class ClientAuth
{
    public function handle(Request $request, Closure $next)
    {
        $clientId = session('client_id');

        if (!$clientId) {
            return redirect()->route('client.login');
        }

        $client = Client::where('id', $clientId)
            ->where('portal_access', true)
            ->select(['id', 'first_name', 'last_name', 'email', 'portal_access'])
            ->first();

        if (!$client) {
            session()->forget('client_id');
            return redirect()->route('client.login');
        }

        // Share client data with all views
        view()->share('currentClient', $client);
        $request->merge(['client' => $client]);

        return $next($request);
    }
}