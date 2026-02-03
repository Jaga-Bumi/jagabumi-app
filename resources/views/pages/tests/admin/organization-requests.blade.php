@extends('layouts.admin')

@php
$title = 'Organization Requests';
$subtitle = 'Review and manage organization registration requests';
@endphp

@section('content')
  {{-- Header with Filter --}}
  <div class="glass-card rounded-xl p-5 mb-6">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
        </div>
        <div>
          <h2 class="font-semibold">All Requests</h2>
          <p class="text-sm text-muted-foreground">{{ $requests->total() }} total requests</p>
        </div>
      </div>

      {{-- Filter Tabs --}}
      <div class="flex items-center gap-2 flex-wrap">
        <a href="{{ route('admin.organization-requests') }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ !request('status') || request('status') === 'all' ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
          All
        </a>
        <a href="{{ route('admin.organization-requests', ['status' => 'PENDING']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') === 'PENDING' ? 'bg-highlight text-highlight-foreground' : 'hover:bg-muted' }}">
          Pending
          @php $pendingCount = \App\Models\OrganizationRequest::where('status', 'PENDING')->count(); @endphp
          @if($pendingCount > 0)
            <span class="ml-1 px-1.5 py-0.5 text-xs rounded-full {{ request('status') === 'PENDING' ? 'bg-white/20' : 'bg-highlight/20 text-highlight' }}">{{ $pendingCount }}</span>
          @endif
        </a>
        <a href="{{ route('admin.organization-requests', ['status' => 'APPROVED']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') === 'APPROVED' ? 'bg-primary text-primary-foreground' : 'hover:bg-muted' }}">
          Approved
        </a>
        <a href="{{ route('admin.organization-requests', ['status' => 'REJECTED']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-all {{ request('status') === 'REJECTED' ? 'bg-destructive text-destructive-foreground' : 'hover:bg-muted' }}">
          Rejected
        </a>
      </div>
    </div>
  </div>

  {{-- Requests List --}}
  @if($requests->count() > 0)
    <div class="space-y-4">
      @foreach($requests as $req)
        <div x-data="{ expanded: false, showApproveModal: false, showRejectModal: false }" class="glass-card rounded-xl overflow-hidden">
          {{-- Request Header --}}
          <div class="p-5">
            <div class="flex items-start gap-4">
              {{-- Avatar --}}
              <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-primary font-bold text-lg flex-shrink-0">
                {{ strtoupper(substr($req->organization_name, 0, 2)) }}
              </div>

              {{-- Main Content --}}
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between gap-4">
                  <div>
                    <div class="flex items-center gap-2 flex-wrap">
                      <h3 class="font-bold text-lg">{{ $req->organization_name }}</h3>
                      @if($req->status === 'PENDING')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-highlight/10 text-highlight">Pending</span>
                      @elseif($req->status === 'APPROVED')
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-primary/10 text-primary">Approved</span>
                      @else
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-destructive/10 text-destructive">Rejected</span>
                      @endif
                    </div>
                    <p class="text-sm text-muted-foreground mt-1">
                      {{ $req->organization_type }} • by <span class="font-medium text-foreground">{{ $req->user->name }}</span> 
                      @if($req->user->handle)
                        <span class="text-muted-foreground">@{{ $req->user->handle }}</span>
                      @endif
                    </p>
                  </div>
                  <p class="text-xs text-muted-foreground whitespace-nowrap">{{ $req->created_at->diffForHumans() }}</p>
                </div>

                <p class="text-sm text-muted-foreground mt-3 line-clamp-2">{{ $req->organization_description }}</p>

                {{-- Quick Actions --}}
                <div class="flex items-center gap-3 mt-4">
                  <button @click="expanded = !expanded" class="inline-flex items-center gap-1.5 text-sm font-medium text-primary hover:underline">
                    <svg class="w-4 h-4 transition-transform" :class="expanded && 'rotate-180'" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                    <span x-text="expanded ? 'Hide Details' : 'View Details'"></span>
                  </button>
                  
                  @if($req->status === 'PENDING')
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
                <p class="text-sm whitespace-pre-line">{{ $req->organization_description }}</p>
              </div>

              {{-- Reason --}}
              <div>
                <h4 class="text-sm font-semibold text-muted-foreground mb-2">Reason for Creating</h4>
                <p class="text-sm whitespace-pre-line">{{ $req->reason }}</p>
              </div>

              {{-- Planned Activities --}}
              <div>
                <h4 class="text-sm font-semibold text-muted-foreground mb-2">Planned Activities</h4>
                <p class="text-sm whitespace-pre-line">{{ $req->planned_activities }}</p>
              </div>

              {{-- Social Links --}}
              <div>
                <h4 class="text-sm font-semibold text-muted-foreground mb-2">Social Links</h4>
                <div class="flex flex-wrap gap-2">
                  @if($req->website_url)
                    <a href="{{ $req->website_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm bg-muted hover:bg-muted/80 transition-colors">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                      </svg>
                      Website
                    </a>
                  @endif
                  @if($req->instagram_url)
                    <a href="{{ $req->instagram_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm bg-muted hover:bg-muted/80 transition-colors">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                      </svg>
                      Instagram
                    </a>
                  @endif
                  @if($req->x_url)
                    <a href="{{ $req->x_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm bg-muted hover:bg-muted/80 transition-colors">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                      </svg>
                      X / Twitter
                    </a>
                  @endif
                  @if($req->facebook_url)
                    <a href="{{ $req->facebook_url }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm bg-muted hover:bg-muted/80 transition-colors">
                      <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                      </svg>
                      Facebook
                    </a>
                  @endif
                  @if(!$req->website_url && !$req->instagram_url && !$req->x_url && !$req->facebook_url)
                    <span class="text-sm text-muted-foreground">No social links provided</span>
                  @endif
                </div>
              </div>

              {{-- User Information --}}
              <div class="p-4 rounded-xl bg-background border border-border">
                <h4 class="text-sm font-semibold mb-3">Applicant Information</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                  <div>
                    <p class="text-muted-foreground">Name</p>
                    <p class="font-medium">{{ $req->user->name }}</p>
                  </div>
                  <div>
                    <p class="text-muted-foreground">Email</p>
                    <p class="font-medium">{{ $req->user->email }}</p>
                  </div>
                  <div>
                    <p class="text-muted-foreground">Handle</p>
                    <p class="font-medium">{{ $req->user->handle ?? 'N/A' }}</p>
                  </div>
                  <div>
                    <p class="text-muted-foreground">Wallet Address</p>
                    <p class="font-medium font-mono text-xs truncate">{{ $req->user->wallet_address ?? 'N/A' }}</p>
                  </div>
                </div>
              </div>

              {{-- Timestamps & Admin Notes --}}
              <div class="flex flex-wrap gap-4 text-sm">
                <div>
                  <p class="text-muted-foreground">Submitted</p>
                  <p class="font-medium">{{ $req->created_at->format('M d, Y H:i') }}</p>
                </div>
                @if($req->responded_at)
                  <div>
                    <p class="text-muted-foreground">Responded</p>
                    <p class="font-medium">{{ $req->responded_at->format('M d, Y H:i') }}</p>
                  </div>
                @endif
              </div>

              @if($req->admin_notes)
                <div class="p-4 rounded-xl bg-muted/50 border border-border">
                  <h4 class="text-sm font-semibold mb-2">Admin Notes</h4>
                  <p class="text-sm whitespace-pre-line">{{ $req->admin_notes }}</p>
                </div>
              @endif
            </div>
          </div>

          {{-- Approve Modal --}}
          <div x-show="showApproveModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showApproveModal = false"></div>
            <div class="relative bg-card rounded-2xl shadow-2xl max-w-md w-full p-6" @click.stop>
              <h3 class="text-lg font-bold mb-2">Approve Organization Request</h3>
              <p class="text-sm text-muted-foreground mb-4">You are about to approve <strong>{{ $req->organization_name }}</strong>. The user will be able to create their organization.</p>
              
              <form method="POST" action="{{ route('admin.organization-requests.approve', $req->id) }}">
                @csrf
                <div class="mb-4">
                  <label class="block text-sm font-medium mb-2">Admin Notes (Optional)</label>
                  <textarea name="admin_notes" rows="3" 
                            class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
                            placeholder="Add any notes for this approval..."></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                  <button type="button" @click="showApproveModal = false" class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors font-medium">Cancel</button>
                  <button type="submit" class="px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors font-medium">Approve</button>
                </div>
              </form>
            </div>
          </div>

          {{-- Reject Modal --}}
          <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="showRejectModal = false"></div>
            <div class="relative bg-card rounded-2xl shadow-2xl max-w-md w-full p-6" @click.stop>
              <h3 class="text-lg font-bold mb-2">Reject Organization Request</h3>
              <p class="text-sm text-muted-foreground mb-4">You are about to reject <strong>{{ $req->organization_name }}</strong>. Please provide a reason for rejection.</p>
              
              <form method="POST" action="{{ route('admin.organization-requests.reject', $req->id) }}">
                @csrf
                <div class="mb-4">
                  <label class="block text-sm font-medium mb-2">Rejection Reason</label>
                  <textarea name="admin_notes" rows="3" required
                            class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-destructive focus:border-transparent transition-all resize-none"
                            placeholder="Explain why this request is being rejected..."></textarea>
                </div>
                <div class="flex gap-3 justify-end">
                  <button type="button" @click="showRejectModal = false" class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors font-medium">Cancel</button>
                  <button type="submit" class="px-4 py-2 rounded-lg bg-destructive text-destructive-foreground hover:bg-destructive/90 transition-colors font-medium">Reject</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Pagination --}}
    @if($requests->hasPages())
      <div class="mt-6">
        {{ $requests->appends(request()->query())->links() }}
      </div>
    @endif
  @else
    <div class="glass-card rounded-xl p-12 text-center">
      <div class="w-20 h-20 rounded-full bg-muted mx-auto flex items-center justify-center mb-4">
        <svg class="w-10 h-10 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
        </svg>
      </div>
      <h3 class="font-semibold text-lg mb-1">No requests found</h3>
      <p class="text-muted-foreground">
        @if(request('status') && request('status') !== 'all')
          No {{ strtolower(request('status')) }} organization requests at the moment.
        @else
          No organization requests have been submitted yet.
        @endif
      </p>
      @if(request('status') && request('status') !== 'all')
        <a href="{{ route('admin.organization-requests') }}" class="inline-flex items-center gap-2 mt-4 text-primary hover:underline font-medium">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
          </svg>
          View all requests
        </a>
      @endif
    </div>
  @endif
@endsection
