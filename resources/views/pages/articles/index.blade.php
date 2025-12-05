@extends('layouts.main')

@section('title', 'Artikel - JagaBumi.id')

@section('content')
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
        'date' => '15 November 2024',
    ],
    [
        'id' => 2,
        'title' => 'Dampak Perubahan Iklim terhadap Ekosistem Laut Indonesia',
        'excerpt' => 'Penelitian terbaru menunjukkan perubahan signifikan pada kehidupan laut.',
        'image' => 'https://images.unsplash.com/photo-1559825481-12a05cc00344?w=600&h=400&fit=crop',
        'category' => 'Riset',
        'author' => 'Dr. Maya Putri',
        'likes' => 567,
        'date' => '12 November 2024',
    ],
    [
        'id' => 3,
        'title' => 'Kisah Sukses: Desa Mandiri Energi Terbarukan',
        'excerpt' => 'Bagaimana sebuah desa di Jawa Tengah berhasil mandiri energi dari matahari.',
        'image' => 'https://images.unsplash.com/photo-1509391366360-2e959784a276?w=600&h=400&fit=crop',
        'category' => 'Inspirasi',
        'author' => 'Budi Santoso',
        'likes' => 892,
        'date' => '10 November 2024',
    ],
    [
        'id' => 4,
        'title' => 'Panduan Memulai Composting di Rumah',
        'excerpt' => 'Ubah sampah organik menjadi pupuk berkualitas dengan mudah.',
        'image' => 'https://images.unsplash.com/photo-1530836369250-ef72a3f5cda8?w=600&h=400&fit=crop',
        'category' => 'Tips & Trik',
        'author' => 'Siti Rahayu',
        'likes' => 445,
        'date' => '8 November 2024',
    ],
    [
        'id' => 5,
        'title' => 'Teknologi Green Building untuk Masa Depan',
        'excerpt' => 'Inovasi terbaru dalam konstruksi ramah lingkungan.',
        'image' => 'https://images.unsplash.com/photo-1518005020951-eccb494ad742?w=600&h=400&fit=crop',
        'category' => 'Teknologi',
        'author' => 'Ir. Bambang',
        'likes' => 332,
        'date' => '5 November 2024',
    ],
    [
        'id' => 6,
        'title' => 'Gerakan Zero Waste di Kalangan Milenial',
        'excerpt' => 'Bagaimana generasi muda mengubah pola konsumsi mereka.',
        'image' => 'https://images.unsplash.com/photo-1542838132-92c53300491e?w=600&h=400&fit=crop',
        'category' => 'Lifestyle',
        'author' => 'Dina Kartika',
        'likes' => 678,
        'date' => '3 November 2024',
    ],
];

$categoryColors = [
    'Tips & Trik' => 'bg-accent/20 text-accent-foreground',
    'Riset' => 'bg-primary/20 text-primary',
    'Inspirasi' => 'bg-gold/20 text-forest',
    'Teknologi' => 'bg-emerald-light/20 text-emerald',
    'Lifestyle' => 'bg-destructive/20 text-destructive',
];
@endphp

<!-- Hero -->
<section class="bg-gradient-to-b from-secondary/50 to-background py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10 animate-fade-in">
            <h1 class="text-4xl md:text-5xl font-bold text-foreground mb-4">
                Artikel Lingkungan
            </h1>
            <p class="text-muted-foreground max-w-2xl mx-auto">
                Baca artikel terbaru tentang lingkungan, sustainability, dan aksi nyata untuk bumi
            </p>
        </div>

        <!-- Search -->
        <div class="max-w-2xl mx-auto animate-fade-up">
            <div class="relative">
                <x-heroicon-o-magnifying-glass class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
                <input
                    type="text"
                    placeholder="Cari artikel..."
                    class="w-full pl-12 pr-4 py-4 rounded-xl border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>
        </div>
    </div>
</section>

<!-- Articles Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <!-- Category Filter -->
        <div class="flex gap-2 mb-8 overflow-x-auto pb-2 scrollbar-hide">
            <button class="btn-hero text-sm px-4 py-2 whitespace-nowrap">Semua</button>
            @foreach(array_unique(array_column($articles, 'category')) as $category)
                <button class="btn-glass text-sm px-4 py-2 whitespace-nowrap">{{ $category }}</button>
            @endforeach
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($articles as $i => $article)
                <div class="animate-fade-up" style="animation-delay: {{ $i * 0.05 }}s;">
                    <a href="#">
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
                                <div class="flex items-center justify-between pt-2 border-t border-border">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="text-xs font-medium text-primary">{{ substr($article['author'], 0, 1) }}</span>
                                        </div>
                                        <div class="text-sm">
                                            <div class="font-medium text-foreground">{{ $article['author'] }}</div>
                                            <div class="text-xs text-muted-foreground">{{ $article['date'] }}</div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button class="flex items-center gap-1 text-muted-foreground hover:text-destructive transition-colors">
                                            <x-heroicon-o-heart class="w-4 h-4" />
                                            <span class="text-xs">{{ $article['likes'] }}</span>
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
@endsection
