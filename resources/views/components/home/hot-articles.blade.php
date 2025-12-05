@php
$articles = [
    [
        'id' => 1,
        'title' => '10 Cara Mudah Mengurangi Jejak Karbon di Rumah',
        'excerpt' => 'Mulai dari hal kecil, kita bisa membuat perubahan besar untuk lingkungan.',
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=600&h=400&fit=crop',
        'category' => 'Tips & Trik',
        'author' => 'Ahmad Rizki',
        'likes' => 234,
    ],
    [
        'id' => 2,
        'title' => 'Dampak Perubahan Iklim terhadap Ekosistem Laut Indonesia',
        'excerpt' => 'Penelitian terbaru menunjukkan perubahan signifikan pada kehidupan laut.',
        'image' => 'https://images.unsplash.com/photo-1559825481-12a05cc00344?w=600&h=400&fit=crop',
        'category' => 'Riset',
        'author' => 'Dr. Maya Putri',
        'likes' => 567,
    ],
    [
        'id' => 3,
        'title' => 'Kisah Sukses: Desa Mandiri Energi Terbarukan',
        'excerpt' => 'Bagaimana sebuah desa di Jawa Tengah berhasil mandiri energi dari matahari.',
        'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=600&h=400&fit=crop',
        'category' => 'Inspirasi',
        'author' => 'Budi Santoso',
        'likes' => 892,
    ],
];

$categoryColors = [
    'Tips & Trik' => 'bg-accent/20 text-accent-foreground',
    'Riset' => 'bg-primary/20 text-primary',
    'Inspirasi' => 'bg-gold/20 text-forest',
];
@endphp

<section class="py-20 bg-secondary/30">
    <div class="container mx-auto px-4">
        <div class="flex items-end justify-between mb-10">
            <div>
                <span class="text-sm font-medium text-primary uppercase tracking-wider">
                    Artikel Populer
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-foreground mt-2">
                    Baca & Pelajari
                </h2>
            </div>
            <a href="{{ route('articles.all') }}">
                <button class="btn-glass px-4 py-2 group inline-flex items-center gap-2 text-sm">
                    Semua Artikel
                    <x-heroicon-o-arrow-right class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
                </button>
            </a>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $i => $article)
                <div class="animate-fade-up" style="animation-delay: {{ $i * 0.1 }}s;">
                    <a href="{{ route('articles.one', $article['id']) }}">
                        <div class="glass-card overflow-hidden group h-full">
                            <div class="relative overflow-hidden">
                                <img
                                    src="{{ $article['image'] }}"
                                    alt="{{ $article['title'] }}"
                                    class="w-full h-52 object-cover group-hover:scale-110 transition-transform duration-500"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-forest/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                <span class="absolute top-3 left-3 px-3 py-1 rounded-lg text-xs font-medium {{ $categoryColors[$article['category']] }}">
                                    {{ $article['category'] }}
                                </span>
                            </div>
                            <div class="p-5 space-y-3">
                                <h3 class="font-semibold text-lg text-foreground line-clamp-2 group-hover:text-primary transition-colors">
                                    {{ $article['title'] }}
                                </h3>
                                <p class="text-sm text-muted-foreground line-clamp-2">
                                    {{ $article['excerpt'] }}
                                </p>
                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-sm text-muted-foreground">
                                        {{ $article['author'] }}
                                    </span>
                                    <div class="flex items-center gap-3">
                                        <button class="flex items-center gap-1 text-muted-foreground hover:text-destructive transition-colors">
                                            <x-heroicon-o-heart class="w-4 h-4" />
                                            <span class="text-xs">{{ $article['likes'] }}</span>
                                        </button>
                                        <button class="text-muted-foreground hover:text-primary transition-colors">
                                            <x-heroicon-o-bookmark class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
