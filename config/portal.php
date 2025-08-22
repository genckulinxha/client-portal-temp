<?php

return [
    // Allow clients to create a portal account automatically on first Google login
    'client_self_signup' => env('PORTAL_CLIENT_SELF_SIGNUP', false),

    // If a client exists but portal_access is disabled, turning this on will enable access on first Google login
    'client_self_signup_enable_access' => env('PORTAL_CLIENT_SELF_SIGNUP_ENABLE_ACCESS', true),
]; 