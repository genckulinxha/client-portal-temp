<?php

// Simple script to check if Socialite is properly installed
require_once __DIR__ . '/vendor/autoload.php';

echo "Checking Laravel Socialite installation...\n\n";

// Check if the vendor directory has socialite
$socialitePath = __DIR__ . '/vendor/laravel/socialite';
if (is_dir($socialitePath)) {
    echo "✅ Socialite package directory exists at: $socialitePath\n";
} else {
    echo "❌ Socialite package directory NOT found\n";
}

// Check if the service provider class exists
if (class_exists('Laravel\Socialite\SocialiteServiceProvider')) {
    echo "✅ SocialiteServiceProvider class is available\n";
} else {
    echo "❌ SocialiteServiceProvider class NOT found\n";
}

// Check if composer.json has socialite
$composerJson = json_decode(file_get_contents(__DIR__ . '/composer.json'), true);
if (isset($composerJson['require']['laravel/socialite'])) {
    echo "✅ Socialite is listed in composer.json: " . $composerJson['require']['laravel/socialite'] . "\n";
} else {
    echo "❌ Socialite NOT listed in composer.json\n";
}

// Check if composer.lock has socialite
if (file_exists(__DIR__ . '/composer.lock')) {
    $composerLock = file_get_contents(__DIR__ . '/composer.lock');
    if (strpos($composerLock, '"name": "laravel/socialite"') !== false) {
        echo "✅ Socialite is installed according to composer.lock\n";
    } else {
        echo "❌ Socialite NOT found in composer.lock\n";
    }
} else {
    echo "❌ composer.lock file not found\n";
}

echo "\nDone.\n";