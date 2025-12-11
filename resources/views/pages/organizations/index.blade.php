@extends('layouts.main')

@section('title', 'Organizations - JagaBumi')

@section('content')
  <div class="min-h-screen py-8">
    <div class="container">
      {{-- Header --}}
      <div class="mb-8">
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-secondary/10 text-secondary border border-secondary/20 text-sm font-medium mb-3">
          Organization Directory
        </span>
        <h1 class="text-3xl font-bold mb-2">Trusted Eco-Organizations</h1>
        <p class="text-muted-foreground">
          Discover and join organizations making real environmental impact
        </p>
      </div>

      {{-- Search --}}
      <div class="mb-8">
        <form method="GET" action="{{ route('organizations.all') }}" id="search-form" class="flex items-center gap-3 max-w-2xl">
          <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
              type="text" 
              name="search" 
              id="search-input"
              value="{{ request('search') }}" 
              placeholder="Search organizations..." 
              class="w-full h-11 pl-10 pr-10 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
            />
            @if(request('search'))
              <button type="button" onclick="document.getElementById('search-input').value=''; document.getElementById('search-form').submit();" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            @endif
          </div>
          @if(request('search'))
            <a href="{{ route('organizations.all') }}" class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-lg bg-muted hover:bg-muted/80 transition-all font-medium whitespace-nowrap">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
              Clear Search
            </a>
          @endif
        </form>
      </div>

      {{-- Organizations Grid --}}
      @if($organizations->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($organizations as $org)
            <div class="card-interactive overflow-hidden">
              <div class="p-6">
                <div class="flex items-start gap-4 mb-4">
                  @if($org->logo_img)
                    <img src="{{ asset('OrganizationStorage/Logo/' . $org->logo_img) }}" alt="{{ $org->name }}" class="w-16 h-16 rounded-2xl object-cover flex-shrink-0 bg-muted">
                  @else
                    <div class="w-16 h-16 rounded-2xl bg-muted flex items-center justify-center text-3xl flex-shrink-0">
                      {{ strtoupper(substr($org->name, 0, 1)) }}
                    </div>
                  @endif
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                      <h3 class="font-semibold truncate">{{ $org->name }}</h3>
                    </div>
                    <div class="flex items-center gap-2 mt-1 text-sm text-muted-foreground">
                      <span>{{ $org->quests_count ?? 0 }} quests</span>
                    </div>
                  </div>
                </div>

                <p class="text-sm text-muted-foreground mb-4 line-clamp-2">
                  {{ $org->motto ?? 'No description available' }}
                </p>

                <div class="flex flex-wrap gap-3 text-sm text-muted-foreground mb-4">
                  <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    {{ $org->quests_count ?? 0 }} quests
                  </span>
                  <span class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ $org->members_count ?? 0 }}
                  </span>
                </div>

                <div class="flex gap-2">
                  @if($org->website_url)
                    <a href="{{ $org->website_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 flex-1 h-9 px-4 rounded-lg border border-border hover:bg-muted transition-all duration-300 text-sm font-semibold">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                      </svg>
                      Website
                    </a>
                  @else
                    <button disabled class="inline-flex items-center justify-center flex-1 h-9 px-4 rounded-lg border border-border bg-muted text-muted-foreground cursor-not-allowed text-sm font-semibold">
                      View Profile
                    </button>
                  @endif
                  @if($org->instagram_url)
                    <a href="{{ $org->instagram_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center w-9 h-9 rounded-lg border border-border hover:bg-muted transition-all duration-300">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                      </svg>
                    </a>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>

        {{-- Pagination --}}
        @if($organizations->hasPages())
          <div class="flex justify-center mt-12">
            <div class="flex gap-2">
              @if($organizations->onFirstPage())
                <button disabled class="h-11 px-6 rounded-lg border border-border bg-muted text-muted-foreground cursor-not-allowed font-semibold">
                  Previous
                </button>
              @else
                <a href="{{ $organizations->previousPageUrl() }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
                  Previous
                </a>
              @endif

              @foreach(range(1, min(5, $organizations->lastPage())) as $page)
                @if($page === $organizations->currentPage())
                  <button class="h-11 px-4 min-w-[44px] rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow">
                    {{ $page }}
                  </button>
                @else
                  <a href="{{ $organizations->url($page) }}" class="inline-flex items-center justify-center h-11 px-4 min-w-[44px] rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
                    {{ $page }}
                  </a>
                @endif
              @endforeach

              @if($organizations->hasMorePages())
                <a href="{{ $organizations->nextPageUrl() }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <h3 class="text-2xl font-bold mb-2">No Organizations Found</h3>
          <p class="text-muted-foreground mb-6">
            @if(request('search'))
              Try adjusting your search to find what you're looking for.
            @else
              No organizations are available at the moment. Check back soon!
            @endif
          </p>
          @if(request('search'))
            <a href="{{ route('organizations.all') }}" class="inline-flex items-center gap-2 h-11 px-6 rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300">
              Clear Search
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
