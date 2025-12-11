@extends('layouts.main')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-background via-background to-primary/5 py-8">
    <div class="container mx-auto px-4">
        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold gradient-text mb-2">
                Welcome back, {{ auth()->user()->name }}!
            </h1>
            <p class="text-muted-foreground">Here's your personal dashboard</p>
        </div>

        {{-- Organization Creation Banner --}}
        @if(isset($approvedRequest) && $approvedRequest)
            <div class="mb-6 glass-card p-6 rounded-2xl border-2 border-primary/50 shadow-glow">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-12 h-12 rounded-full gradient-primary flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-xl font-bold text-primary mb-2">🎉 Your Organization Request is Approved!</h3>
                        <p class="text-foreground mb-4">
                            Congratulations! Your request to create "<strong>{{ $approvedRequest->name }}</strong>" has been approved by our admin team. 
                            You can now create your organization and start managing environmental quests!
                        </p>
                        <a href="{{ route('organization.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow hover:shadow-lift hover:-translate-y-1 transition-all duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Your Organization Now
                        </a>
                    </div>
                </div>
            </div>
        @endif

        {{-- Quick Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-card p-6 rounded-2xl hover-lift">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <span class="text-3xl font-bold">{{ auth()->user()->questParticipants()->count() }}</span>
                </div>
                <h3 class="font-semibold text-foreground mb-1">Joined Quests</h3>
                <p class="text-sm text-muted-foreground">Total quests you've participated in</p>
            </div>

            <div class="glass-card p-6 rounded-2xl hover-lift">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-green-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-green-500">{{ auth()->user()->questParticipants()->where('status', 'COMPLETED')->count() }}</span>
                </div>
                <h3 class="font-semibold text-foreground mb-1">Completed Quests</h3>
                <p class="text-sm text-muted-foreground">Successfully finished quests</p>
            </div>

            <div class="glass-card p-6 rounded-2xl hover-lift">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-yellow-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                        </svg>
                    </div>
                    <span class="text-3xl font-bold text-yellow-500">{{ auth()->user()->questParticipants()->where('status', 'APPROVED')->count() }}</span>
                </div>
                <h3 class="font-semibold text-foreground mb-1">Won Prizes</h3>
                <p class="text-sm text-muted-foreground">Approved as winner</p>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="glass-card p-6 rounded-2xl">
            <h2 class="text-2xl font-bold mb-6">Your Recent Quests</h2>
            
            @php
                $recentQuests = auth()->user()->questParticipants()
                    ->with('quest.organization')
                    ->latest()
                    ->take(5)
                    ->get();
            @endphp

            @if($recentQuests->count() > 0)
                <div class="space-y-4">
                    @foreach($recentQuests as $participation)
                        <div class="flex items-center gap-4 p-4 rounded-xl bg-muted/50 hover:bg-muted transition-colors">
                            <div class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                                @if($participation->quest->banner_img)
                                    <img src="{{ asset('storage/' . $participation->quest->banner_img) }}" alt="{{ $participation->quest->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-green-500 to-emerald-600"></div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h3 class="font-semibold text-foreground mb-1">{{ $participation->quest->title }}</h3>
                                <p class="text-sm text-muted-foreground">{{ $participation->quest->organization->name }}</p>
                            </div>
                            <div class="text-right">
                                @if($participation->status === 'APPROVED')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-green-500/10 text-green-500 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Winner
                                    </span>
                                @elseif($participation->status === 'COMPLETED')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-500/10 text-blue-500 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Completed
                                    </span>
                                @elseif($participation->status === 'JOINED')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-yellow-500/10 text-yellow-500 text-sm font-medium">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        In Progress
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-muted text-muted-foreground text-sm font-medium">
                                        {{ $participation->status }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-muted/50 flex items-center justify-center">
                        <svg class="w-10 h-10 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">No quests yet</h3>
                    <p class="text-muted-foreground mb-4">Start your environmental journey by joining a quest!</p>
                    <a href="{{ route('quests.all') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow hover:shadow-lift hover:-translate-y-1 transition-all duration-300">
                        Explore Quests
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
