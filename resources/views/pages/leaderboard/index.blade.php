@extends('layouts.main')

@section('title', 'Leaderboard - JagaBumi.id')

@section('content')
@php
$leaderboard = [
    ['rank' => 1, 'name' => 'Ahmad Rizki', 'points' => 12500, 'quests' => 25, 'avatar' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=100&h=100&fit=crop', 'badge' => 'gold'],
    ['rank' => 2, 'name' => 'Siti Rahayu', 'points' => 11200, 'quests' => 23, 'avatar' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=100&h=100&fit=crop', 'badge' => 'silver'],
    ['rank' => 3, 'name' => 'Budi Santoso', 'points' => 10800, 'quests' => 22, 'avatar' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=100&h=100&fit=crop', 'badge' => 'bronze'],
    ['rank' => 4, 'name' => 'Maya Putri', 'points' => 9500, 'quests' => 19, 'avatar' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=100&h=100&fit=crop'],
    ['rank' => 5, 'name' => 'Dina Kartika', 'points' => 8900, 'quests' => 18, 'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=100&h=100&fit=crop'],
    ['rank' => 6, 'name' => 'Andi Pratama', 'points' => 8200, 'quests' => 17, 'avatar' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=100&h=100&fit=crop'],
    ['rank' => 7, 'name' => 'Rina Wati', 'points' => 7800, 'quests' => 16, 'avatar' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=100&h=100&fit=crop'],
    ['rank' => 8, 'name' => 'Fajar Ramadan', 'points' => 7200, 'quests' => 15, 'avatar' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=100&h=100&fit=crop'],
];
@endphp

<!-- Hero -->
<section class="bg-gradient-to-b from-secondary/50 to-background py-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-10 animate-fade-in">
            <span class="inline-block px-4 py-2 rounded-full bg-gold/20 text-gold text-sm font-medium mb-4">
                🏆 Top Contributors
            </span>
            <h1 class="text-4xl md:text-5xl font-bold text-foreground mb-4">
                Leaderboard
            </h1>
            <p class="text-muted-foreground max-w-2xl mx-auto">
                Lihat peringkat pahlawan lingkungan berdasarkan kontribusi mereka
            </p>
        </div>

        <!-- Period Selector -->
        <div class="flex justify-center gap-2 mb-8">
            <button class="btn-hero text-sm px-6 py-2">Bulan Ini</button>
            <button class="btn-glass text-sm px-6 py-2">Semua Waktu</button>
        </div>
    </div>
</section>

<!-- Top 3 Podium -->
<section class="py-12 bg-background">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto mb-12">
            @foreach(array_slice($leaderboard, 0, 3) as $i => $user)
                <div class="animate-fade-up {{ $i === 0 ? 'md:order-2' : ($i === 1 ? 'md:order-1' : 'md:order-3') }}" style="animation-delay: {{ $i * 0.1 }}s;">
                    <div class="glass-card p-6 text-center {{ $i === 0 ? 'ring-2 ring-gold shadow-glow' : '' }}">
                        <div class="relative inline-block mb-4">
                            <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" class="w-24 h-24 rounded-full mx-auto {{ $i === 0 ? 'ring-4 ring-gold' : ($i === 1 ? 'ring-4 ring-gray-300' : 'ring-4 ring-orange-600') }}">
                            <div class="absolute -top-2 -right-2 w-10 h-10 rounded-full flex items-center justify-center text-lg {{ $i === 0 ? 'bg-gold text-forest' : ($i === 1 ? 'bg-gray-300 text-gray-700' : 'bg-orange-600 text-white') }}">
                                {{ $user['rank'] }}
                            </div>
                        </div>
                        <h3 class="font-bold text-lg text-foreground mb-2">{{ $user['name'] }}</h3>
                        <div class="text-2xl font-bold text-primary mb-4">{{ number_format($user['points']) }} pts</div>
                        <div class="flex justify-center gap-4 text-sm text-muted-foreground">
                            <div>
                                <div class="font-medium text-foreground">{{ $user['quests'] }}</div>
                                <div class="text-xs">Quest</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Full Leaderboard -->
        <div class="max-w-4xl mx-auto">
            <div class="glass-card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-secondary/50 border-b border-border">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Rank</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-foreground">Pengguna</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-foreground">Quest</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold text-foreground">Poin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border">
                            @foreach(array_slice($leaderboard, 3) as $i => $user)
                                <tr class="hover:bg-secondary/30 transition-colors animate-fade-in" style="animation-delay: {{ $i * 0.05 }}s;">
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-muted text-muted-foreground font-medium text-sm">
                                            {{ $user['rank'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $user['avatar'] }}" alt="{{ $user['name'] }}" class="w-10 h-10 rounded-full">
                                            <div>
                                                <div class="font-medium text-foreground">{{ $user['name'] }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-muted-foreground">{{ $user['quests'] }}</td>
                                    <td class="px-6 py-4 text-right font-semibold text-primary">{{ number_format($user['points']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
