<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ $title ?? config('app.name', 'JagaBumi') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">
    
    @if(isset($showHeader) && $showHeader)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endif

    <main class="{{ $mainClass ?? 'py-10' }}">
        {{ $slot }}
    </main>

    @if(isset($showFooter) && $showFooter)
        <footer class="mt-8 text-center py-6">
            <p class="text-xs text-slate-500">
                &copy; {{ date('Y') }} {{ config('app.name', 'JagaBumi') }}. Powered by 
                <span class="font-medium">Web3Auth</span> & 
                <span class="font-medium">ZKsync</span>
            </p>
        </footer>
    @endif
    
    @stack('scripts')
</body>
</html>
