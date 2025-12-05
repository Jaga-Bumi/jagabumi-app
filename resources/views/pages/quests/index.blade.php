@extends('layouts.main')

@section('title', 'Jelajahi Quest - JagaBumi.id')

@section('content')
@php
$quests = [
    [
        'id' => 1,
        'title' => 'Tanam 1000 Pohon di Kalimantan',
        'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=400&h=300&fit=crop',
        'deadline' => '5 Hari Lagi',
        'participants' => 234,
        'maxParticipants' => 500,
        'difficulty' => 'Menengah',
        'reward' => 'Sertifikat + Voucher',
        'category' => 'Penanaman',
        'isHot' => true,
    ],
    [
        'id' => 2,
        'title' => 'Bersih Pantai Bali Challenge',
        'image' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&h=300&fit=crop',
        'deadline' => '12 Hari Lagi',
        'participants' => 567,
        'maxParticipants' => 1000,
        'difficulty' => 'Mudah',
        'reward' => 'Sertifikat',
        'category' => 'Kebersihan',
        'isHot' => true,
    ],
    [
        'id' => 3,
        'title' => 'Urban Farming Jakarta',
        'image' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=400&h=300&fit=crop',
        'deadline' => '8 Hari Lagi',
        'participants' => 189,
        'maxParticipants' => 300,
        'difficulty' => 'Sulit',
        'reward' => 'Sertifikat + Hadiah',
        'category' => 'Pertanian',
        'isHot' => false,
    ],
    [
        'id' => 4,
        'title' => 'Daur Ulang Plastik Nasional',
        'image' => 'https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=400&h=300&fit=crop',
        'deadline' => '20 Hari Lagi',
        'participants' => 892,
        'maxParticipants' => 2000,
        'difficulty' => 'Mudah',
        'reward' => 'Kupon Belanja',
        'category' => 'Daur Ulang',
        'isHot' => false,
    ],
    [
        'id' => 5,
        'title' => 'Konservasi Terumbu Karang',
        'image' => 'https://images.unsplash.com/photo-1546026423-cc4642628d2b?w=400&h=300&fit=crop',
        'deadline' => '15 Hari Lagi',
        'participants' => 156,
        'maxParticipants' => 200,
        'difficulty' => 'Sulit',
        'reward' => 'Sertifikat Pro',
        'category' => 'Konservasi',
        'isHot' => false,
    ],
    [
        'id' => 6,
        'title' => 'Kampanye Zero Waste Sekolah',
        'image' => 'https://images.unsplash.com/photo-1569163139599-0f4517e36f51?w=400&h=300&fit=crop',
        'deadline' => '30 Hari Lagi',
        'participants' => 445,
        'maxParticipants' => 1000,
        'difficulty' => 'Menengah',
        'reward' => 'Sertifikat + Merchandise',
        'category' => 'Edukasi',
        'isHot' => true,
    ],
];

$difficultyColors = [
    'Mudah' => 'bg-accent/20 text-accent-foreground border-accent/30',
    'Menengah' => 'bg-gold/20 text-forest border-gold/30',
    'Sulit' => 'bg-destructive/20 text-destructive border-destructive/30',
];
@endphp

<!-- Hero -->
<section class="bg-gradient-to-b from-secondary/50 to-background py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10 animate-fade-in">
            <h1 class="text-4xl md:text-5xl font-bold text-foreground mb-4">
                Jelajahi Quest
            </h1>
            <p class="text-muted-foreground max-w-2xl mx-auto">
                Pilih quest yang sesuai dengan minatmu dan mulai berkontribusi untuk lingkungan
            </p>
        </div>

        <!-- Search & Filter -->
        <div class="flex flex-col md:flex-row gap-4 max-w-3xl mx-auto animate-fade-up">
            <div class="relative flex-1">
                <x-heroicon-o-magnifying-glass class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" />
                <input
                    type="text"
                    placeholder="Cari quest..."
                    class="w-full pl-12 pr-4 py-4 rounded-xl border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-primary"
                />
            </div>
            <select class="px-6 py-4 rounded-xl border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-primary md:w-48">
                <option>Terbaru</option>
                <option>Terpopuler</option>
                <option>Deadline Terdekat</option>
            </select>
        </div>
    </div>
</section>

<!-- Quest Grid -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($quests as $i => $quest)
                <div class="animate-fade-up" style="animation-delay: {{ $i * 0.05 }}s;">
                    <a href="{{ route('quests.show', $quest['id']) }}">
                        <div class="card-quest group h-full">
                            <div class="relative">
                                <img
                                    src="{{ $quest['image'] }}"
                                    alt="{{ $quest['title'] }}"
                                    class="w-full h-52 object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                                <div class="absolute inset-0 bg-gradient-to-t from-forest/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <!-- Badges -->
                                <div class="absolute top-3 left-3 flex gap-2">
                                    @if($quest['isHot'])
                                        <span class="px-3 py-1 rounded-lg text-xs font-medium bg-destructive/90 text-destructive-foreground border-none flex items-center gap-1">
                                            <x-heroicon-s-fire class="w-3 h-3" />
                                            Hot
                                        </span>
                                    @endif
                                    <span class="px-3 py-1 rounded-lg text-xs font-medium border {{ $difficultyColors[$quest['difficulty']] }}">
                                        {{ $quest['difficulty'] }}
                                    </span>
                                </div>
                                
                                <span class="absolute top-3 right-3 px-3 py-1 rounded-lg text-xs font-medium bg-card/90 text-foreground border-none">
                                    {{ $quest['category'] }}
                                </span>
                            </div>
                            
                            <div class="p-5 space-y-4">
                                <h3 class="font-semibold text-lg text-foreground line-clamp-2 group-hover:text-primary transition-colors min-h-[56px]">
                                    {{ $quest['title'] }}
                                </h3>
                                
                                <!-- Progress -->
                                <div class="space-y-2">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-muted-foreground">Kuota</span>
                                        <span class="font-medium">{{ $quest['participants'] }}/{{ $quest['maxParticipants'] }}</span>
                                    </div>
                                    <div class="h-2 bg-secondary rounded-full overflow-hidden">
                                        <div
                                            class="h-full bg-gradient-to-r from-primary to-emerald-light rounded-full transition-all"
                                            style="width: {{ ($quest['participants'] / $quest['maxParticipants']) * 100 }}%"
                                        ></div>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-sm text-muted-foreground">
                                    <div class="flex items-center gap-1.5">
                                        <x-heroicon-o-clock class="w-4 h-4" />
                                        <span>{{ $quest['deadline'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <x-heroicon-o-gift class="w-4 h-4 text-gold" />
                                        <span class="text-xs">{{ $quest['reward'] }}</span>
                                    </div>
                                </div>
                                
                                <button class="btn-hero w-full text-sm">
                                    Ikuti Quest
                                </button>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endsection
