@extends('layouts.main')

@section('title', 'Leaderboard - JagaBumi')

@section('content')
  <div class="min-h-screen py-8">
    <div class="container">
      {{-- Header --}}
      <div class="text-center mb-12">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-medium mb-4">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
          </svg>
          Leaderboard
        </span>
        <h1 class="text-3xl sm:text-4xl font-bold mb-4">
          Top <span class="gradient-text">Eco-Warriors</span>
        </h1>
        <p class="text-muted-foreground max-w-lg mx-auto">
          Celebrating the champions of environmental action. Ranked by total quests completed!
        </p>
      </div>

      {{-- Top 3 Podium --}}
      @if($topUsers->count() >= 3)
        <div class="flex justify-center items-end gap-4 mb-12">
          {{-- 2nd Place --}}
          <div class="text-center animate-slide-up stagger-2" style="opacity: 0;">
            <div class="relative mb-4">
              @if($topUsers[1]->avatar_url)
                <img src="{{ $topUsers[1]->avatar_url }}" alt="{{ $topUsers[1]->name }}" class="w-20 h-20 rounded-full border-4 border-muted-foreground/30 shadow-lg object-cover">
              @else
                <div class="w-20 h-20 rounded-full border-4 border-muted-foreground/30 shadow-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-2xl">
                  {{ strtoupper(substr($topUsers[1]->name, 0, 1)) }}
                </div>
              @endif
              <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-muted-foreground/20 flex items-center justify-center text-sm font-bold">
                2
              </div>
            </div>
            <div class="bg-muted-foreground/20 rounded-t-lg pt-4 pb-8 px-6 min-w-[120px]">
              <p class="font-semibold text-sm truncate">{{ $topUsers[1]->name }}</p>
              <p class="text-primary font-bold">{{ $topUsers[1]->completed_quests_count }}</p>
              <p class="text-xs text-muted-foreground">quests completed</p>
            </div>
          </div>

          {{-- 1st Place --}}
          <div class="text-center animate-slide-up stagger-1" style="opacity: 0;">
            <div class="relative mb-4">
              <div class="absolute -top-6 left-1/2 -translate-x-1/2">
                <svg class="w-8 h-8 text-highlight animate-bounce-subtle" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                </svg>
              </div>
              @if($topUsers[0]->avatar_url)
                <img src="{{ $topUsers[0]->avatar_url }}" alt="{{ $topUsers[0]->name }}" class="w-24 h-24 rounded-full border-4 border-highlight shadow-glow object-cover">
              @else
                <div class="w-24 h-24 rounded-full border-4 border-highlight shadow-glow bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-3xl">
                  {{ strtoupper(substr($topUsers[0]->name, 0, 1)) }}
                </div>
              @endif
              <div class="absolute -bottom-2 -right-2 w-10 h-10 rounded-full gradient-primary flex items-center justify-center text-primary-foreground font-bold shadow-glow">
                1
              </div>
            </div>
            <div class="gradient-primary rounded-t-lg pt-6 pb-12 px-8 min-w-[140px]">
              <p class="font-semibold text-primary-foreground truncate">{{ $topUsers[0]->name }}</p>
              <p class="text-primary-foreground font-bold text-lg">{{ $topUsers[0]->completed_quests_count }}</p>
              <p class="text-xs text-primary-foreground/80">quests completed</p>
            </div>
          </div>

          {{-- 3rd Place --}}
          <div class="text-center animate-slide-up stagger-3" style="opacity: 0;">
            <div class="relative mb-4">
              @if($topUsers[2]->avatar_url)
                <img src="{{ $topUsers[2]->avatar_url }}" alt="{{ $topUsers[2]->name }}" class="w-20 h-20 rounded-full border-4 border-highlight/30 shadow-lg object-cover">
              @else
                <div class="w-20 h-20 rounded-full border-4 border-highlight/30 shadow-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center text-white font-bold text-2xl">
                  {{ strtoupper(substr($topUsers[2]->name, 0, 1)) }}
                </div>
              @endif
              <div class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-highlight/20 flex items-center justify-center text-sm font-bold">
                3
              </div>
            </div>
            <div class="bg-highlight/20 rounded-t-lg pt-4 pb-6 px-6 min-w-[120px]">
              <p class="font-semibold text-sm truncate">{{ $topUsers[2]->name }}</p>
              <p class="text-primary font-bold">{{ $topUsers[2]->completed_quests_count }}</p>
              <p class="text-xs text-muted-foreground">quests completed</p>
            </div>
          </div>
        </div>
      @endif

      {{-- Tabs --}}
      <div x-data="{ activeTab: 'users' }" class="w-full">
        {{-- Tab Buttons --}}
        <div class="flex justify-center mb-8">
          <div class="inline-flex gap-2 p-1 bg-muted rounded-lg">
            <button 
              @click="activeTab = 'users'" 
              :class="activeTab === 'users' ? 'gradient-primary text-primary-foreground shadow-glow' : 'text-muted-foreground hover:text-foreground'"
              class="flex items-center gap-2 px-6 py-2.5 rounded-md font-semibold transition-all duration-300"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
              </svg>
              <span>Users</span>
            </button>
            <button 
              @click="activeTab = 'quests'" 
              :class="activeTab === 'quests' ? 'gradient-primary text-primary-foreground shadow-glow' : 'text-muted-foreground hover:text-foreground'"
              class="flex items-center gap-2 px-6 py-2.5 rounded-md font-semibold transition-all duration-300"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
              </svg>
              <span>Quests</span>
            </button>
          </div>
        </div>

        {{-- Users Tab Content --}}
        <div x-show="activeTab === 'users'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="bg-card border border-border rounded-xl overflow-hidden shadow-soft">
          <div class="p-6 border-b border-border bg-muted/30">
            <h3 class="flex items-center gap-2 text-lg font-semibold">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
              </svg>
              Top Eco-Warriors by Quests Completed
            </h3>
          </div>
          <div class="p-6">
            <div class="space-y-2">
              @forelse($topUsers as $index => $user)
                <div class="flex items-center gap-4 p-4 rounded-xl transition-all duration-300 hover-lift {{ $index < 3 ? 'bg-primary/5 border border-primary/10' : 'hover:bg-muted' }}">
                  <div class="flex-shrink-0">
                    @if($index === 0)
                      <svg class="w-6 h-6 text-highlight" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    @elseif($index === 1)
                      <svg class="w-6 h-6 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                      </svg>
                    @elseif($index === 2)
                      <svg class="w-6 h-6 text-highlight/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                      </svg>
                    @else
                      <span class="w-6 h-6 flex items-center justify-center text-muted-foreground font-bold text-sm">{{ $index + 1 }}</span>
                    @endif
                  </div>
                  
                  @if($user->avatar_url)
                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-12 h-12 rounded-full object-cover border-2 border-border">
                  @else
                    <div class="w-12 h-12 rounded-full gradient-primary flex items-center justify-center text-white font-bold text-lg shadow-soft">
                      {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                  @endif
                  
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <p class="font-semibold truncate">{{ $user->name }}</p>
                    </div>
                    <p class="text-sm text-muted-foreground flex items-center gap-1">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      {{ $user->completed_quests_count }} quests completed
                    </p>
                  </div>
                  
                  <div class="text-right">
                    <p class="font-bold text-primary text-xl">{{ $user->completed_quests_count }}</p>
                    <p class="text-xs text-muted-foreground">quests</p>
                  </div>
                  
                  <div class="text-right">
                    <p class="font-bold text-primary">{{ $user->completed_quests_count }}</p>
                    <p class="text-xs text-muted-foreground">quests</p>
                  </div>
                </div>
              @empty
                <div class="text-center py-12 text-muted-foreground">
                  <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                  <p>No users yet</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>

        {{-- Quests Tab Content --}}
        <div x-show="activeTab === 'quests'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100" class="bg-card border border-border rounded-xl overflow-hidden shadow-soft">
          <div class="p-6 border-b border-border bg-muted/30">
            <h3 class="flex items-center gap-2 text-lg font-semibold">
              <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
              </svg>
              Most Popular Quests
            </h3>
          </div>
          <div class="p-6">
            <div class="space-y-2">
              @forelse($topQuests as $index => $quest)
                <a href="{{ route('quests.detail', $quest->slug) }}" class="flex items-center gap-4 p-4 rounded-xl transition-all duration-200 hover-lift {{ $index < 3 ? 'bg-primary/5 border border-primary/10' : 'hover:bg-muted' }}">
                  <div class="flex-shrink-0">
                    @if($index === 0)
                      <svg class="w-6 h-6 text-highlight" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                      </svg>
                    @elseif($index === 1)
                      <svg class="w-6 h-6 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                      </svg>
                    @elseif($index === 2)
                      <svg class="w-6 h-6 text-highlight/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                      </svg>
                    @else
                      <span class="w-6 h-6 flex items-center justify-center text-muted-foreground font-bold text-sm">{{ $index + 1 }}</span>
                    @endif
                  </div>
                  
                  <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center shadow-soft">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                    </svg>
                  </div>
                  
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold truncate">{{ $quest->title }}</p>
                    <p class="text-sm text-muted-foreground">by {{ $quest->organization->name ?? 'Organization' }}</p>
                  </div>
                  
                  <div class="text-right">
                    <div class="flex items-center gap-1 justify-end">
                      <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                      </svg>
                      <span class="font-bold">{{ $quest->quest_participants_count ?? 0 }}</span>
                    </div>
                    <span class="flex items-center gap-1 text-sm text-muted-foreground justify-end">
                      <svg class="w-3 h-3 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      {{ $quest->approved_submissions_count ?? 0 }} approved
                    </span>
                  </div>
                </a>
              @empty
                <div class="text-center py-12 text-muted-foreground">
                  <svg class="w-16 h-16 mx-auto mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                  </svg>
                  <p>No quests yet</p>
                </div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
@endsection

@push('scripts')
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
@endpush
