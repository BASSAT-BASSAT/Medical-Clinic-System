<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Medical Clinic') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-blue-500 via-blue-600 to-purple-700">
            <!-- Logo/Branding -->
            <div class="mb-6 text-center">
                <div class="bg-white/20 backdrop-blur-sm p-4 rounded-2xl inline-block mb-4">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-white">Medical Clinic</h1>
                <p class="text-blue-100 text-sm">Appointment Management System</p>
            </div>

            <div class="w-full sm:max-w-md px-8 py-8 bg-white shadow-2xl overflow-hidden rounded-2xl">
                {{ $slot }}
            </div>

            <p class="mt-6 text-blue-100 text-sm">
                &copy; {{ date('Y') }} Medical Clinic. All rights reserved.
            </p>
        </div>
    </body>
</html>
