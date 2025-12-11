@extends('layouts.main')

@section('title', 'Artikel - JagaBumi.id')

@section('content')
<div class="min-h-screen py-8">
    <div class="container">
      {{-- Header --}}
      <div class="mb-8 flex items-center justify-between">
          <!-- Left side (existing content) -->
          <div>
              <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary border border-primary/20 text-sm font-medium mb-3">
                  Articles
              </span>
              <h1 class="text-3xl font-bold mb-2">Learn & Get Inspired</h1>
              <p class="text-muted-foreground">
                  Discover tips, stories, and insights for sustainable living
              </p>
          </div>

          <!-- Right side button -->
          <a href="{{ route('articles.create') }}"
            class="px-4 py-2 rounded-lg bg-primary text-white font-medium shadow hover:bg-primary/90">
              + Create Article
          </a>
      </div>
      {{-- Filters --}}
      <div class="mb-8">
        <form method="GET" action="{{ route('articles.all') }}" id="filter-form" class="flex flex-col sm:flex-row gap-4">
          {{-- Search --}}
          <div class="relative flex-1">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              type="text"
              name="search"
              id="search-input"
              value="{{ request('search') }}"
              placeholder="Search articles..."
              class="w-full pl-12 pr-10 py-3 rounded-lg border border-border bg-card text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
            />
            @if(request('search'))
              <button type="button" onclick="document.getElementById('search-input').value=''; document.getElementById('filter-form').submit();" class="absolute right-4 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            @endif
          </div>
          
          <div class="flex gap-3">
            {{-- Sort Filter --}}
            <select 
              name="sort" 
              onchange="this.form.submit()"
              class="h-[50px] px-4 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all min-w-[160px]"
            >
              <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
              <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Oldest</option>
              <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
              <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
            </select>

            {{-- Clear Filters Button --}}
            @if(request('search') || (request('sort') && request('sort') !== 'newest'))
              <a href="{{ route('articles.all') }}" class="inline-flex items-center justify-center gap-2 h-[50px] px-4 rounded-lg bg-muted hover:bg-muted/80 transition-all font-medium whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Clear Filters
              </a>
            @endif
          </div>
        </form>
      </div>

      {{-- Articles Grid --}}
      @if($articles->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($articles as $article)
            <a href="{{ route('articles.single', $article->id) }}" class="block h-full">
              <div class="card-interactive overflow-hidden group h-full">
                <div class="relative overflow-hidden h-48">
                  @if($article->thumbnail)
                    <img
                      src="/storage/ArticleStorage/Thumbnail/{{ $article->thumbnail }}"
                      alt="{{ $article->title }}"
                      class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                    />
                  @else
                    <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20"></div>
                  @endif
                  <div class="absolute inset-0 bg-gradient-to-t from-background/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                </div>
                <div class="p-6 space-y-3">
                  <h3 class="font-semibold text-lg line-clamp-2 group-hover:text-primary transition-colors">
                    {{ $article->title }}
                  </h3>
                  <p class="text-sm text-muted-foreground line-clamp-3">
                    {{ Str::limit(strip_tags($article->body), 150) }}
                  </p>
                  <div class="flex items-center justify-between pt-3 border-t border-border">
                    <div class="flex items-center gap-2">
                      @if($article->organization)
                        @if($article->organization->logo_img)
                          <img src="/storage/OrganizationStorage/Logo/{{ $article->organization->logo_img }}" alt="{{ $article->organization->name }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                          <div class="w-8 h-8 rounded-full gradient-primary flex items-center justify-center">
                            <span class="text-xs font-bold text-primary-foreground">{{ substr($article->organization->name, 0, 1) }}</span>
                          </div>
                        @endif
                        <div class="text-sm">
                          <div class="font-medium">{{ $article->organization->name }}</div>
                          <div class="text-xs text-muted-foreground">{{ $article->created_at->format('d M Y') }}</div>
                        </div>
                      @elseif($article->user)
                        @if($article->user->avatar_url)
                          <img src="{{ $article->user->avatar_url }}" alt="{{ $article->user->name }}" class="w-8 h-8 rounded-full object-cover">
                        @else
                          <div class="w-8 h-8 rounded-full gradient-primary flex items-center justify-center">
                            <span class="text-xs font-bold text-primary-foreground">{{ substr($article->user->name, 0, 1) }}</span>
                          </div>
                        @endif
                        <div class="text-sm">
                          <div class="font-medium">{{ $article->user->name }}</div>
                          <div class="text-xs text-muted-foreground">{{ $article->created_at->format('d M Y') }}</div>
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            </a>
          @endforeach
        </div>

        {{-- Pagination --}}
        @if($articles->hasPages())
          <div class="flex justify-center mt-12">
            <div class="flex gap-2">
              @if($articles->onFirstPage())
                <button disabled class="h-11 px-6 rounded-lg border border-border bg-muted text-muted-foreground cursor-not-allowed font-semibold">
                  Previous
                </button>
              @else
                <a href="{{ $articles->previousPageUrl() }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
                  Previous
                </a>
              @endif

              @foreach(range(1, min(5, $articles->lastPage())) as $page)
                @if($page === $articles->currentPage())
                  <button class="h-11 px-4 min-w-[44px] rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow">
                    {{ $page }}
                  </button>
                @else
                  <a href="{{ $articles->url($page) }}" class="inline-flex items-center justify-center h-11 px-4 min-w-[44px] rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
                    {{ $page }}
                  </a>
                @endif
              @endforeach

              @if($articles->hasMorePages())
                <a href="{{ $articles->nextPageUrl() }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
                  Next
                </a>
              @else
                <button disabled class="h-11 px-6 rounded-lg border border-border bg-muted text-muted-foreground cursor-not-allowed font-semibold">
                  Next
                </button>
              @endif
            </div>
          </div>
        @endif
      @else
        {{-- Empty State --}}
        <div class="text-center py-16">
          <svg class="w-24 h-24 mx-auto mb-6 text-muted-foreground opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
          </svg>
          <h3 class="text-2xl font-bold mb-2">No Articles Found</h3>
          <p class="text-muted-foreground mb-6">
            @if(request('search') || request('sort'))
              Try adjusting your filters to find what you're looking for.
            @else
              No articles are available at the moment. Check back soon!
            @endif
          </p>
          @if(request('search') || (request('sort') && request('sort') !== 'newest'))
            <a href="{{ route('articles.all') }}" class="inline-flex items-center gap-2 h-11 px-6 rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300">
              Clear Filters
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </a>
          @endif
        </div>
      @endif
    </div>
</div>
@endsection

@push('scripts')
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
@endpush
