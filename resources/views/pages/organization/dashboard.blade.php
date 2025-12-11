@extends('layouts.organization')

@section('content')
@php
$title = 'Dashboard';
$subtitle = "Welcome back! Here's your organization overview.";
@endphp

  {{-- Create Organization Popup --}}
  <div x-data="{ showCreateOrgDialog: false }" @keydown.escape.window="showCreateOrgDialog = false">
    {{-- Trigger Button (show only if user has no organization) --}}
    @if(!$currentOrg)
    <div class="mb-6">
      <button @click="showCreateOrgDialog = true" 
              class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create Organization
      </button>
    </div>
    @endif

    {{-- Dialog Overlay --}}
    <div x-show="showCreateOrgDialog" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50"
         @click="showCreateOrgDialog = false"
         style="display: none;">
    </div>

    {{-- Dialog Content --}}
    <div x-show="showCreateOrgDialog"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 flex items-center justify-center z-50 p-4"
         @click.away="showCreateOrgDialog = false"
         style="display: none;">
      
      <div class="glass-card rounded-2xl shadow-2xl max-w-3xl w-full max-h-[90vh] overflow-y-auto" @click.stop>
        {{-- Header --}}
        <div class="sticky top-0 bg-background/95 backdrop-blur-sm border-b border-border p-6 z-10">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="text-2xl font-bold flex items-center gap-2">
                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Create New Organization
              </h2>
              <p class="text-muted-foreground text-sm mt-1">New organizations are automatically set to "IN REVIEW" status and require admin approval.</p>
            </div>
            <button @click="showCreateOrgDialog = false" class="text-muted-foreground hover:text-foreground transition-colors">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        {{-- Form --}}
        <form action="{{ route('organization.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
          @csrf
          
          {{-- Basic Information --}}
          <div class="space-y-4">
            <h4 class="font-medium text-sm text-muted-foreground">Basic Information</h4>
            
            <div class="space-y-2">
              <label for="name" class="block font-medium text-sm">Organization Name *</label>
              <input type="text" name="name" id="name" required
                     class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                     placeholder="e.g., Green Earth Initiative">
            </div>

            <div class="space-y-2">
              <label for="handle" class="block font-medium text-sm">Handle *</label>
              <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-muted-foreground">@</span>
                <input type="text" name="handle" id="handle" required pattern="[a-zA-Z0-9_]+"
                       class="w-full pl-8 pr-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                       placeholder="organization_handle">
              </div>
              <p class="text-xs text-muted-foreground">Only letters, numbers, and underscores allowed</p>
            </div>

            <div class="space-y-2">
              <label for="org_email" class="block font-medium text-sm">Organization Email *</label>
              <input type="email" name="org_email" id="org_email" required
                     class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                     placeholder="contact@organization.com">
            </div>

            <div class="space-y-2">
              <label for="motto" class="block font-medium text-sm">Motto</label>
              <input type="text" name="motto" id="motto" maxlength="100"
                     class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                     placeholder="Your organization's inspiring motto">
            </div>

            <div class="space-y-2">
              <label for="desc" class="block font-medium text-sm">Description *</label>
              <textarea name="desc" id="desc" rows="4" required
                        class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-none"
                        placeholder="Tell us about your organization's mission, vision, and goals..."></textarea>
            </div>
          </div>

          {{-- Visual Branding --}}
          <div class="space-y-4">
            <h4 class="font-medium text-sm text-muted-foreground flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Visual Branding
            </h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="logo_img" class="block font-medium text-sm">Logo *</label>
                <input type="file" name="logo_img" id="logo_img" accept="image/*" required
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                <p class="text-xs text-muted-foreground">Square image recommended</p>
              </div>
              
              <div class="space-y-2">
                <label for="banner_img" class="block font-medium text-sm">Banner *</label>
                <input type="file" name="banner_img" id="banner_img" accept="image/*" required
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
                <p class="text-xs text-muted-foreground">Wide landscape image recommended</p>
              </div>
            </div>
          </div>

          {{-- Social Media --}}
          <div class="space-y-4">
            <h4 class="font-medium text-sm text-muted-foreground">Social Media (Optional)</h4>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div class="space-y-2">
                <label for="website_url" class="block font-medium text-sm">Website</label>
                <input type="url" name="website_url" id="website_url"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                       placeholder="https://yoursite.com">
              </div>
              
              <div class="space-y-2">
                <label for="instagram_url" class="block font-medium text-sm">Instagram</label>
                <input type="url" name="instagram_url" id="instagram_url"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                       placeholder="https://instagram.com/yourorg">
              </div>
              
              <div class="space-y-2">
                <label for="x_url" class="block font-medium text-sm">X (Twitter)</label>
                <input type="url" name="x_url" id="x_url"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                       placeholder="https://x.com/yourorg">
              </div>
              
              <div class="space-y-2">
                <label for="facebook_url" class="block font-medium text-sm">Facebook</label>
                <input type="url" name="facebook_url" id="facebook_url"
                       class="w-full px-4 py-3 bg-background border border-border rounded-xl focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                       placeholder="https://facebook.com/yourorg">
              </div>
            </div>
          </div>

          {{-- Footer Actions --}}
          <div class="flex justify-end gap-3 pt-4 border-t border-border">
            <button type="button" @click="showCreateOrgDialog = false"
                    class="px-6 py-3 rounded-lg border border-border hover:bg-muted/50 transition-colors font-medium">
              Cancel
            </button>
            <button type="submit"
                    class="px-6 py-3 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold">
              Create Organization
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Stats Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
    {{-- Active Quests --}}
    <div class="glass-card hover-lift p-4 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Active Quests</p>
          <p class="text-2xl font-bold mt-1">{{ $stats['active_quests'] }}</p>
          <div class="flex items-center gap-1 mt-2">
            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span class="text-sm font-medium text-primary">{{ $stats['total_quests'] }} total</span>
          </div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Total Participants --}}
    <div class="glass-card hover-lift p-4 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Total Participants</p>
          <p class="text-2xl font-bold mt-1">{{ number_format($stats['total_participants']) }}</p>
          <div class="flex items-center gap-1 mt-2">
            <svg class="w-4 h-4 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span class="text-sm font-medium text-secondary">Growing</span>
          </div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Prizes Distributed --}}
    <div class="glass-card hover-lift p-4 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Prizes Distributed</p>
          <p class="text-2xl font-bold mt-1">{{ $stats['prizes_distributed'] }}</p>
          <div class="flex items-center gap-1 mt-2">
            <svg class="w-4 h-4 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
            </svg>
            <span class="text-sm font-medium text-accent">Successful</span>
          </div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-accent/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
          </svg>
        </div>
      </div>
    </div>
  </div>

  {{-- Participant Growth Chart --}}
  <div class="mb-6">
    <div class="glass-card rounded-xl p-6">
      <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        <h2 class="text-base font-semibold">Participant Growth</h2>
      </div>
      <div class="h-64">
        <canvas id="participantChart"></canvas>
      </div>
    </div>
  </div>

  {{-- Recent Activity --}}
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    {{-- Recent Quests --}}
    <div class="glass-card rounded-xl p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
          </svg>
          <h2 class="text-base font-semibold">Recent Quests</h2>
        </div>
        <a href="{{ route('organization.quests.index') }}" class="text-sm text-primary hover:underline flex items-center gap-1">
          View All
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>
      <div class="space-y-3">
        @forelse($recentQuests as $quest)
          <a href="{{ route('quests.detail', $quest->slug) }}" class="flex items-center justify-between p-3 rounded-xl bg-muted/50 hover:bg-muted transition-colors">
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm truncate">{{ $quest->title }}</p>
              <div class="flex items-center gap-3 mt-1">
                <span class="text-xs text-muted-foreground flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                  </svg>
                  {{ $quest->quest_participants_count }}
                </span>
                <span class="text-xs text-muted-foreground flex items-center gap-1">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  {{ $quest->quest_end_at->format('M d') }}
                </span>
              </div>
            </div>
            @php
              $statusColors = [
                'ACTIVE' => 'bg-primary/10 text-primary',
                'IN REVIEW' => 'bg-yellow-500/10 text-yellow-600',
                'ENDED' => 'bg-muted text-muted-foreground',
              ];
            @endphp
            <span class="px-2 py-1 rounded-lg text-xs font-medium {{ $statusColors[$quest->status] ?? 'bg-muted' }}">
              {{ $quest->status }}
            </span>
          </a>
        @empty
          <div class="text-center py-8 text-muted-foreground">
            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
            <p class="text-sm">No quests yet</p>
          </div>
        @endforelse
      </div>
    </div>

    {{-- Recent Submissions --}}
    <div class="glass-card rounded-xl p-6">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <h2 class="text-base font-semibold">Recent Submissions</h2>
        </div>
        <a href="{{ route('organization.submissions') }}" class="text-sm text-primary hover:underline flex items-center gap-1">
          Review All
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
        </a>
      </div>
      <div class="space-y-3">
        @forelse($recentSubmissions as $submission)
          <div class="flex items-center gap-3 p-3 rounded-xl bg-muted/50 hover:bg-muted transition-colors">
            @if($submission->user->avatar_url)
              <img src="{{ $submission->user->avatar_url }}" alt="{{ $submission->user->name }}" class="w-10 h-10 rounded-full object-cover">
            @else
              <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                <span class="text-sm font-semibold text-primary">{{ strtoupper(substr($submission->user->name, 0, 1)) }}</span>
              </div>
            @endif
            <div class="flex-1 min-w-0">
              <p class="font-medium text-sm">{{ $submission->user->name }}</p>
              <p class="text-xs text-muted-foreground truncate">{{ $submission->quest->title }}</p>
            </div>
            <span class="text-xs text-muted-foreground">{{ $submission->submission_date->diffForHumans() }}</span>
          </div>
        @empty
          <div class="text-center py-8 text-muted-foreground">
            <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-sm">No submissions yet</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('participantChart').getContext('2d');
    const participantData = @json($participantData);
    
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: participantData.map(d => d.month),
        datasets: [{
          label: 'Participants',
          data: participantData.map(d => d.participants),
          borderColor: 'hsl(var(--secondary))',
          backgroundColor: 'hsl(var(--secondary) / 0.1)',
          tension: 0.4,
          fill: true,
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              precision: 0
            }
          }
        }
      }
    });
  </script>
  @endpush
@endsection
