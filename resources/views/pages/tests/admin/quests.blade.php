@extends('layouts.admin')

@php
$title = 'Quest Review';
$subtitle = 'Review and manage quest submissions from organizations';
@endphp

@section('content')
  {{-- Header with Filter --}}
  <div class="glass-card rounded-xl p-5 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-secondary/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
          </svg>
        </div>
        <div>
          <h2 class="font-semibold">All Quests</h2>
          <p class="text-sm text-muted-foreground">{{ $quests->total() }} total quests</p>
        </div>
      </div>

      {{-- Filter Tabs --}}
      <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.quests') }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ !request('status') || request('status') === 'all' ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
          All
        </a>
        <a href="{{ route('admin.quests', ['status' => 'IN_REVIEW']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') === 'IN_REVIEW' ? 'bg-highlight text-highlight-foreground' : 'hover:bg-muted' }}">
          In Review
          @php $reviewCount = \App\Models\Quest::where('status', 'IN REVIEW')->count(); @endphp
          @if($reviewCount > 0)
            <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ request('status') === 'IN_REVIEW' ? 'bg-white/20' : 'bg-highlight/20 text-highlight' }}">{{ $reviewCount }}</span>
          @endif
        </a>
        <a href="{{ route('admin.quests', ['status' => 'APPROVED']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') === 'APPROVED' ? 'bg-blue-500 text-white' : 'hover:bg-muted' }}">
          Approved
        </a>
        <a href="{{ route('admin.quests', ['status' => 'ACTIVE']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') === 'ACTIVE' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted' }}">
          Active
        </a>
        <a href="{{ route('admin.quests', ['status' => 'ENDED']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') === 'ENDED' ? 'bg-muted text-foreground' : 'hover:bg-muted' }}">
          Ended
        </a>
        <a href="{{ route('admin.quests', ['status' => 'REJECTED']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') === 'REJECTED' ? 'bg-destructive text-destructive-foreground' : 'hover:bg-muted' }}">
          Rejected
        </a>
      </div>
    </div>
  </div>

  {{-- Quests List --}}
  @if($quests->count() > 0)
    <div class="space-y-4">
      @foreach($quests as $quest)
        <div x-data="{ expanded: false, showApproveModal: false, showRejectModal: false }" class="glass-card rounded-xl overflow-hidden">
          {{-- Quest Header --}}
          <div class="p-5">
            <div class="flex items-start gap-4">
              {{-- Banner/Logo --}}
              @if($quest->banner_url)
                <img src="{{ $quest->banner_url }}" alt="{{ $quest->title }}" class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
              @elseif($quest->organization && $quest->organization->logo_img)
                <img src="{{ $quest->organization->logo_img }}" alt="{{ $quest->organization->name }}" class="w-20 h-20 rounded-xl object-cover flex-shrink-0">
              @else
                <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-secondary/20 to-accent/20 flex items-center justify-center text-secondary font-bold text-xl flex-shrink-0">
                  {{ strtoupper(substr($quest->title, 0, 2)) }}
                </div>
              @endif

              {{-- Main Content --}}
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <div class="flex items-center gap-2 flex-wrap">
                      <h3 class="font-bold text-lg">{{ $quest->title }}</h3>
                      @if($quest->status === 'IN REVIEW')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-highlight/10 text-highlight">In Review</span>
                      @elseif($quest->status === 'APPROVED')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-blue-500/10 text-blue-600">Approved</span>
                      @elseif($quest->status === 'ACTIVE')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-primary/10 text-primary">Active</span>
                      @elseif($quest->status === 'ENDED')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-muted text-muted-foreground">Ended</span>
                      @elseif($quest->status === 'REJECTED')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-destructive/10 text-destructive">Rejected</span>
                      @elseif($quest->status === 'CANCELLED')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-destructive/10 text-destructive">Cancelled</span>
                      @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-muted text-muted-foreground">{{ $quest->status }}</span>
                      @endif
                    </div>
                    <p class="text-sm text-muted-foreground mt-1">
                      by <span class="font-medium text-foreground">{{ $quest->organization->name ?? 'N/A' }}</span>
                    </p>
                  </div>
                  <p class="text-xs text-muted-foreground whitespace-nowrap">{{ $quest->created_at->diffForHumans() }}</p>
                </div>

                {{-- Quick Info --}}
                <div class="flex flex-wrap gap-4 mt-3 text-sm">
                  <div class="flex items-center gap-1.5 text-muted-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    {{ $quest->location_name }}
                  </div>
                  <div class="flex items-center gap-1.5 text-muted-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    {{ $quest->participant_limit }} participants
                  </div>
                  <div class="flex items-center gap-1.5 text-muted-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                    </svg>
                    {{ $quest->winner_limit }} winners
                  </div>
                  @if($quest->prizes->count() > 0)
                    <div class="flex items-center gap-1.5 text-muted-foreground">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                      </svg>
                      {{ $quest->prizes->count() }} prizes
                    </div>
                  @endif
                </div>

                <p class="text-sm text-muted-foreground mt-3 line-clamp-2">{{ $quest->desc }}</p>

                {{-- Quick Actions --}}
                <div class="flex items-center gap-3 mt-4 flex-wrap">
                  <button @click="expanded = !expanded" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline">
                    <svg class="w-4 h-4 transition-transform" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span x-text="expanded ? 'Hide Details' : 'View Details'"></span>
                  </button>
                  
                  <a href="{{ route('quests.detail', $quest->slug) }}" target="_blank" class="inline-flex items-center gap-1.5 text-sm font-medium text-muted-foreground hover:text-foreground">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    View Public Page
                  </a>
                  
                  @if($quest->status === 'IN REVIEW')
                    <button @click="showApproveModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-primary/10 text-primary hover:bg-primary/20 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                      </svg>
                      Approve
                    </button>
                    <button @click="showRejectModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-destructive/10 text-destructive hover:bg-destructive/20 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                      </svg>
                      Reject
                    </button>
                  @endif
                </div>
              </div>
            </div>
          </div>

          {{-- Expanded Details --}}
          <div x-show="expanded" x-collapse class="border-t border-border">
            <div class="p-5 space-y-5 bg-muted/30">
              {{-- Full Description --}}
              <div>
                <h4 class="text-sm font-semibold text-muted-foreground mb-2">Full Description</h4>
                <p class="text-sm whitespace-pre-line">{{ $quest->desc }}</p>
              </div>

              {{-- Timeline --}}
              <div>
                <h4 class="text-sm font-semibold text-muted-foreground mb-3">Timeline</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div class="p-3 rounded-lg bg-background border border-border">
                    <p class="text-xs text-muted-foreground mb-1">Registration Period</p>
                    <p class="text-sm font-medium">{{ $quest->registration_start_at->format('M d, Y H:i') }}</p>
                    <p class="text-xs text-muted-foreground">to {{ $quest->registration_end_at->format('M d, Y H:i') }}</p>
                  </div>
                  <div class="p-3 rounded-lg bg-background border border-border">
                    <p class="text-xs text-muted-foreground mb-1">Quest Period</p>
                    <p class="text-sm font-medium">{{ $quest->quest_start_at->format('M d, Y H:i') }}</p>
                    <p class="text-xs text-muted-foreground">to {{ $quest->quest_end_at->format('M d, Y H:i') }}</p>
                  </div>
                  <div class="p-3 rounded-lg bg-background border border-border">
                    <p class="text-xs text-muted-foreground mb-1">Judging Period</p>
                    <p class="text-sm font-medium">{{ $quest->judging_start_at->format('M d, Y H:i') }}</p>
                    <p class="text-xs text-muted-foreground">to {{ $quest->judging_end_at->format('M d, Y H:i') }}</p>
                  </div>
                </div>
                <div class="mt-3 p-3 rounded-lg bg-primary/5 border border-primary/20">
                  <p class="text-xs text-muted-foreground mb-1">Prize Distribution Date</p>
                  <p class="text-sm font-medium text-primary">{{ $quest->prize_distribution_date->format('M d, Y') }}</p>
                </div>
              </div>

              {{-- Location Details --}}
              <div>
                <h4 class="text-sm font-semibold text-muted-foreground mb-2">Location Details</h4>
                <div class="p-4 rounded-xl bg-background border border-border">
                  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                      <p class="text-muted-foreground">Location Name</p>
                      <p class="font-medium">{{ $quest->location_name }}</p>
                    </div>
                    <div>
                      <p class="text-muted-foreground">Coordinates</p>
                      <p class="font-medium font-mono text-xs">{{ $quest->latitude }}, {{ $quest->longitude }}</p>
                    </div>
                    <div>
                      <p class="text-muted-foreground">Radius</p>
                      <p class="font-medium">{{ number_format($quest->radius_meter) }} meters</p>
                    </div>
                    @if($quest->liveness_code)
                      <div>
                        <p class="text-muted-foreground">Liveness Code</p>
                        <p class="font-medium font-mono">{{ $quest->liveness_code }}</p>
                      </div>
                    @endif
                  </div>
                </div>
              </div>

              {{-- Prizes --}}
              @if($quest->prizes->count() > 0)
                <div>
                  <h4 class="text-sm font-semibold text-muted-foreground mb-2">Prizes ({{ $quest->prizes->count() }})</h4>
                  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($quest->prizes as $prize)
                      <div class="p-3 rounded-lg bg-background border border-border flex items-center gap-3">
                        @if($prize->image_url)
                          <img src="{{ $prize->image_url }}" alt="{{ $prize->name }}" class="w-12 h-12 rounded-lg object-cover">
                        @else
                          <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-accent/20 to-highlight/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                          </div>
                        @endif
                        <div>
                          <p class="font-medium text-sm">{{ $prize->name }}</p>
                          <p class="text-xs text-muted-foreground">{{ $prize->type }}</p>
                        </div>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endif

              {{-- Organization Info --}}
              <div class="p-4 rounded-xl bg-background border border-border">
                <h4 class="text-sm font-semibold mb-3">Organization Information</h4>
                <div class="flex items-center gap-4">
                  @if($quest->organization && $quest->organization->logo_img)
                    <img src="{{ $quest->organization->logo_img }}" alt="{{ $quest->organization->name }}" class="w-12 h-12 rounded-lg object-cover">
                  @else
                    <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold">
                      {{ strtoupper(substr($quest->organization->name ?? 'N', 0, 2)) }}
                    </div>
                  @endif
                  <div>
                    <p class="font-semibold">{{ $quest->organization->name ?? 'N/A' }}</p>
                    @if($quest->organization && $quest->organization->slug)
                      <a href="{{ route('organizations.show', $quest->organization->slug) }}" target="_blank" class="text-sm text-primary hover:underline">View Organization</a>
                    @endif
                  </div>
                </div>
              </div>

              {{-- Timestamps --}}
              <div class="flex flex-wrap gap-6 text-sm">
                <div>
                  <p class="text-muted-foreground">Created</p>
                  <p class="font-medium">{{ $quest->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($quest->approval_date)
                  <div>
                    <p class="text-muted-foreground">Approved</p>
                    <p class="font-medium">{{ $quest->approval_date->format('M d, Y H:i') }}</p>
                  </div>
                @endif
              </div>
            </div>
          </div>

          {{-- Approve Modal --}}
          <div x-show="showApproveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showApproveModal = false"></div>
            <div class="relative bg-card rounded-2xl shadow-2xl max-w-md w-full p-6" @click.stop>
              <h3 class="text-lg font-bold mb-2">Approve Quest</h3>
              <p class="text-sm text-muted-foreground mb-4">You are about to approve <strong>{{ $quest->title }}</strong>. The organization will be notified and the quest will become available for activation.</p>
              
              <form method="POST" action="{{ route('admin.quests.approve', $quest->id) }}">
                @csrf
                <div class="flex gap-3 justify-end">
                  <button type="button" @click="showApproveModal = false" class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors font-medium">Cancel</button>
                  <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors font-medium">Approve Quest</button>
                </div>
              </form>
            </div>
          </div>

          {{-- Reject Modal --}}
          <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showRejectModal = false"></div>
            <div class="relative bg-card rounded-2xl shadow-2xl max-w-md w-full p-6" @click.stop>
              <h3 class="text-lg font-bold mb-2">Reject Quest</h3>
              <p class="text-sm text-muted-foreground mb-4">You are about to reject <strong>{{ $quest->title }}</strong>. The organization will be notified.</p>
              
              <form method="POST" action="{{ route('admin.quests.reject', $quest->id) }}">
                @csrf
                <div class="flex gap-3 justify-end">
                  <button type="button" @click="showRejectModal = false" class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors font-medium">Cancel</button>
                  <button type="submit" class="px-4 py-2 rounded-lg bg-destructive text-destructive-foreground hover:bg-destructive/90 transition-colors font-medium">Reject Quest</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($quests->hasPages())
      <div class="mt-6">
        {{ $quests->appends(request()->query())->links() }}
      </div>
    @endif
  @else
    <div class="glass-card rounded-xl p-12 text-center">
      <div class="w-20 h-20 rounded-full bg-muted mx-auto flex items-center justify-center mb-4">
        <svg class="w-10 h-10 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
        </svg>
      </div>
      <h3 class="font-semibold text-lg mb-1">No quests found</h3>
      <p class="text-muted-foreground">
        @if(request('status') && request('status') !== 'all')
          No {{ str_replace('_', ' ', strtolower(request('status'))) }} quests at the moment.
        @else
          No quests have been submitted yet.
        @endif
      </p>
      @if(request('status') && request('status') !== 'all')
        <a href="{{ route('admin.quests') }}" class="inline-flex items-center gap-2 mt-4 text-primary hover:underline font-medium">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          View all quests
        </a>
      @endif
    </div>
  @endif
@endsection
