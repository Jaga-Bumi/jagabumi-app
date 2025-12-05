<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'JagaBumi.id - Platform Aksi Lingkungan')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700,800,900" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">
    @include('components.navbar')
    
    <main class="flex-1 pt-16 lg:pt-20">
        @yield('content')
    </main>
    
    @if(!isset($hideFooter) || !$hideFooter)
        @include('components.footer')
    @endif

    @stack('scripts')
</body>
</html>
