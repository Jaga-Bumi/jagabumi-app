@extends('layouts.main')

@section('title', 'JagaBumi - Turn Your Eco-Actions Into Real Impact')

@section('content')
  {{-- Hero Section --}}
  <section class="relative min-h-[90vh] flex items-center overflow-hidden">
    {{-- Background --}}
    <div class="absolute inset-0">
      <img src="{{ asset('images/hero-bg.jpg') }}" alt="Hero Background" class="w-full h-full object-cover" />
      <div class="absolute inset-0 bg-gradient-to-r from-background/95 via-background/80 to-background/40"></div>
    </div>

    <div class="container relative z-10 py-20">
      <div class="max-w-2xl space-y-8 animate-slide-up">
        <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-medium">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
          </svg>
          #1 Environmental Gamification Platform
        </span>
        
        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold leading-tight">
          Turn Your <span class="gradient-text">Eco-Actions</span><br />Into Real Impact
        </h1>
        
        <p class="text-lg text-muted-foreground max-w-xl">
          Join thousands of eco-warriors completing quests, earning rewards, and making a real difference for Mother Earth. Every action counts.
        </p>
        
        <div class="flex flex-wrap gap-4">
          <a href="{{ route('quests.all') }}" class="inline-flex items-center gap-2 h-14 px-8 rounded-lg font-semibold gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300">
            Explore Quests
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </a>
          <a href="{{ route('join-us') }}" class="inline-flex items-center gap-2 h-14 px-8 rounded-lg font-semibold glass-card hover-lift transition-all duration-300">
            Join Us
          </a>
        </div>

        {{-- Quick Stats --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-8">
          @php
            $stats = [
              ['value' => '12K+', 'label' => 'Eco Warriors', 'icon' => 'users'],
              ['value' => number_format(\App\Models\Quest::count()), 'label' => 'Active Quests', 'icon' => 'target'],
              ['value' => '2.4M', 'label' => 'Trees Planted', 'icon' => 'tree'],
              ['value' => number_format(\App\Models\Organization::count()) . '+', 'label' => 'Organizations', 'icon' => 'leaf'],
            ];
          @endphp
          @foreach($stats as $index => $stat)
            <div class="text-center animate-slide-up stagger-{{ $index + 1 }}" style="opacity: 0;">
              <div class="flex items-center justify-center gap-2 mb-1">
                @if($stat['icon'] === 'users')
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                @elseif($stat['icon'] === 'target')
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                  </svg>
                @elseif($stat['icon'] === 'tree')
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                  </svg>
                @else
                  <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                  </svg>
                @endif
                <span class="text-2xl font-bold gradient-text">{{ $stat['value'] }}</span>
              </div>
              <p class="text-sm text-muted-foreground">{{ $stat['label'] }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Floating Elements --}}
    <div class="absolute right-10 top-1/3 hidden xl:block animate-float">
      <div class="w-20 h-20 rounded-2xl gradient-primary shadow-glow flex items-center justify-center">
        <svg class="w-10 h-10 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
      </div>
    </div>
    <div class="absolute right-40 bottom-1/3 hidden xl:block animate-float" style="animation-delay: 1s;">
      <div class="w-16 h-16 rounded-2xl bg-secondary shadow-soft flex items-center justify-center">
        <svg class="w-8 h-8 text-secondary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </div>
    </div>
  </section>

  {{-- Active Quests Section --}}
  <section class="py-20 bg-muted/30">
    <div class="container">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-10">
        <div>
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-medium mb-3">
            Featured Quests
          </span>
          <h2 class="text-3xl font-bold">Active Quests Near You</h2>
        </div>
        <a href="{{ route('quests.all') }}" class="inline-flex items-center gap-2 h-11 px-6 rounded-lg font-semibold border border-border hover:bg-muted transition-all duration-300 hover-lift">
          View All Quests
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>

      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($top3Quests as $index => $quest)
          <div class="card-quest overflow-hidden animate-slide-up stagger-{{ $index + 1 }}" style="opacity: 0;">
            <div class="relative h-48 overflow-hidden">
              <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20"></div>
              <div class="absolute top-3 right-3">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-card/80 backdrop-blur-sm text-xs font-semibold shadow-soft">
                  500 Points
                </span>
              </div>
            </div>
            <div class="p-5 space-y-4">
              <div>
                <h3 class="font-semibold text-lg mb-1">{{ $quest->title }}</h3>
                <p class="text-sm text-muted-foreground">{{ $quest->organization->name ?? 'Organization' }}</p>
              </div>
              <div class="flex flex-wrap gap-3 text-sm text-muted-foreground">
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                  </svg>
                  {{ $quest->location_name }}
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                  {{ $quest->quest_participants_count ?? 0 }}
                </span>
                <span class="flex items-center gap-1">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                  </svg>
                  {{ $quest->quest_start_at->diffForHumans() }}
                </span>
              </div>
              <a href="{{ route('quests.detail', $quest->slug) }}" class="inline-flex items-center justify-center w-full h-11 px-4 rounded-lg font-semibold bg-primary text-primary-foreground hover:opacity-90 transition-all duration-300">
                View Quest
              </a>
            </div>
          </div>
        @empty
          <div class="col-span-full text-center py-12 text-muted-foreground">
            <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
            </svg>
            <p>No active quests at the moment. Check back soon!</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  {{-- Impact Stats Section --}}
  <section class="py-20 relative overflow-hidden">
    <div class="absolute inset-0 gradient-primary opacity-5"></div>
    <div class="container relative">
      <div class="text-center max-w-2xl mx-auto mb-12">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-medium mb-3">
          Our Impact
        </span>
        <h2 class="text-3xl font-bold mb-4">Together, We're Making History</h2>
        <p class="text-muted-foreground">
          Every quest completed, every tree planted, every piece of trash collected brings us closer to a sustainable future.
        </p>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
          $impactStats = [
            ['value' => '2.4M+', 'label' => 'Trees Planted', 'icon' => 'tree'],
            ['value' => '15K', 'label' => 'Water Sources Cleaned', 'icon' => 'droplet'],
            ['value' => '890T', 'label' => 'Waste Recycled', 'icon' => 'recycle'],
            ['value' => '12K+', 'label' => 'Active Warriors', 'icon' => 'users'],
          ];
        @endphp
        @foreach($impactStats as $stat)
          <div class="glass-card text-center p-8">
            <div class="w-16 h-16 rounded-2xl bg-muted flex items-center justify-center mx-auto mb-4">
              @if($stat['icon'] === 'tree')
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              @elseif($stat['icon'] === 'droplet')
                <svg class="w-8 h-8 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              @elseif($stat['icon'] === 'recycle')
                <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
              @else
                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
              @endif
            </div>
            <div class="text-3xl font-bold gradient-text mb-2">{{ $stat['value'] }}</div>
            <p class="text-muted-foreground">{{ $stat['label'] }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  {{-- Featured Organizations --}}
  <section class="py-20 bg-muted/30">
    <div class="container">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-10">
        <div>
          <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/10 text-secondary border border-secondary/20 text-sm font-medium mb-3">
            Top Organizations
          </span>
          <h2 class="text-3xl font-bold">Trusted by Leading Eco-Organizations</h2>
        </div>
        <a href="{{ route('organizations.all') }}" class="inline-flex items-center gap-2 h-11 px-6 rounded-lg font-semibold border border-border hover:bg-muted transition-all duration-300 hover-lift">
          View All
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>

      <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($top3Orgs as $org)
          <div class="card-interactive p-6">
            <div class="w-16 h-16 rounded-2xl gradient-primary flex items-center justify-center text-3xl text-primary-foreground mb-4">
              {{ strtoupper(substr($org->name, 0, 1)) }}
            </div>
            <h3 class="font-semibold mb-2">{{ $org->name }}</h3>
            <div class="flex items-center gap-2 text-sm text-muted-foreground mb-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
              </svg>
              <span>{{ $org->quests_count ?? 0 }} Quests</span>
            </div>
            <a href="{{ route('organizations.all') }}" class="inline-flex items-center justify-center w-full h-9 px-4 rounded-lg font-semibold bg-muted hover:bg-muted/80 transition-all duration-300 mt-2">
              View Profile
            </a>
          </div>
        @empty
          <div class="col-span-full text-center py-12 text-muted-foreground">
            <p>No organizations yet.</p>
          </div>
        @endforelse
      </div>
    </div>
  </section>

  {{-- CTA Section --}}
  <section class="py-20 relative overflow-hidden">
    <div class="absolute inset-0 gradient-primary"></div>
    <div class="absolute inset-0 bg-hero-pattern opacity-10"></div>
    
    <div class="container relative z-10 text-center">
      <div class="max-w-2xl mx-auto space-y-6">
        <h2 class="text-3xl sm:text-4xl font-bold text-primary-foreground">
          Ready to Make a Difference?
        </h2>
        <p class="text-primary-foreground/80 text-lg">
          Join JagaBumi today and start your journey as an eco-warrior. Complete quests, earn rewards, and help save our planet.
        </p>
        <div class="flex flex-wrap justify-center gap-4 pt-4">
          @guest
            <button id="auth-btn-cta" type="button" class="inline-flex items-center gap-2 h-14 px-8 rounded-lg font-semibold bg-card text-foreground hover:bg-card/90 shadow-lift transition-all duration-300">
              Get Started Now
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </button>
          @else
            <a href="{{ route('quests.all') }}" class="inline-flex items-center gap-2 h-14 px-8 rounded-lg font-semibold bg-card text-foreground hover:bg-card/90 shadow-lift transition-all duration-300">
              Explore Quests
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
              </svg>
            </a>
          @endguest
          <a href="{{ route('join-us') }}" class="inline-flex items-center gap-2 h-14 px-8 rounded-lg font-semibold border-2 border-primary-foreground/30 text-primary-foreground hover:bg-primary-foreground/10 transition-all duration-300">
            Become an Organization
          </a>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
  <script>
    // Sync CTA auth button with navbar auth button
    document.addEventListener('DOMContentLoaded', function() {
      const ctaAuthBtn = document.getElementById('auth-btn-cta');
      if (ctaAuthBtn) {
        ctaAuthBtn.addEventListener('click', () => {
          const authBtn = document.getElementById('auth-btn');
          if (authBtn) authBtn.click();
        });
      }
    });
  </script>
@endpush
