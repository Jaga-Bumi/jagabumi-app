@extends('layouts.admin')

@php
$title = 'Dashboard';
$subtitle = 'Welcome back! Here\'s an overview of platform activity.';
@endphp

@section('content')
  {{-- Stats Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    {{-- Pending Org Requests --}}
    <div class="glass-card hover-lift p-5 rounded-xl border {{ $pendingOrgRequests > 0 ? 'border-highlight/30' : 'border-transparent' }}">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Pending Org Requests</p>
          <p class="text-3xl font-bold mt-1">{{ $pendingOrgRequests }}</p>
          @if($pendingOrgRequests > 0)
            <a href="{{ route('admin.organization-requests', ['status' => 'PENDING']) }}" class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-primary hover:underline">
              Review now
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          @else
            <p class="mt-2 text-sm text-muted-foreground">All caught up!</p>
          @endif
        </div>
        <div class="w-12 h-12 rounded-xl {{ $pendingOrgRequests > 0 ? 'bg-highlight/10' : 'bg-primary/10' }} flex items-center justify-center">
          <svg class="w-6 h-6 {{ $pendingOrgRequests > 0 ? 'text-highlight' : 'text-primary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Quests In Review --}}
    <div class="glass-card hover-lift p-5 rounded-xl border {{ $questsInReview > 0 ? 'border-highlight/30' : 'border-transparent' }}">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Quests In Review</p>
          <p class="text-3xl font-bold mt-1">{{ $questsInReview }}</p>
          @if($questsInReview > 0)
            <a href="{{ route('admin.quests', ['status' => 'IN_REVIEW']) }}" class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-primary hover:underline">
              Review now
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
              </svg>
            </a>
          @else
            <p class="mt-2 text-sm text-muted-foreground">All caught up!</p>
          @endif
        </div>
        <div class="w-12 h-12 rounded-xl {{ $questsInReview > 0 ? 'bg-highlight/10' : 'bg-secondary/10' }} flex items-center justify-center">
          <svg class="w-6 h-6 {{ $questsInReview > 0 ? 'text-highlight' : 'text-secondary' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Total Users --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Total Users</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($totalUsers) }}</p>
          <div class="flex items-center gap-1 mt-2">
            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span class="text-sm font-medium text-primary">Growing</span>
          </div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Active Organizations --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Active Organizations</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($totalOrganizations) }}</p>
          <div class="flex items-center gap-1 mt-2">
            <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span class="text-sm font-medium text-secondary">Active</span>
          </div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
      </div>
    </div>
  </div>

  {{-- Recent Activity Grid --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Recent Organization Requests --}}
    <div class="glass-card rounded-xl overflow-hidden">
      <div class="p-5 border-b border-border flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <div>
            <h2 class="font-semibold">Recent Organization Requests</h2>
            <p class="text-xs text-muted-foreground">Latest submissions from users</p>
          </div>
        </div>
        <a href="{{ route('admin.organization-requests') }}" class="text-sm text-primary hover:underline flex items-center gap-1">
          View All
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>
      
      @if($recentOrgRequests->count() > 0)
        <div class="divide-y divide-border">
          @foreach($recentOrgRequests as $req)
            <div class="p-4 hover:bg-muted/50 transition-colors">
              <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-primary font-bold text-sm flex-shrink-0">
                  {{ strtoupper(substr($req->organization_name, 0, 2)) }}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="font-semibold text-sm truncate">{{ $req->organization_name }}</h4>
                    @if($req->status === 'PENDING')
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-highlight/10 text-highlight">Pending</span>
                    @elseif($req->status === 'APPROVED')
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary">Approved</span>
                    @else
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-destructive/10 text-destructive">Rejected</span>
                    @endif
                  </div>
                  <p class="text-xs text-muted-foreground mt-0.5">
                    {{ $req->organization_type }} • by {{ $req->user->name ?? 'Unknown' }}
                  </p>
                  <p class="text-xs text-muted-foreground mt-1">{{ $req->created_at->diffForHumans() }}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="p-8 text-center">
          <div class="w-16 h-16 rounded-full bg-muted mx-auto flex items-center justify-center mb-3">
            <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
          </div>
          <p class="text-muted-foreground">No organization requests yet</p>
        </div>
      @endif
    </div>

    {{-- Recent Quests --}}
    <div class="glass-card rounded-xl overflow-hidden">
      <div class="p-5 border-b border-border flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </div>
          <div>
            <h2 class="font-semibold">Recent Quests</h2>
            <p class="text-xs text-muted-foreground">Latest quest submissions</p>
          </div>
        </div>
        <a href="{{ route('admin.quests') }}" class="text-sm text-primary hover:underline flex items-center gap-1">
          View All
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>
      
      @if($recentQuests->count() > 0)
        <div class="divide-y divide-border">
          @foreach($recentQuests as $quest)
            <div class="p-4 hover:bg-muted/50 transition-colors">
              <div class="flex items-start gap-3">
                @if($quest->organization && $quest->organization->logo_img)
                  <img src="{{ $quest->organization->logo_img }}" alt="{{ $quest->organization->name }}" class="w-10 h-10 rounded-lg object-cover flex-shrink-0">
                @else
                  <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-secondary/20 to-accent/20 flex items-center justify-center text-secondary font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr($quest->title, 0, 2)) }}
                  </div>
                @endif
                <div class="flex-1 min-w-0">
                  <div class="flex items-center gap-2 flex-wrap">
                    <h4 class="font-semibold text-sm truncate">{{ $quest->title }}</h4>
                    @if($quest->status === 'IN REVIEW')
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-highlight/10 text-highlight">In Review</span>
                    @elseif($quest->status === 'APPROVED')
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-blue-500/10 text-blue-600">Approved</span>
                    @elseif($quest->status === 'ACTIVE')
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary">Active</span>
                    @elseif($quest->status === 'ENDED')
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-muted text-muted-foreground">Ended</span>
                    @elseif($quest->status === 'REJECTED')
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-destructive/10 text-destructive">Rejected</span>
                    @else
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-muted text-muted-foreground">{{ $quest->status }}</span>
                    @endif
                  </div>
                  <p class="text-xs text-muted-foreground mt-0.5">
                    by {{ $quest->organization->name ?? 'N/A' }}
                  </p>
                  <p class="text-xs text-muted-foreground mt-1">{{ $quest->created_at->diffForHumans() }}</p>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div class="p-8 text-center">
          <div class="w-16 h-16 rounded-full bg-muted mx-auto flex items-center justify-center mb-3">
            <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </div>
          <p class="text-muted-foreground">No quests yet</p>
        </div>
      @endif
    </div>
  </div>

  {{-- Quick Actions --}}
  <div class="mt-6">
    <div class="glass-card rounded-xl p-5">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-accent/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
          </svg>
        </div>
        <div>
          <h2 class="font-semibold">Quick Actions</h2>
          <p class="text-xs text-muted-foreground">Common administrative tasks</p>
        </div>
      </div>
      
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <a href="{{ route('admin.organization-requests', ['status' => 'PENDING']) }}" class="flex items-center gap-3 p-4 rounded-xl border border-border hover:bg-muted/50 hover:border-primary/30 transition-all group">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center group-hover:bg-primary/20 transition-colors">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="font-medium text-sm">Review Org Requests</p>
            <p class="text-xs text-muted-foreground">{{ $pendingOrgRequests }} pending</p>
          </div>
        </a>
        
        <a href="{{ route('admin.quests', ['status' => 'IN_REVIEW']) }}" class="flex items-center gap-3 p-4 rounded-xl border border-border hover:bg-muted/50 hover:border-secondary/30 transition-all group">
          <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center group-hover:bg-secondary/20 transition-colors">
            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
          </div>
          <div>
            <p class="font-medium text-sm">Review Quests</p>
            <p class="text-xs text-muted-foreground">{{ $questsInReview }} pending</p>
          </div>
        </a>
        
        <a href="{{ route('admin.quests') }}" class="flex items-center gap-3 p-4 rounded-xl border border-border hover:bg-muted/50 hover:border-accent/30 transition-all group">
          <div class="w-10 h-10 rounded-lg bg-accent/10 flex items-center justify-center group-hover:bg-accent/20 transition-colors">
            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
          </div>
          <div>
            <p class="font-medium text-sm">All Quests</p>
            <p class="text-xs text-muted-foreground">Browse all quests</p>
          </div>
        </a>
        
        <a href="{{ route('admin.organization-requests') }}" class="flex items-center gap-3 p-4 rounded-xl border border-border hover:bg-muted/50 hover:border-highlight/30 transition-all group">
          <div class="w-10 h-10 rounded-lg bg-highlight/10 flex items-center justify-center group-hover:bg-highlight/20 transition-colors">
            <svg class="w-5 h-5 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <div>
            <p class="font-medium text-sm">All Org Requests</p>
            <p class="text-xs text-muted-foreground">View history</p>
          </div>
        </a>
      </div>
    </div>
  </div>
@endsection
