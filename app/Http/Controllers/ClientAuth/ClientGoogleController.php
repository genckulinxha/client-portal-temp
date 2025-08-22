<?php

namespace App\Http\Controllers\ClientAuth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class ClientGoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        // Retrieve the Google user; if state fails locally, retry stateless for dev convenience
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (InvalidStateException $e) {
            if (app()->environment('local')) {
                $googleUser = Socialite::driver('google')->stateless()->user();
            } else {
                throw $e;
            }
        }

        $email = $googleUser->getEmail();
        $client = Client::where('email', $email)->first();

        // If not found, optionally create on first login
        if (!$client && Config::get('portal.client_self_signup')) {
            $client = new Client();
            $client->first_name = $googleUser->user['given_name'] ?? '';
            $client->last_name = $googleUser->user['family_name'] ?? '';
            $client->email = $email;
            $client->status = 'prospect';
            $client->portal_access = true;
            $client->save();
        }

        // If found but portal_access is disabled, optionally enable
        if ($client && !$client->portal_access && Config::get('portal.client_self_signup_enable_access')) {
            $client->portal_access = true;
            $client->save();
        }

        if (!$client || !$client->portal_access) {
            return redirect()
                ->route('client.login')
                ->withErrors(['email' => 'No portal account found for this Google account or portal access is disabled.']);
        }

        $client->last_portal_login = now();
        $client->save();

        session(['client_id' => $client->id]);

        return redirect()->intended(route('client.dashboard'));
    }
}