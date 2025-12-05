@extends('layouts.main')

@section('title', 'Tanam 1000 Pohon di Kalimantan - JagaBumi.id')

@section('content')
@php
$quest = [
    'id' => $id ?? 1,
    'title' => 'Tanam 1000 Pohon di Kalimantan',
    'description' => 'Bergabunglah dalam misi besar untuk menanam 1000 pohon di area terdampak deforestasi Kalimantan.',
    'longDescription' => 'Program penanaman pohon ini merupakan kolaborasi antara JagaBumi.id dengan berbagai komunitas lingkungan di Kalimantan. Setiap peserta akan mendapatkan kesempatan untuk menanam pohon secara langsung di lokasi yang telah ditentukan, belajar teknik penanaman yang benar dari ahli kehutanan, dan mendapatkan edukasi tentang pentingnya hutan bagi kehidupan.',
    'image' => 'https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=1200&h=600&fit=crop',
    'deadline' => '25 Desember 2024',
    'participants' => 234,
    'maxParticipants' => 500,
    'difficulty' => 'Menengah',
    'location' => 'Kalimantan Timur, Indonesia',
    'organizer' => [
        'name' => 'Yayasan Hutan Lestari',
        'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop',
        'verified' => true,
    ],
    'rewards' => [
        ['type' => 'certificate', 'label' => 'Sertifikat Digital', 'description' => 'Sertifikat resmi yang dapat diverifikasi'],
        ['type' => 'voucher', 'label' => 'Voucher Belanja', 'description' => 'Voucher senilai Rp 500.000'],
        ['type' => 'badge', 'label' => 'Badge Eksklusif', 'description' => 'Badge khusus untuk profil Anda'],
    ],
];
@endphp

