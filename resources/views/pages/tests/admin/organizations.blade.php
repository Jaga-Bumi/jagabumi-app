@extends('layouts.admin')

@php
$title = 'Organizations Management';
$subtitle = 'View and manage all organizations on the platform.';
@endphp

@section('content')
  {{-- Stats Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    {{-- Total Organizations --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Total Organizations</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($totalOrganizations) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Active Organizations --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Active</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($activeCount) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Inactive Organizations --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Inactive</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($totalOrganizations - $activeCount) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-muted flex items-center justify-center">
          <svg class="w-6 h-6 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
          </svg>
        </div>
      </div>
    </div>
  </div>

  {{-- Search & Filters --}}
  <div class="glass-card rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('admin.organizations') }}" class="flex flex-col md:flex-row gap-4">
      {{-- Search --}}
      <div class="flex-1 relative">
        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input 
          type="text" 
          name="search" 
          value="{{ request('search') }}" 
          placeholder="Search by name, handle, or email..."
          class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
        />
      </div>
      
      {{-- Status Filter --}}
      <div class="flex items-center gap-2">
        <select name="status" class="px-4 py-2.5 rounded-xl border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50">
          <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Status</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
        
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-primary-foreground font-medium hover:opacity-90 transition-all">
          Search
        </button>
        
        @if(request('search') || request('status') !== 'all')
          <a href="{{ route('admin.organizations') }}" class="px-4 py-2.5 rounded-xl border border-border hover:bg-muted transition-all">
            Clear
          </a>
        @endif
      </div>
    </form>
  </div>

  {{-- Organizations List --}}
  <div class="glass-card rounded-xl overflow-hidden">
    <div class="p-5 border-b border-border">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <div>
          <h2 class="font-semibold">All Organizations</h2>
          <p class="text-xs text-muted-foreground">{{ $organizations->total() }} organizations found</p>
        </div>
      </div>
    </div>

    @if($organizations->count() > 0)
      <div class="divide-y divide-border">
        @foreach($organizations as $org)
          <div x-data="{ expanded: false, showStatusModal: false }" class="p-5 hover:bg-muted/30 transition-colors">
            <div class="flex items-start gap-4">
              {{-- Logo --}}
              @if($org->logo_img)
                <img src="{{ $org->logo_img }}" alt="{{ $org->name }}" class="w-14 h-14 rounded-xl object-cover flex-shrink-0" />
              @else
                <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-primary font-bold text-lg flex-shrink-0">
                  {{ strtoupper(substr($org->name, 0, 2)) }}
                </div>
              @endif

              {{-- Info --}}
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <div class="flex items-center gap-2 flex-wrap">
                      <h3 class="font-semibold">{{ $org->name }}</h3>
                      @if($org->status === 'ACTIVE')
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-secondary/10 text-secondary">Active</span>
                      @else
                        <span class="px-2.5 py-0.5 text-xs font-medium rounded-full bg-muted text-muted-foreground">Inactive</span>
                      @endif
                    </div>
                    @if($org->handle)
                      <p class="text-sm text-muted-foreground">@{{ $org->handle }}</p>
                    @endif
                    @if($org->motto)
                      <p class="text-sm text-muted-foreground mt-1 italic">"{{ $org->motto }}"</p>
                    @endif
                  </div>

                  <button @click="expanded = !expanded" class="p-2 hover:bg-muted rounded-lg transition-colors flex-shrink-0">
                    <svg :class="expanded ? 'rotate-180' : ''" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                  </button>
                </div>

                {{-- Stats Row --}}
                <div class="flex items-center gap-4 mt-3 flex-wrap">
                  <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>{{ $org->members_count ?? 0 }} members</span>
                  </div>
                  <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span>{{ $org->quests_count ?? 0 }} quests</span>
                  </div>
                  <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                    </svg>
                    <span>{{ $org->articles_count ?? 0 }} articles</span>
                  </div>
                  <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ $org->created_at->format('M d, Y') }}</span>
                  </div>
                </div>
              </div>
            </div>

            {{-- Expanded Content --}}
            <div x-show="expanded" x-collapse class="mt-4 pt-4 border-t border-border">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Description --}}
                <div class="space-y-2">
                  <h4 class="text-sm font-semibold text-muted-foreground">Description</h4>
                  <p class="text-sm">{{ $org->desc ?? 'No description provided.' }}</p>
                </div>

                {{-- Contact & Links --}}
                <div class="space-y-2">
                  <h4 class="text-sm font-semibold text-muted-foreground">Contact & Links</h4>
                  <div class="space-y-1.5">
                    @if($org->org_email)
                      <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <span>{{ $org->org_email }}</span>
                      </div>
                    @endif
                    @if($org->website_url)
                      <div class="flex items-center gap-2 text-sm">
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                        <a href="{{ $org->website_url }}" target="_blank" class="text-primary hover:underline">{{ $org->website_url }}</a>
                      </div>
                    @endif
                    <div class="flex items-center gap-3 mt-2">
                      @if($org->instagram_url)
                        <a href="{{ $org->instagram_url }}" target="_blank" class="p-2 hover:bg-muted rounded-lg transition-colors" title="Instagram">
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                          </svg>
                        </a>
                      @endif
                      @if($org->x_url)
                        <a href="{{ $org->x_url }}" target="_blank" class="p-2 hover:bg-muted rounded-lg transition-colors" title="X (Twitter)">
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                          </svg>
                        </a>
                      @endif
                      @if($org->facebook_url)
                        <a href="{{ $org->facebook_url }}" target="_blank" class="p-2 hover:bg-muted rounded-lg transition-colors" title="Facebook">
                          <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                          </svg>
                        </a>
                      @endif
                    </div>
                  </div>
                </div>
              </div>

              {{-- Creator Info --}}
              <div class="mt-4 pt-4 border-t border-border">
                <div class="flex items-center justify-between flex-wrap gap-4">
                  <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-primary font-bold text-xs">
                      {{ strtoupper(substr($org->creator->name ?? 'U', 0, 2)) }}
                    </div>
                    <div>
                      <p class="text-sm font-medium">Created by {{ $org->creator->name ?? 'Unknown' }}</p>
                      @if($org->creator && $org->creator->handle)
                        <p class="text-xs text-muted-foreground">@{{ $org->creator->handle }}</p>
                      @endif
                    </div>
                  </div>

                  {{-- Actions --}}
                  <div class="flex items-center gap-2">
                    <a href="{{ route('organizations.show', $org->slug) }}" target="_blank" class="px-4 py-2 text-sm font-medium rounded-xl border border-border hover:bg-muted transition-all flex items-center gap-2">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                      View Page
                    </a>
                    <button 
                      @click="showStatusModal = true" 
                      class="px-4 py-2 text-sm font-medium rounded-xl {{ $org->status === 'ACTIVE' ? 'bg-destructive/10 text-destructive hover:bg-destructive/20' : 'bg-secondary/10 text-secondary hover:bg-secondary/20' }} transition-all"
                    >
                      {{ $org->status === 'ACTIVE' ? 'Deactivate' : 'Activate' }}
                    </button>
                  </div>
                </div>
              </div>

              {{-- Status Modal --}}
              <div x-show="showStatusModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="showStatusModal = false">
                <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showStatusModal = false"></div>
                <div class="relative glass-card rounded-2xl p-6 w-full max-w-md" @click.stop>
                  <h3 class="text-lg font-bold mb-2">{{ $org->status === 'ACTIVE' ? 'Deactivate' : 'Activate' }} Organization</h3>
                  <p class="text-sm text-muted-foreground mb-4">
                    Are you sure you want to {{ $org->status === 'ACTIVE' ? 'deactivate' : 'activate' }} <strong>{{ $org->name }}</strong>?
                    @if($org->status === 'ACTIVE')
                      This will hide the organization from public view.
                    @else
                      This will make the organization visible again.
                    @endif
                  </p>
                  <form method="POST" action="{{ route('admin.organizations.update-status', $org->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="{{ $org->status === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE' }}" />
                    <div class="flex justify-end gap-3">
                      <button type="button" @click="showStatusModal = false" class="px-4 py-2 text-sm font-medium rounded-xl border border-border hover:bg-muted transition-all">
                        Cancel
                      </button>
                      <button type="submit" class="px-4 py-2 text-sm font-medium rounded-xl {{ $org->status === 'ACTIVE' ? 'bg-destructive text-destructive-foreground' : 'bg-secondary text-secondary-foreground' }} hover:opacity-90 transition-all">
                        {{ $org->status === 'ACTIVE' ? 'Deactivate' : 'Activate' }}
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Pagination --}}
      @if($organizations->hasPages())
        <div class="p-4 border-t border-border">
          {{ $organizations->withQueryString()->links() }}
        </div>
      @endif
    @else
      <div class="p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted/50 flex items-center justify-center">
          <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold mb-1">No Organizations Found</h3>
        <p class="text-sm text-muted-foreground">
          @if(request('search'))
            No organizations match your search criteria.
          @else
            There are no organizations registered yet.
          @endif
        </p>
      </div>
    @endif
  </div>
@endsection
