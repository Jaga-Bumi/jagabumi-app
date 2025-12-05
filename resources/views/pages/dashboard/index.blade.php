@extends('layouts.main')

@section('title', 'Dashboard - JagaBumi.id')

@section('content')
<section class="py-12 bg-background min-h-screen">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-foreground">Dashboard</h1>
                @auth
                    <button 
                        id="logout-btn" 
                        class="btn-glass px-4 py-2 text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        Logout
                    </button>
                @endauth
            </div>

            <!-- Main Card -->
            <div class="glass-card p-8">
                <h2 class="text-2xl font-bold text-foreground mb-6">Selamat Datang, {{ $user->name }}! 👋</h2>
                
                @auth
                    <div class="bg-gradient-to-r from-primary/10 to-emerald-light/10 border border-primary/30 rounded-xl p-4 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/20 flex items-center justify-center">
                                <x-heroicon-o-check-circle class="w-6 h-6 text-primary" />
                            </div>
                            <div>
                                <p class="font-bold text-primary">Status: Login Berhasil</p>
                                <p class="text-sm text-muted-foreground">Akun Anda telah terverifikasi</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 bg-secondary/30 rounded-xl">
                            <div class="text-sm text-muted-foreground mb-1">Email</div>
                            <div class="font-medium text-foreground">{{ $user->email }}</div>
                        </div>
                        
                        <div class="p-4 bg-secondary/30 rounded-xl">
                            <div class="text-sm text-muted-foreground mb-1">Verifier ID (Google)</div>
                            <div class="font-medium text-foreground">{{ $user->verifier_id }}</div>
                        </div>
                        
                        <div class="p-4 bg-secondary/30 rounded-xl">
                            <div class="text-sm text-muted-foreground mb-1">Wallet Address (Auto-Generated)</div>
                            <code class="font-mono text-sm bg-card px-2 py-1 rounded text-primary break-all">{{ $user->wallet_address }}</code>
                        </div>
                    </div>
                @endauth

                @guest
                    <div class="bg-gradient-to-r from-gold/10 to-accent/10 border border-gold/30 rounded-xl p-6 mb-6">
                        <div class="flex items-start gap-3">
                            <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-gold flex-shrink-0 mt-0.5" />
                            <div>
                                <p class="font-medium text-foreground mb-2">Perhatian</p>
                                <p class="text-sm text-muted-foreground">
                                    Anda dapat melihat halaman ini, namun untuk melaksanakan Quest, silakan login terlebih dahulu.
                                </p>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('login') }}" class="w-full btn-hero justify-center group">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                            <span>Login dengan Web3Auth</span>
                        </span>
                    </a>
                @endguest
            </div>
        </div>
    </div>
</section>

@auth
    <meta name="logout-route" content="{{ route('logout') }}">
    @vite(['resources/js/logout.js'])
@endauth
@endsection
