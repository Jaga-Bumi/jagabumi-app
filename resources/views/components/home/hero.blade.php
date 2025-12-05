<section class="relative min-h-[85vh] flex items-center overflow-hidden bg-gradient-to-br from-background via-primary/5 to-emerald-light/10">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 overflow-hidden">
        <!-- Floating Orbs -->
        <div class="absolute top-20 right-1/4 w-64 h-64 bg-gradient-to-br from-primary/20 to-emerald-light/20 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-20 left-1/4 w-80 h-80 bg-gradient-to-br from-emerald-light/15 to-accent/15 rounded-full blur-3xl" style="animation: float 4s ease-in-out infinite 1.5s;"></div>
        <div class="absolute top-1/2 left-1/3 w-56 h-56 bg-gradient-to-br from-accent/15 to-primary/15 rounded-full blur-3xl" style="animation: float 5s ease-in-out infinite 0.5s;"></div>
        
        <!-- Grid Pattern -->
        <div class="absolute inset-0 bg-hero-pattern opacity-30"></div>
    </div>

    <div class="container mx-auto px-4 py-12 md:py-20 relative z-10">
        <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 items-center max-w-7xl mx-auto">
            <!-- Left Content -->
            <div class="space-y-6 text-center lg:text-left">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass border border-primary/30 shadow-sm animate-fade-in">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-accent opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-accent"></span>
                    </span>
                    <span class="text-xs font-semibold bg-gradient-to-r from-primary to-emerald-light bg-clip-text text-transparent">
                        🌍 Platform Aksi Lingkungan #1 Indonesia
                    </span>
                </div>

                <!-- Main Heading -->
                <div class="space-y-3 animate-fade-up">
                    <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold leading-tight">
                        <span class="text-foreground">Jaga Bumi Bersama,</span>
                        <br />
                        <span class="inline-block bg-gradient-to-r from-primary via-emerald-light to-accent bg-clip-text text-transparent animate-shimmer bg-[length:200%_100%]">
                            Ubah Aksi Jadi Dampak
                        </span>
                    </h1>
                    
                    <p class="text-base md:text-lg text-muted-foreground max-w-xl mx-auto lg:mx-0 leading-relaxed">
                        Bergabunglah dengan <span class="font-semibold text-primary">50,000+ pahlawan lingkungan</span>. 
                        Ikuti quest seru, raih rewards, dan ciptakan perubahan nyata untuk masa depan bumi.
                    </p>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start" style="animation: fade-up 0.8s ease-out 0.4s both;">
                    <a href="{{ route('quests.index') }}" class="group relative overflow-hidden px-6 py-3 bg-gradient-to-r from-primary to-emerald-light rounded-xl font-semibold text-white text-sm shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105">
                        <span class="relative z-10 flex items-center justify-center gap-2">
                            <x-heroicon-o-bolt class="w-4 h-4" />
                            Mulai Quest Sekarang
                            <x-heroicon-o-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                        </span>
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-light to-primary opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                    </a>
                    
                    <a href="{{ route('login') }}" class="group px-6 py-3 glass border border-primary/30 rounded-xl font-semibold text-foreground text-sm hover:border-primary transition-all duration-300 hover:shadow-lg flex items-center justify-center gap-2">
                        <x-heroicon-o-user-plus class="w-4 h-4" />
                        Gabung Gratis
                    </a>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-4 pt-6" style="animation: fade-up 0.8s ease-out 0.6s both;">
                    @php
                        $stats = [
                            ['icon' => '👥', 'value' => '50K+', 'label' => 'Pengguna', 'color' => 'from-primary to-emerald-light'],
                            ['icon' => '✅', 'value' => '1.2K+', 'label' => 'Quest', 'color' => 'from-emerald-light to-accent'],
                            ['icon' => '🏢', 'value' => '500+', 'label' => 'Organisasi', 'color' => 'from-accent to-primary'],
                        ];
                    @endphp
                    @foreach($stats as $stat)
                        <div class="group relative overflow-hidden p-3 rounded-xl glass border border-border hover:border-primary/50 transition-all duration-300">
                            <div class="text-xl mb-1">{{ $stat['icon'] }}</div>
                            <div class="text-xl md:text-2xl font-bold bg-gradient-to-r {{ $stat['color'] }} bg-clip-text text-transparent">
                                {{ $stat['value'] }}
                            </div>
                            <div class="text-xs text-muted-foreground font-medium">
                                {{ $stat['label'] }}
                            </div>
                            <div class="absolute inset-0 bg-gradient-to-r {{ $stat['color'] }} opacity-0 group-hover:opacity-5 transition-opacity duration-300"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Right Visual -->
            <div class="relative lg:block hidden" style="animation: scale-in 0.8s ease-out 0.2s both;">
                <div class="relative max-w-lg mx-auto px-8">
                    <!-- Main Earth Image -->
                    <div class="relative aspect-square">
                        <!-- Glow Effect -->
                        <div class="absolute inset-0 rounded-full bg-gradient-to-br from-primary/30 via-emerald-light/30 to-accent/30 blur-3xl animate-pulse-glow"></div>
                        
                        <!-- Earth Image -->
                        <div class="relative z-0 rounded-2xl overflow-hidden shadow-xl animate-float">
                            <img
                                src="https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=600&h=600&fit=crop"
                                alt="Jaga Bumi - Planet Earth"
                                class="w-full h-full object-cover"
                            />
                            <div class="absolute inset-0 bg-gradient-to-t from-background/40 to-transparent"></div>
                        </div>

                        <!-- Floating Achievement Cards -->
                        <div class="absolute left-4 top-1/4 glass-card p-3 rounded-xl shadow-lg border border-primary/30 animate-float max-w-[140px] z-10">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-accent/20 to-accent/10 flex items-center justify-center text-xl shadow-inner">
                                    🌱
                                </div>
                                <div>
                                    <div class="text-[10px] text-muted-foreground font-medium">Pohon Ditanam</div>
                                    <div class="text-sm font-bold text-foreground">125K+</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute right-4 top-1/3 glass-card p-3 rounded-xl shadow-lg border border-emerald-light/30 max-w-[140px] z-10" style="animation: float 3s ease-in-out infinite 1s;">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary/20 to-primary/10 flex items-center justify-center text-xl shadow-inner">
                                    🏆
                                </div>
                                <div>
                                    <div class="text-[10px] text-muted-foreground font-medium">Quest Aktif</div>
                                    <div class="text-sm font-bold text-foreground">48</div>
                                </div>
                            </div>
                        </div>

                        <div class="absolute bottom-8 left-1/4 glass-card p-3 rounded-xl shadow-lg border border-accent/30 max-w-[150px] z-10" style="animation: float 4s ease-in-out infinite 2s;">
                            <div class="flex items-center gap-2">
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-light/20 to-emerald-light/10 flex items-center justify-center text-xl shadow-inner">
                                    ♻️
                                </div>
                                <div>
                                    <div class="text-[10px] text-muted-foreground font-medium">Plastik Didaur</div>
                                    <div class="text-sm font-bold text-foreground">3.2 Ton</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scroll Indicator -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 animate-bounce hidden md:flex">
            <div class="flex flex-col items-center gap-1 text-muted-foreground">
                <span class="text-[10px] font-medium uppercase tracking-wider">Scroll</span>
                <x-heroicon-o-chevron-down class="w-5 h-5" />
            </div>
        </div>
    </div>
</section>
