@extends('layouts.organization')

@section('content')
@php
$title = 'Quest Management';
$subtitle = 'Create and manage environmental quests for your organization';
@endphp
  <div class="max-w-6xl mx-auto space-y-6" x-data="{
    searchQuery: '',
    statusFilter: 'all',
    filteredQuests: [],
    quests: {{ Js::from($quests) }},
    
    init() {
      this.updateFilteredQuests();
      this.$watch('searchQuery', () => this.updateFilteredQuests());
      this.$watch('statusFilter', () => this.updateFilteredQuests());
    },
    
    updateFilteredQuests() {
      let filtered = this.quests;
      
      // Filter by status
      if (this.statusFilter !== 'all') {
        filtered = filtered.filter(q => q.status === this.statusFilter);
      }
      
      // Filter by search
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(q => 
          q.title.toLowerCase().includes(query) ||
          q.desc.toLowerCase().includes(query)
        );
      }
      
      this.filteredQuests = filtered;
    },
    
    getStatusColor(status) {
      const colors = {
        'IN REVIEW': 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border-yellow-500/20',
        'ACTIVE': 'bg-green-500/10 text-green-600 dark:text-green-400 border-green-500/20',
        'ENDED': 'bg-gray-500/10 text-gray-600 dark:text-gray-400 border-gray-500/20',
        'CANCELLED': 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20',
        'REJECTED': 'bg-red-500/10 text-red-600 dark:text-red-400 border-red-500/20'
      };
      return colors[status] || 'bg-muted/50 text-muted-foreground border-border';
    },
    
    async deleteQuest(questId) {
      if (!confirm('Are you sure you want to delete this quest? This action cannot be undone.')) {
        return;
      }
      
      try {
        const response = await fetch(`{{ url('/organization/quests') }}/${questId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        });
        
        const data = await response.json();
        
        if (response.ok) {
          window.location.reload();
        } else {
          alert(data.message || 'Failed to delete quest');
        }
      } catch (error) {
        alert('An error occurred. Please try again.');
      }
    }
  }">
    
    {{-- Success/Error Messages --}}
    @if(session('success'))
      <div class="glass-card rounded-xl p-4 bg-green-500/10 border border-green-500/20">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-green-600 dark:text-green-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm text-green-600 dark:text-green-400">{{ session('success') }}</p>
        </div>
      </div>
    @endif

    @if(session('error'))
      <div class="glass-card rounded-xl p-4 bg-destructive/10 border border-destructive/20">
        <div class="flex items-start gap-3">
          <svg class="w-5 h-5 text-destructive flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm text-destructive">{{ session('error') }}</p>
        </div>
      </div>
    @endif
    
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-between">
      <div class="flex-1 max-w-sm space-y-3">
        {{-- Search --}}
        <div class="relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input 
            type="text" 
            x-model="searchQuery"
            placeholder="Search quests..." 
            class="pl-9 w-full px-4 py-2.5 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all"
          >
        </div>
        
        {{-- Status Filter --}}
        <select 
          x-model="statusFilter"
          class="w-full px-4 py-2.5 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all"
        >
          <option value="all">All Statuses</option>
          <option value="IN REVIEW">In Review</option>
          <option value="ACTIVE">Active</option>
          <option value="ENDED">Ended</option>
          <option value="CANCELLED">Cancelled</option>
          <option value="REJECTED">Rejected</option>
        </select>
      </div>
      
      <a 
        href="{{ route('organization.quests.create') }}"
        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold whitespace-nowrap h-fit"
      >
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Create New Quest
      </a>
    </div>

    {{-- Quest Statistics --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="glass-card rounded-xl p-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold" x-text="quests.length">0</p>
            <p class="text-sm text-muted-foreground">Total Quests</p>
          </div>
        </div>
      </div>
      
      <div class="glass-card rounded-xl p-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-green-500/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold" x-text="quests.filter(q => q.status === 'ACTIVE').length">0</p>
            <p class="text-sm text-muted-foreground">Active</p>
          </div>
        </div>
      </div>
      
      <div class="glass-card rounded-xl p-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold" x-text="quests.filter(q => q.status === 'IN REVIEW').length">0</p>
            <p class="text-sm text-muted-foreground">In Review</p>
          </div>
        </div>
      </div>
      
      <div class="glass-card rounded-xl p-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-lg bg-secondary/10 flex items-center justify-center">
            <svg class="w-5 h-5 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold" x-text="quests.reduce((sum, q) => sum + (q.quest_participants_count || 0), 0)">0</p>
            <p class="text-sm text-muted-foreground">Total Participants</p>
          </div>
        </div>
      </div>
    </div>

    {{-- Quests List --}}
    <div class="glass-card rounded-xl overflow-hidden">
      <div class="p-6 border-b border-border">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
          </svg>
          <h2 class="text-base font-semibold">Your Quests</h2>
        </div>
      </div>

      <div class="p-6">
        <template x-if="filteredQuests.length === 0">
          <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto mb-4 text-muted-foreground opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <p class="text-muted-foreground mb-4">No quests found</p>
            <a 
              href="{{ route('organization.quests.create') }}"
              class="inline-flex items-center gap-2 px-4 py-2 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
              </svg>
              Create Your First Quest
            </a>
          </div>
        </template>

        <div class="space-y-4">
          <template x-for="quest in filteredQuests" :key="quest.id">
            <div class="p-4 rounded-xl bg-muted/50 hover:bg-muted transition-all duration-200 hover-lift">
              <div class="flex gap-4">
                {{-- Quest Banner --}}
                <template x-if="quest.banner_url">
                  <div class="w-32 h-24 rounded-lg overflow-hidden flex-shrink-0 bg-muted">
                    <img :src="quest.banner_url" :alt="quest.title" class="w-full h-full object-cover">
                  </div>
                </template>
                <template x-if="!quest.banner_url">
                  <div class="w-32 h-24 rounded-lg overflow-hidden flex-shrink-0 bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                    <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </template>
                
                {{-- Quest Info --}}
                <div class="flex-1 min-w-0">
                  <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex-1 min-w-0">
                      <h3 class="font-semibold text-lg mb-1 line-clamp-1" x-text="quest.title"></h3>
                      <p class="text-sm text-muted-foreground line-clamp-2" x-text="quest.desc"></p>
                    </div>
                    
                    {{-- Status Badge --}}
                    <span 
                      :class="getStatusColor(quest.status)"
                      class="inline-flex items-center gap-1 px-2 py-1 rounded-md text-xs font-medium border whitespace-nowrap"
                      x-text="quest.status"
                    ></span>
                  </div>
                  
                  <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground mb-3">
                    <div class="flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                      </svg>
                      <span x-text="(quest.quest_participants_count || 0) + ' participants'"></span>
                    </div>
                    
                    <div class="flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                      <span x-text="new Date(quest.quest_start_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></span>
                    </div>
                    
                    <div class="flex items-center gap-1">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                      </svg>
                      <span x-text="quest.location_name" class="line-clamp-1"></span>
                    </div>
                  </div>
                  
                  {{-- Actions --}}
                  <div class="flex items-center gap-2">
                    <a 
                      :href="`{{ url('/organization/quests') }}/${quest.id}`"
                      class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-card border border-border hover:bg-muted transition-colors text-sm font-medium"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View
                    </a>
                    
                    <template x-if="quest.status === 'IN REVIEW'">
                      <button 
                        @click="deleteQuest(quest.id)"
                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-destructive/10 border border-destructive/20 text-destructive hover:bg-destructive/20 transition-colors text-sm font-medium"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete
                      </button>
                    </template>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>
  </div>
@endsection
