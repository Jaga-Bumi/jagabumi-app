@extends('layouts.admin')

@php
$title = 'Articles Management';
$subtitle = 'View and manage all articles on the platform.';
@endphp

@section('content')
  {{-- Stats Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    {{-- Total Articles --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Total Articles</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($totalArticles) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Organization Articles --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Organization Articles</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($orgArticles) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
      </div>
    </div>

    {{-- User Articles --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">User Articles</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($userArticles) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
      </div>
    </div>
  </div>

  {{-- Search & Filters --}}
  <div class="glass-card rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('admin.articles') }}" class="flex flex-col md:flex-row gap-4">
      {{-- Search --}}
      <div class="flex-1 relative">
        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input 
          type="text" 
          name="search" 
          value="{{ request('search') }}" 
          placeholder="Search by title or content..."
          class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
        />
      </div>
      
      {{-- Filter --}}
      <div class="flex items-center gap-2">
        <select name="filter" class="px-4 py-2.5 rounded-xl border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50">
          <option value="all" {{ request('filter') === 'all' ? 'selected' : '' }}>All Articles</option>
          <option value="organization" {{ request('filter') === 'organization' ? 'selected' : '' }}>Organization</option>
          <option value="user" {{ request('filter') === 'user' ? 'selected' : '' }}>User</option>
        </select>
        
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-primary-foreground font-medium hover:opacity-90 transition-all">
          Search
        </button>
        
        @if(request('search') || request('filter') !== 'all')
          <a href="{{ route('admin.articles') }}" class="px-4 py-2.5 rounded-xl border border-border hover:bg-muted transition-all">
            Clear
          </a>
        @endif
      </div>
    </form>
  </div>

  {{-- Articles List --}}
  <div class="glass-card rounded-xl overflow-hidden">
    <div class="p-5 border-b border-border">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
          </svg>
        </div>
        <div>
          <h2 class="font-semibold">All Articles</h2>
          <p class="text-xs text-muted-foreground">{{ $articles->total() }} articles found</p>
        </div>
      </div>
    </div>

    @if($articles->count() > 0)
      <div class="divide-y divide-border">
        @foreach($articles as $article)
          <div x-data="{ showDeleteModal: false }" class="p-5 hover:bg-muted/30 transition-colors">
            <div class="flex gap-4">
              {{-- Thumbnail --}}
              @if($article->thumbnail)
                <img src="{{ $article->thumbnail }}" alt="{{ $article->title }}" class="w-32 h-24 rounded-xl object-cover flex-shrink-0" />
              @else
                <div class="w-32 h-24 rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center flex-shrink-0">
                  <svg class="w-8 h-8 text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                  </svg>
                </div>
              @endif

              {{-- Content --}}
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <h3 class="font-semibold line-clamp-1">{{ $article->title }}</h3>
                    <p class="text-sm text-muted-foreground line-clamp-2 mt-1">
                      {{ Str::limit(strip_tags($article->body), 150) }}
                    </p>
                  </div>
                  <div class="flex items-center gap-2 flex-shrink-0">
                    <a href="{{ route('articles.single', $article->id) }}" target="_blank" class="p-2 hover:bg-muted rounded-lg transition-colors" title="View Article">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                    </a>
                    <button @click="showDeleteModal = true" class="p-2 hover:bg-destructive/10 text-destructive rounded-lg transition-colors" title="Delete Article">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                      </svg>
                    </button>
                  </div>
                </div>

                {{-- Meta Info --}}
                <div class="flex items-center gap-4 mt-3 flex-wrap">
                  {{-- Author/Organization --}}
                  @if($article->organization)
                    <div class="flex items-center gap-2">
                      @if($article->organization->logo_img)
                        <img src="{{ $article->organization->logo_img }}" alt="{{ $article->organization->name }}" class="w-6 h-6 rounded-full object-cover" />
                      @else
                        <div class="w-6 h-6 rounded-full bg-secondary/20 flex items-center justify-center text-xs font-bold text-secondary">
                          {{ strtoupper(substr($article->organization->name, 0, 1)) }}
                        </div>
                      @endif
                      <span class="text-sm">{{ $article->organization->name }}</span>
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-secondary/10 text-secondary">Org</span>
                    </div>
                  @elseif($article->user)
                    <div class="flex items-center gap-2">
                      @if($article->user->avatar_url)
                        <img src="{{ $article->user->avatar_url }}" alt="{{ $article->user->name }}" class="w-6 h-6 rounded-full object-cover" />
                      @else
                        <div class="w-6 h-6 rounded-full bg-accent/20 flex items-center justify-center text-xs font-bold text-accent">
                          {{ strtoupper(substr($article->user->name ?? 'U', 0, 1)) }}
                        </div>
                      @endif
                      <span class="text-sm">{{ $article->user->name ?? 'Unknown User' }}</span>
                      <span class="px-2 py-0.5 text-xs font-medium rounded-full bg-accent/10 text-accent">User</span>
                    </div>
                  @endif

                  {{-- Date --}}
                  <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ $article->created_at->format('M d, Y') }}</span>
                  </div>

                  {{-- Updated --}}
                  @if($article->updated_at && $article->updated_at->ne($article->created_at))
                    <div class="flex items-center gap-1.5 text-sm text-muted-foreground">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                      </svg>
                      <span>Updated {{ $article->updated_at->diffForHumans() }}</span>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            {{-- Delete Modal --}}
            <div x-show="showDeleteModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="showDeleteModal = false">
              <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showDeleteModal = false"></div>
              <div class="relative glass-card rounded-2xl p-6 w-full max-w-md" @click.stop>
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-12 h-12 rounded-full bg-destructive/10 flex items-center justify-center">
                    <svg class="w-6 h-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                  </div>
                  <div>
                    <h3 class="text-lg font-bold">Delete Article</h3>
                    <p class="text-sm text-muted-foreground">This action cannot be undone.</p>
                  </div>
                </div>
                <p class="text-sm text-muted-foreground mb-4">
                  Are you sure you want to delete <strong class="text-foreground">"{{ Str::limit($article->title, 50) }}"</strong>?
                </p>
                <form method="POST" action="{{ route('admin.articles.delete', $article->id) }}">
                  @csrf
                  @method('DELETE')
                  <div class="flex justify-end gap-3">
                    <button type="button" @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium rounded-xl border border-border hover:bg-muted transition-all">
                      Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium rounded-xl bg-destructive text-destructive-foreground hover:opacity-90 transition-all">
                      Delete Article
                    </button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      {{-- Pagination --}}
      @if($articles->hasPages())
        <div class="p-4 border-t border-border">
          {{ $articles->withQueryString()->links() }}
        </div>
      @endif
    @else
      <div class="p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted/50 flex items-center justify-center">
          <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold mb-1">No Articles Found</h3>
        <p class="text-sm text-muted-foreground">
          @if(request('search'))
            No articles match your search criteria.
          @else
            There are no articles published yet.
          @endif
        </p>
      </div>
    @endif
  </div>
@endsection
