<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="auth-route" content="{{ route('auth.web3') }}">
    <meta name="web3auth-client-id" content="{{ config('services.web3auth.client_id') }}">
    <meta name="web3auth-network" content="{{ config('services.web3auth.network') }}">
    <title>Login - JagaBumi.id</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen flex">
        <!-- Left Side - Illustration -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-forest">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/80 to-forest/90 z-10"></div>
            <img
                src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=800&h=800&fit=crop"
                alt="Earth Illustration"
                class="absolute inset-0 w-full h-full object-cover"
            />
            <div class="relative z-20 flex flex-col justify-center items-center p-12 text-center">
                <div class="mb-8 animate-scale-in">
                    <div class="w-20 h-20 rounded-2xl bg-primary-foreground/20 backdrop-blur-lg flex items-center justify-center mb-6 mx-auto">
                        <x-heroicon-o-cube class="w-10 h-10 text-primary-foreground" />
                    </div>
                    <h1 class="text-4xl font-bold text-primary-foreground mb-4">
                        Selamat Datang di JagaBumi.id
                    </h1>
                    <p class="text-primary-foreground/80 text-lg max-w-md">
                        Bergabunglah dengan komunitas pahlawan lingkungan dan mulai perjalananmu 
                        untuk menjaga bumi bersama.
                    </p>
                </div>
                
                <div class="grid grid-cols-3 gap-6 mt-8">
                    @php
                        $stats = [
                            ['value' => '50K+', 'label' => 'Pengguna'],
                            ['value' => '1.2K+', 'label' => 'Quest'],
                            ['value' => '500+', 'label' => 'Organisasi'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                        <div class="text-center animate-count-up">
                            <div class="text-2xl font-bold text-primary-foreground">{{ $stat['value'] }}</div>
                            <div class="text-sm text-primary-foreground/70">{{ $stat['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="flex-1 flex items-center justify-center p-6 lg:p-12 bg-background">
            <div class="w-full max-w-md animate-fade-in">
                <!-- Logo Mobile -->
                <a href="{{ route('home') }}" class="flex items-center gap-2 mb-8 lg:hidden">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-emerald-light flex items-center justify-center">
                        <x-heroicon-o-cube class="w-5 h-5 text-primary-foreground" />
                    </div>
                    <span class="text-xl font-bold">
                        Jaga<span class="text-primary">Bumi</span>.id
                    </span>
                </a>

                <!-- Auth Card -->
                <div class="glass-card p-6">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-foreground mb-2">Masuk ke JagaBumi.id</h2>
                        <p class="text-muted-foreground text-sm">
                            Login mudah dengan akun sosial media Anda
                        </p>
                    </div>

                    <!-- Info Box -->
                    <div class="bg-gradient-to-r from-primary/10 to-emerald-light/10 border border-primary/30 rounded-xl p-4 mb-6">
                        <div class="flex gap-3">
                            <x-heroicon-o-information-circle class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" />
                            <div class="text-sm text-foreground">
                                <p class="font-medium mb-1">🎁 Login & Dapatkan Dompet Digital Gratis!</p>
                                <p class="text-muted-foreground">Gunakan akun Google, Facebook, atau email Anda. Kami akan otomatis membuatkan dompet Web3 untuk menyimpan hadiah dari quest yang Anda selesaikan.</p>
                            </div>
                        </div>
                    </div>

                    <div id="loading" class="hidden mb-6">
                        <div class="flex flex-col items-center justify-center py-8">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mb-4"></div>
                            <p class="text-muted-foreground">Menghubungkan...</p>
                        </div>
                    </div>

                    <div id="error-box" class="hidden mb-6">
                        <div class="bg-destructive/10 border border-destructive/30 rounded-xl p-4">
                            <div class="flex gap-3">
                                <x-heroicon-o-exclamation-circle class="w-5 h-5 text-destructive flex-shrink-0" />
                                <p id="error-message" class="text-sm text-destructive"></p>
                            </div>
                        </div>
                    </div>

                    <div id="auth-buttons">
                        <button 
                            id="auth-btn" 
                            type="button"
                            class="w-full btn-hero justify-center group disabled:opacity-50 disabled:cursor-not-allowed"
                            aria-label="Login"
                        >
                            <span class="relative z-10 flex items-center justify-center gap-3">
                                <x-heroicon-o-arrow-right-on-rectangle class="w-5 h-5" />
                                <span>Masuk / Daftar</span>
                            </span>
                        </button>
                        
                        <!-- Additional Info -->
                        <div class="mt-6 space-y-3">
                            <div class="flex items-start gap-2 text-sm text-muted-foreground">
                                <x-heroicon-o-check class="w-4 h-4 text-emerald-light flex-shrink-0 mt-0.5" />
                                <span>Daftar otomatis saat login pertama kali</span>
                            </div>
                            <div class="flex items-start gap-2 text-sm text-muted-foreground">
                                <x-heroicon-o-check class="w-4 h-4 text-emerald-light flex-shrink-0 mt-0.5" />
                                <span>Dompet digital otomatis untuk simpan rewards</span>
                            </div>
                            <div class="flex items-start gap-2 text-sm text-muted-foreground">
                                <x-heroicon-o-check class="w-4 h-4 text-emerald-light flex-shrink-0 mt-0.5" />
                                <span>Aman dan terenkripsi</span>
                            </div>
                        </div>

                        <!-- Privacy Note -->
                        <p class="text-xs text-muted-foreground text-center mt-6">
                            Dengan masuk, Anda menyetujui 
                            <a href="#" class="text-primary hover:underline">Syarat & Ketentuan</a> dan 
                            <a href="#" class="text-primary hover:underline">Kebijakan Privasi</a> kami.
                        </p>
                    </div>

                    <div class="mt-6 text-center">
                        <a href="{{ route('home') }}" class="text-sm text-primary hover:underline">
                            ← Kembali ke Beranda
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/auth.js'])
</body>
</html>
