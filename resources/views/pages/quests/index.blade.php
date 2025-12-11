@extends('layouts.main')

@section('title', 'Explore Quests - JagaBumi')

@section('content')
  <div class="min-h-screen py-8">
    <div class="container">
      {{-- Header --}}
      <div class="mb-8">
        <h1 class="text-3xl font-bold mb-2">Explore Quests</h1>
        <p class="text-muted-foreground">
          Discover environmental challenges and make a real impact
        </p>
      </div>

      {{-- Filters --}}
      <div class="mb-8">
        <form method="GET" action="{{ route('quests.all') }}" id="filter-form" class="flex flex-col lg:flex-row gap-4">
          {{-- Search --}}
          <div class="flex-1 relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input 
              type="text" 
              name="search" 
              id="search-input"
              value="{{ request('search') }}" 
              placeholder="Search quests..." 
              class="w-full h-11 pl-10 pr-10 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all"
            />
            @if(request('search'))
              <button type="button" onclick="document.getElementById('search-input').value=''; document.getElementById('filter-form').submit();" class="absolute right-3 top-1/2 -translate-y-1/2 text-muted-foreground hover:text-foreground transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
              </button>
            @endif
          </div>
          
          <div class="flex flex-wrap gap-3">
            {{-- Status Filter --}}
            <select 
              name="status" 
              onchange="this.form.submit()"
              class="h-11 px-4 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all min-w-[140px]"
            >
              <option value="">All Status</option>
              <option value="ACTIVE" {{ request('status') === 'ACTIVE' ? 'selected' : '' }}>Active</option>
              <option value="ENDED" {{ request('status') === 'ENDED' ? 'selected' : '' }}>Ended</option>
              <option value="APPROVED" {{ request('status') === 'APPROVED' ? 'selected' : '' }}>Approved</option>
              <option value="IN REVIEW" {{ request('status') === 'IN REVIEW' ? 'selected' : '' }}>In Review</option>
              <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
            </select>

            {{-- Sort Filter --}}
            <select 
              name="sort" 
              onchange="this.form.submit()"
              class="h-11 px-4 rounded-lg border border-border bg-card focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all min-w-[140px]"
            >
              <option value="newest" {{ request('sort') === 'newest' ? 'selected' : '' }}>Newest</option>
              <option value="ending_soon" {{ request('sort') === 'ending_soon' ? 'selected' : '' }}>Ending Soon</option>
            </select>

            {{-- Clear Filters Button --}}
            @if(request('search') || request('status') || (request('sort') && request('sort') !== 'newest'))
              <a href="{{ route('quests.all') }}" class="inline-flex items-center justify-center gap-2 h-11 px-4 rounded-lg bg-muted hover:bg-muted/80 transition-all font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Clear Filters
              </a>
            @endif
          </div>
        </form>
      </div>

      {{-- Quest Grid --}}
      @if($quests->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          @foreach($quests as $quest)
            <div class="card-quest overflow-hidden">
              <div class="relative h-48 overflow-hidden">
                @if($quest->banner_url)
                  <img src="{{ asset($quest->banner_url) }}" alt="{{ $quest->title }}" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                @else
                  <div class="w-full h-full bg-gradient-to-br from-primary/20 to-secondary/20 transition-transform duration-500 hover:scale-110"></div>
                @endif
              </div>
              <div class="p-5 space-y-4">
                <div>
                  <h3 class="font-semibold text-lg mb-1">{{ $quest->title }}</h3>
                  <div class="flex items-center gap-2 text-sm text-muted-foreground">
                    @if($quest->organization->logo_img)
                      <img src="{{ asset('OrganizationStorage/Logo/' . $quest->organization->logo_img) }}" alt="{{ $quest->organization->name }}" class="w-5 h-5 rounded-full object-cover">
                    @else
                      <div class="w-5 h-5 rounded-full bg-primary/10 flex items-center justify-center text-primary text-xs font-bold">
                        {{ substr($quest->organization->name ?? 'O', 0, 1) }}
                      </div>
                    @endif
                    <span>{{ $quest->organization->name ?? 'Organization' }}</span>
                  </div>
                </div>
                <p class="text-sm text-muted-foreground line-clamp-2">
                  {{ Str::limit(strip_tags($quest->desc), 100) }}
                </p>
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
          @endforeach
        </div>

        {{-- Pagination --}}
        @if($quests->hasPages())
          <div class="flex justify-center mt-12">
            <div class="flex gap-2">
              @if($quests->onFirstPage())
                <button disabled class="h-11 px-6 rounded-lg border border-border bg-muted text-muted-foreground cursor-not-allowed font-semibold">
                  Previous
                </button>
              @else
                <a href="{{ $quests->previousPageUrl() }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
                  Previous
                </a>
              @endif

              @foreach(range(1, min(5, $quests->lastPage())) as $page)
                @if($page === $quests->currentPage())
                  <button class="h-11 px-4 min-w-[44px] rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow">
                    {{ $page }}
                  </button>
                @else
                  <a href="{{ $quests->url($page) }}" class="inline-flex items-center justify-center h-11 px-4 min-w-[44px] rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
                    {{ $page }}
                  </a>
                @endif
              @endforeach

              @if($quests->hasMorePages())
                <a href="{{ $quests->nextPageUrl() }}" class="inline-flex items-center justify-center h-11 px-6 rounded-lg border border-border hover:bg-muted transition-all duration-300 font-semibold hover-lift">
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
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <h3 class="text-2xl font-bold mb-2">No Quests Found</h3>
          <p class="text-muted-foreground mb-6">
            @if(request('search') || request('status'))
              Try adjusting your filters to find what you're looking for.
            @else
              No quests are available at the moment. Check back soon!
            @endif
          </p>
          @if(request('search') || request('status'))
            <a href="{{ route('quests.all') }}" class="inline-flex items-center gap-2 h-11 px-6 rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300">
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
