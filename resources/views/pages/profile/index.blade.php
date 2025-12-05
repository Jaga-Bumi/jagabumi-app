@extends('layouts.main')

@section('title', 'Profil Saya - JagaBumi.id')

@section('content')
@php
$user = auth()->user() ?? (object)[
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop',
    'points' => 8500,
    'rank' => 12,
    'quests_completed' => 17,
    'badges' => 5,
    'joined' => 'Oktober 2024',
];

$myQuests = [
    ['title' => 'Tanam 1000 Pohon di Kalimantan', 'status' => 'completed', 'date' => '15 Nov 2024', 'points' => 500],
    ['title' => 'Bersih Pantai Bali Challenge', 'status' => 'in_progress', 'date' => 'Sedang Berjalan', 'points' => 0],
    ['title' => 'Urban Farming Jakarta', 'status' => 'completed', 'date' => '10 Nov 2024', 'points' => 300],
];
@endphp

<!-- Header -->
<section class="bg-gradient-to-b from-secondary/50 to-background py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">
            <div class="glass-card p-8">
                <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                    <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full ring-4 ring-primary/20">
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl font-bold text-foreground mb-2">{{ $user->name }}</h1>
                        <p class="text-muted-foreground mb-4">{{ $user->email }}</p>
                        <div class="flex flex-wrap gap-4 justify-center md:justify-start">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ number_format($user->points) }}</div>
                                <div class="text-sm text-muted-foreground">Poin</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ $user->rank }}</div>
                                <div class="text-sm text-muted-foreground">Rank</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ $user->quests_completed }}</div>
                                <div class="text-sm text-muted-foreground">Quest</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ $user->badges }}</div>
                                <div class="text-sm text-muted-foreground">Badge</div>
                            </div>
                        </div>
                    </div>
                    <button class="btn-glass px-6 py-2">Edit Profil</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto space-y-8">
            <!-- My Quests -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold text-foreground mb-6">Quest Saya</h2>
                <div class="space-y-4">
                    @foreach($myQuests as $quest)
                        <div class="flex items-center justify-between p-4 bg-secondary/30 rounded-xl hover:bg-secondary/50 transition-colors">
                            <div class="flex-1">
                                <h3 class="font-medium text-foreground mb-1">{{ $quest['title'] }}</h3>
                                <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                    <span>{{ $quest['date'] }}</span>
                                    @if($quest['status'] === 'completed')
                                        <span class="px-2 py-1 rounded-lg bg-accent/20 text-accent-foreground text-xs">Selesai</span>
                                    @else
                                        <span class="px-2 py-1 rounded-lg bg-gold/20 text-forest text-xs">Berlangsung</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                @if($quest['status'] === 'completed')
                                    <div class="font-semibold text-primary">+{{ $quest['points'] }} pts</div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Achievements -->
            <div class="glass-card p-6">
                <h2 class="text-xl font-semibold text-foreground mb-6">Pencapaian</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    @foreach(['Pemula', 'Pelestari', 'Pejuang Hijau', 'Pahlawan Bumi'] as $badge)
                        <div class="text-center p-4 bg-secondary/30 rounded-xl">
                            <div class="w-16 h-16 rounded-full bg-gold/20 flex items-center justify-center mx-auto mb-3">
                                <x-heroicon-s-check-badge class="w-8 h-8 text-gold" />
                            </div>
                            <div class="font-medium text-sm text-foreground">{{ $badge }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