<!-- Hero -->
<section class="relative h-[50vh] min-h-[400px] overflow-hidden">
    <div class="absolute inset-0">
        <img
            src="{{ $quest['image'] }}"
            alt="{{ $quest['title'] }}"
            class="w-full h-full object-cover"
        />
        <div class="absolute inset-0 bg-gradient-to-t from-forest via-forest/50 to-transparent"></div>
    </div>
    
    <div class="absolute inset-0 flex items-end">
        <div class="container mx-auto px-4 pb-8">
            <div class="max-w-3xl animate-fade-in">
                <a href="{{ route('quests.index') }}" class="inline-flex items-center gap-2 text-primary-foreground/80 hover:text-primary-foreground mb-4">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Kembali ke Quest
                </a>
                <span class="inline-block px-3 py-1 rounded-lg text-xs font-medium bg-gold/90 text-forest mb-4">
                    {{ $quest['difficulty'] }}
                </span>
                <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold text-primary-foreground mb-4">
                    {{ $quest['title'] }}
                </h1>
                <p class="text-primary-foreground/80 text-lg max-w-2xl">
                    {{ $quest['description'] }}
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-12 bg-background">
    <div class="container mx-auto px-4">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Info Cards -->
                <div class="grid sm:grid-cols-3 gap-4 animate-fade-up">
                    @php
                        $infoCards = [
                            ['icon' => 'calendar', 'label' => 'Deadline', 'value' => $quest['deadline']],
                            ['icon' => 'users', 'label' => 'Peserta', 'value' => $quest['participants'].'/'.$quest['maxParticipants']],
                            ['icon' => 'map', 'label' => 'Lokasi', 'value' => 'Kalimantan Timur'],
                        ];
                    @endphp
                    @foreach($infoCards as $card)
                        <div class="glass-card p-4 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
                                @if($card['icon'] === 'calendar')
                                    <x-heroicon-o-calendar class="w-5 h-5 text-primary" />
                                @elseif($card['icon'] === 'users')
                                    <x-heroicon-o-user-group class="w-5 h-5 text-primary" />
                                @else
                                    <x-heroicon-o-map-pin class="w-5 h-5 text-primary" />
                                @endif
                            </div>
                            <div>
                                <div class="text-xs text-muted-foreground">{{ $card['label'] }}</div>
                                <div class="font-semibold text-foreground">{{ $card['value'] }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Description -->
                <div class="glass-card p-6 animate-fade-up" style="animation-delay: 0.1s;">
                    <h2 class="text-xl font-semibold mb-4">Tentang Quest</h2>
                    <div class="text-muted-foreground leading-relaxed space-y-4">
                        <p>{{ $quest['longDescription'] }}</p>
                        <p>Pohon yang ditanam akan dipantau perkembangannya selama 5 tahun ke depan, dan setiap peserta akan mendapatkan update berkala tentang pertumbuhan pohon yang mereka tanam.</p>
                    </div>
                </div>

                <!-- Location Map -->
                <div class="glass-card p-6 animate-fade-up" style="animation-delay: 0.2s;">
                    <h2 class="text-xl font-semibold mb-4">Lokasi</h2>
                    <div class="flex items-center gap-2 text-muted-foreground mb-4">
                        <x-heroicon-o-map-pin class="w-4 h-4" />
                        {{ $quest['location'] }}
                    </div>
                    <div class="aspect-video rounded-xl overflow-hidden bg-muted">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d4083728.8833!2d115.5!3d0.5!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x320c500dcd1e7c0d%3A0x6d3c6e1b2e5ed7b6!2sEast%20Kalimantan!5e0!3m2!1sen!2sid!4v1699000000000!5m2!1sen!2sid"
                            width="100%"
                            height="100%"
                            style="border: 0"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Action Card -->
                <div class="glass-card p-6 sticky top-24 animate-fade-in">
                    <div class="flex items-center gap-3 mb-6">
                        <img src="{{ $quest['organizer']['avatar'] }}" alt="{{ $quest['organizer']['name'] }}" class="w-12 h-12 rounded-full">
                        <div>
                            <div class="font-medium text-foreground flex items-center gap-1">
                                {{ $quest['organizer']['name'] }}
                                @if($quest['organizer']['verified'])
                                    <x-heroicon-s-check-badge class="w-4 h-4 text-primary" />
                                @endif
                            </div>
                            <div class="text-sm text-muted-foreground">Penyelenggara</div>
                        </div>
                    </div>

                    <!-- Progress -->
                    <div class="space-y-2 mb-6">
                        <div class="flex justify-between text-sm">
                            <span class="text-muted-foreground">Kuota Terisi</span>
                            <span class="font-medium">{{ round(($quest['participants'] / $quest['maxParticipants']) * 100) }}%</span>
                        </div>
                        <div class="h-3 bg-secondary rounded-full overflow-hidden">
                            <div
                                class="h-full bg-gradient-to-r from-primary to-emerald-light rounded-full"
                                style="width: {{ ($quest['participants'] / $quest['maxParticipants']) * 100 }}%"
                            ></div>
                        </div>
                        <div class="text-sm text-muted-foreground">
                            {{ $quest['participants'] }} dari {{ $quest['maxParticipants'] }} peserta
                        </div>
                    </div>

                    <!-- Rewards -->
                    <div class="space-y-3 mb-6">
                        <h3 class="font-medium text-foreground">Reward</h3>
                        @foreach($quest['rewards'] as $reward)
                            <div class="flex items-center gap-3 p-3 bg-secondary/50 rounded-xl">
                                <div class="w-10 h-10 rounded-lg bg-gold/20 flex items-center justify-center">
                                    <x-heroicon-o-gift class="w-5 h-5 text-gold" />
                                </div>
                                <div>
                                    <div class="font-medium text-sm">{{ $reward['label'] }}</div>
                                    <div class="text-xs text-muted-foreground">{{ $reward['description'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <button class="btn-hero w-full mb-3">
                        Ikuti Quest
                    </button>
                    
                    <div class="flex gap-2">
                        <button class="btn-glass flex-1 flex items-center justify-center gap-2">
                            <x-heroicon-o-share class="w-4 h-4" />
                            Bagikan
                        </button>
                        <button class="btn-glass w-12 h-12 flex items-center justify-center">
                            <x-heroicon-o-bookmark class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
