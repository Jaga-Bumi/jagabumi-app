<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-route" content="{{ route('auth.web3') }}">
    <meta name="logout-route" content="{{ route('logout') }}">
    <meta name="web3auth-client-id" content="{{ config('services.web3auth.client_id') }}">
    <meta name="web3auth-network" content="{{ config('services.web3auth.network') }}">

    <title>@yield('title', 'JagaBumi - Gamifying Environmental Action')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/auth.js', 'resources/js/logout.js'])
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @stack('styles')
</head>
<body class="min-h-screen bg-background text-foreground antialiased">
    @include('components.navbar')
    
    <main class="pt-16 lg:pt-20">
        @yield('content')
    </main>
    
    @if(!isset($hideFooter) || !$hideFooter)
        @include('components.footer')
    @endif

    @stack('scripts')
</body>
</html>
