<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Law Firm Portal') }} - Client Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <!-- Logo -->
            <div class="flex justify-center mb-6">
                <h1 class="text-2xl font-bold text-blue-800">Law Firm Client Portal</h1>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('client.login') }}">
                @csrf

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                    <input id="email" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" />
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <label for="password" class="block font-medium text-sm text-gray-700">Password</label>
                    <input id="password" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500" type="password" name="password" required autocomplete="current-password" />
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">Remember me</span>
                    </label>
                </div>

                <div class="mt-4 space-y-3">
                    <a href="{{ route('client.auth.google') }}"
                       class="w-full inline-flex items-center justify-center gap-2 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium py-2 px-4 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="h-5 w-5">
                            <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.602 32.89 29.223 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C33.896 6.053 29.179 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                            <path fill="#FF3D00" d="M6.306 14.691l6.571 4.816C14.46 16.289 18.85 14 24 14c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C33.896 6.053 29.179 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                            <path fill="#4CAF50" d="M24 44c5.166 0 9.851-1.977 13.396-5.196l-6.179-5.229C29.13 35.786 26.715 36.8 24 36.8 18.805 36.8 14.442 33.716 12.71 29.386l-6.571 5.066C9.508 40.469 16.227 44 24 44z"/>
                            <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-1.316 3.807-5.217 6.8-11.303 6.8-5.195 0-9.558-3.084-11.29-7.414l-6.571 5.066C9.508 40.469 16.227 44 24 44c11.045 0 20-8.955 20-20 0-1.341-.138-2.65-.389-3.917z"/>
                        </svg>
                        Continue with Google
                    </a>

                    <button type="submit" class="w-full bg-blue-800 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Log in
                    </button>
                </div>
            </form>

            <!-- Help Text -->
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Need help accessing your portal? Contact your attorney.
                </p>
            </div>
        </div>

        <!-- Demo Credentials -->
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-blue-50 shadow-md overflow-hidden sm:rounded-lg">
            <h3 class="text-lg font-medium text-blue-800 mb-3">Demo Credentials</h3>
            <div class="space-y-2 text-sm">
                <div>
                    <strong>Jennifer Thompson:</strong><br>
                    Email: jennifer.thompson@email.com<br>
                    Password: client123
                </div>
                <div>
                    <strong>David Martinez:</strong><br>
                    Email: david.martinez@email.com<br>
                    Password: client123
                </div>
                <div>
                    <strong>James Anderson:</strong><br>
                    Email: james.anderson@email.com<br>
                    Password: client123
                </div>
            </div>
        </div>
    </div>
</body>
</html>