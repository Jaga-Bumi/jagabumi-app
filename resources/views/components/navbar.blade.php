<nav class="fixed top-0 left-0 right-0 z-50 glass border-b border-border/50">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between h-16 lg:h-20">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-emerald-light flex items-center justify-center shadow-lg group-hover:shadow-glow transition-shadow duration-300">
                    <x-heroicon-o-cube class="w-5 h-5 text-primary-foreground" />
                </div>
                <span class="text-xl font-bold text-foreground">
                    Jaga<span class="text-primary">Bumi</span>.id
                </span>
            </a>

            <!-- Desktop Navigation -->
            <div class="hidden lg:flex items-center gap-8">
                @php
                    $navLinks = [
                        ['href' => route('home'), 'label' => 'Beranda'],
                        ['href' => route('quests.index'), 'label' => 'Quest'],
                        ['href' => route('articles.index'), 'label' => 'Artikel'],
                        ['href' => route('leaderboard'), 'label' => 'Leaderboard'],
                    ];
                @endphp

                @foreach($navLinks as $link)
                    <a href="{{ $link['href'] }}" 
                       class="relative text-sm font-medium transition-colors duration-200 hover:text-primary {{ request()->url() == $link['href'] ? 'text-primary' : 'text-muted-foreground' }}">
                        {{ $link['label'] }}
                        @if(request()->url() == $link['href'])
                            <div class="absolute -bottom-1 left-0 right-0 h-0.5 bg-primary rounded-full"></div>
                        @endif
                    </a>
                @endforeach
            </div>

            <!-- Actions -->
            <div class="hidden lg:flex items-center gap-3">
                @auth
                    <a href="{{ route('profile') }}" class="text-sm font-medium text-muted-foreground hover:text-primary transition-colors">
                        Profil
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="btn-glass text-sm">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="btn-hero text-sm">Masuk</a>
                @endauth
            </div>

            <!-- Mobile Menu Button -->
            <button class="lg:hidden p-2" id="mobile-menu-button">
                <x-heroicon-o-bars-3 class="w-6 h-6" id="menu-open-icon" />
                <x-heroicon-o-x-mark class="w-6 h-6 hidden" id="menu-close-icon" />
            </button>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="lg:hidden glass border-t border-border/50 hidden" id="mobile-menu">
        <div class="container mx-auto px-4 py-4 space-y-4">
            @foreach($navLinks as $link)
                <a href="{{ $link['href'] }}" 
                   class="block py-2 text-base font-medium {{ request()->url() == $link['href'] ? 'text-primary' : 'text-muted-foreground' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <div class="flex gap-3 pt-4 border-t border-border">
                @auth
                    <a href="{{ route('profile') }}" class="flex-1">
                        <button class="btn-glass w-full text-sm">Profil</button>
                    </a>
                    <form action="{{ route('logout') }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn-glass w-full text-sm">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full">
                        <button class="btn-hero w-full text-sm">Masuk</button>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>

@push('scripts')
<script>
    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const openIcon = document.getElementById('menu-open-icon');
    const closeIcon = document.getElementById('menu-close-icon');

    menuButton.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
        openIcon.classList.toggle('hidden');
        closeIcon.classList.toggle('hidden');
    });
</script>
@endpush
