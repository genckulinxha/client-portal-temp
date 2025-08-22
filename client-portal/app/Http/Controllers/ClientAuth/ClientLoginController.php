<?php

namespace App\Http\Controllers\ClientAuth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ClientLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('client.auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $client = Client::where('email', $request->email)
            ->where('portal_access', true)
            ->first();

        if (!$client || !Hash::check($request->password, $client->portal_password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect or portal access is disabled.'],
            ]);
        }

        // Update last login time
        $client->update(['last_portal_login' => now()]);

        // Store client in session
        session(['client_id' => $client->id]);

        return redirect()->intended(route('client.dashboard'));
    }

    public function logout(Request $request)
    {
        session()->forget('client_id');
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('client.login');
    }
}