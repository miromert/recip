<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ isset($title) ? $title . ' — ' : '' }}{{ config('app.name', 'Recip') }}</title>
        <meta name="description" content="{{ $metaDescription ?? 'Less reading, more cooking. Recipes with easy metric/imperial conversion.' }}">

        {{-- Open Graph --}}
        <meta property="og:title" content="{{ $title ?? config('app.name') }}">
        <meta property="og:description" content="{{ $metaDescription ?? 'Less reading, more cooking.' }}">
        <meta property="og:type" content="{{ $ogType ?? 'website' }}">
        @isset($ogImage)
            <meta property="og:image" content="{{ $ogImage }}">
        @endisset

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- JSON-LD structured data --}}
        @isset($jsonLd)
            <script type="application/ld+json">{!! $jsonLd !!}</script>
        @endisset
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-50">
            @include('layouts.navigation')

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-green-50 border border-green-200 text-green-800 rounded-lg p-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if (session('info'))
                <div class="max-w-7xl mx-auto mt-4 px-4 sm:px-6 lg:px-8">
                    <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-lg p-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)">
                        {{ session('info') }}
                    </div>
                </div>
            @endif

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow-sm">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-white border-t border-gray-200 mt-12">
                <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                        <div class="text-sm text-gray-500">
                            &copy; {{ date('Y') }} Recip — Less reading, more cooking.
                        </div>
                        <div class="flex gap-6 text-sm text-gray-500">
                            <a href="{{ route('about') }}" class="hover:text-gray-700">About</a>
                            <a href="{{ route('privacy') }}" class="hover:text-gray-700">Privacy</a>
                            <a href="https://github.com/miromert/recip" target="_blank" rel="noopener" class="hover:text-gray-700">GitHub</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
