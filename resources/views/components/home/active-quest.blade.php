@php
$quests = [
    [
        'id' => 1,
        'title' => 'Tanam 1000 Pohon di Kalimantan',
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=400&h=250&fit=crop',
        'deadline' => '5 Hari Lagi',
        'participants' => 234,
        'difficulty' => 'Menengah',
        'reward' => 'Sertifikat + Voucher',
    ],
    [
        'id' => 2,
        'title' => 'Bersih Pantai Bali Challenge',
        'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=250&fit=crop',
        'deadline' => '12 Hari Lagi',
        'participants' => 567,
        'difficulty' => 'Mudah',
        'reward' => 'Sertifikat',
    ],
    [
        'id' => 3,
        'title' => 'Urban Farming Jakarta',
        'image' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=250&fit=crop',
        'deadline' => '8 Hari Lagi',
        'participants' => 189,
        'difficulty' => 'Sulit',
        'reward' => 'Sertifikat + Hadiah',
    ],
    [
        'id' => 4,
        'title' => 'Daur Ulang Plastik Nasional',
        'image' => 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=400&h=250&fit=crop',
        'deadline' => '20 Hari Lagi',
        'participants' => 892,
        'difficulty' => 'Mudah',
        'reward' => 'Kupon Belanja',
    ],
    [
        'id' => 5,
        'title' => 'Konservasi Terumbu Karang',
        'image' => 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?w=400&h=250&fit=crop',
        'deadline' => '15 Hari Lagi',
        'participants' => 156,
        'difficulty' => 'Sulit',
        'reward' => 'Sertifikat Pro',
    ],
];

$difficultyColors = [
    'Mudah' => 'bg-accent/20 text-accent-foreground border-accent/30',
    'Menengah' => 'bg-gold/20 text-forest border-gold/30',
    'Sulit' => 'bg-destructive/20 text-destructive border-destructive/30',
];
@endphp

<section class="py-20 bg-background">
    <div class="container mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-sm font-medium text-primary uppercase tracking-wider">
                    Quest Aktif
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-foreground mt-2">
                    Ikuti Quest Seru Hari Ini
                </h2>
            </div>
            <div class="hidden md:flex gap-2">
                <button class="btn-glass w-10 h-10 flex items-center justify-center" onclick="scrollQuests('left')">
                    <x-heroicon-o-chevron-left class="w-5 h-5" />
                </button>
                <button class="btn-glass w-10 h-10 flex items-center justify-center" onclick="scrollQuests('right')">
                    <x-heroicon-o-chevron-right class="w-5 h-5" />
                </button>
            </div>
        </div>

        <div id="quest-scroll" class="flex gap-6 overflow-x-auto pb-4 scrollbar-hide snap-x snap-mandatory">
            @foreach($quests as $i => $quest)
                <div class="snap-start animate-fade-up" style="animation-delay: {{ $i * 0.1 }}s;">
                    <div class="card-quest w-[320px] flex-shrink-0">
                        <div class="relative">
                            <img
                                src="{{ $quest['image'] }}"
                                alt="{{ $quest['title'] }}"
                                class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                            <span class="absolute top-3 right-3 px-3 py-1 rounded-lg text-xs font-medium border {{ $difficultyColors[$quest['difficulty']] }}">
                                {{ $quest['difficulty'] }}
                            </span>
                        </div>
                        <div class="p-5 space-y-4">
                            <h3 class="font-semibold text-lg text-foreground line-clamp-2 group-hover:text-primary transition-colors">
                                {{ $quest['title'] }}
                            </h3>
                            <div class="flex items-center justify-between text-sm text-muted-foreground">
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-o-clock class="w-4 h-4" />
                                    <span>{{ $quest['deadline'] }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <x-heroicon-o-user-group class="w-4 h-4" />
                                    <span>{{ $quest['participants'] }} peserta</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-accent bg-accent/10 px-2 py-1 rounded-lg">
                                    {{ $quest['reward'] }}
                                </span>
                                <a href="{{ route('quests.one', $quest['id']) }}">
                                    <button class="btn-hero text-sm px-4 py-2">
                                        Ikuti
                                    </button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('quests.all') }}">
                <button class="btn-glass px-6 py-3 group inline-flex items-center gap-2">
                    Lihat Semua Quest
                    <x-heroicon-o-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </button>
            </a>
        </div>
    </div>
</section>

@push('scripts')
<script>
    function scrollQuests(direction) {
        const container = document.getElementById('quest-scroll');
        const scrollAmount = 340;
        container.scrollBy({
            left: direction === 'left' ? -scrollAmount : scrollAmount,
            behavior: 'smooth'
        });
    }
</script>
@endpush
