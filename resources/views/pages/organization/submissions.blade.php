@extends('layouts.organization')

@section('content')
@php
$title = 'Submission Review';
$subtitle = 'Select winners from participant submissions';
$questsData = $quests ?? collect([]);
@endphp
  <div class="space-y-6" x-data="{
    activeTab: 'pending',
    searchQuery: '',
    questFilter: 'all',
    submissions: {{ Js::from($submissions) }},
    questsData: {{ Js::from($questsData) }},
    filteredSubmissions: [],
    selectedSubmission: null,
    viewModalOpen: false,
    processingIds: [],
    
    init() {
      this.updateFilteredSubmissions();
      this.$watch('activeTab', () => this.updateFilteredSubmissions());
      this.$watch('searchQuery', () => this.updateFilteredSubmissions());
      this.$watch('questFilter', () => this.updateFilteredSubmissions());
    },
    
    updateFilteredSubmissions() {
      let filtered = this.submissions;
      
      if (this.activeTab === 'pending') {
        filtered = filtered.filter(sub => sub.status === 'COMPLETED');
      } else if (this.activeTab === 'winners') {
        filtered = filtered.filter(sub => sub.status === 'APPROVED');
      }
      
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(sub => 
          sub.user.name.toLowerCase().includes(query) ||
          sub.quest.title.toLowerCase().includes(query)
        );
      }
      
      if (this.questFilter !== 'all') {
        filtered = filtered.filter(sub => sub.quest.slug === this.questFilter);
      }
      
      this.filteredSubmissions = filtered;
    },
    
    get pendingCount() {
      return this.submissions.filter(s => s.status === 'COMPLETED').length;
    },
    
    get winnersCount() {
      return this.submissions.filter(s => s.status === 'APPROVED').length;
    },
    
    get quests() {
      return this.questsData;
    },
    
    getQuestWinnerProgress(questId) {
      const quest = this.questsData.find(q => q.id === questId);
      if (!quest) return { current: 0, limit: 0 };
      return { current: quest.winners_count, limit: quest.winner_limit };
    },
    
    isWinnerLimitReached(questId) {
      const progress = this.getQuestWinnerProgress(questId);
      return progress.current >= progress.limit;
    },
    
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    openViewModal(submission) {
      this.selectedSubmission = submission;
      this.viewModalOpen = true;
    },
    
    isProcessing(id) {
      return this.processingIds.includes(id);
    },
    
    async toggleWinner(submission) {
      if (this.isProcessing(submission.id)) return;
      
      const isCurrentlyWinner = submission.status === 'APPROVED';
      
      this.processingIds.push(submission.id);
      
      try {
        const endpoint = isCurrentlyWinner 
          ? `/organization/submissions/${submission.id}/reject`
          : `/organization/submissions/${submission.id}/approve`;
          
        const response = await fetch(endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
          }
        });
        
        const data = await response.json();
        
        if (response.ok) {
          // Update local submission
          const index = this.submissions.findIndex(s => s.id === submission.id);
          if (index !== -1) {
            this.submissions[index].status = isCurrentlyWinner ? 'COMPLETED' : 'APPROVED';
          }
          
          // Update quest winner count
          if (data.winners_count !== undefined) {
            const questIndex = this.questsData.findIndex(q => q.id === submission.quest.id);
            if (questIndex !== -1) {
              this.questsData[questIndex].winners_count = data.winners_count;
            }
          } else {
            // Manually update count
            const questIndex = this.questsData.findIndex(q => q.id === submission.quest.id);
            if (questIndex !== -1) {
              this.questsData[questIndex].winners_count += isCurrentlyWinner ? -1 : 1;
            }
          }
          
          this.updateFilteredSubmissions();
          
          this.showToast('success', 
            isCurrentlyWinner ? 'Removed from Winners' : 'Winner Added',
            isCurrentlyWinner ? 'Participant returned to pending' : 'Participant is now a winner!'
          );
        } else {
          this.showToast('error', 'Error', data.message || 'Failed to update status');
        }
      } catch (error) {
        console.error('Error:', error);
        this.showToast('error', 'Error', 'An error occurred');
      } finally {
        this.processingIds = this.processingIds.filter(id => id !== submission.id);
      }
    },
    
    showToast(type, title, message) {
      const toast = document.createElement('div');
      toast.className = `fixed top-4 right-4 z-[100] p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-primary text-primary-foreground' : 'bg-destructive text-destructive-foreground'} animate-slide-in`;
      toast.innerHTML = `
        <div class='font-semibold'>${title}</div>
        <div class='text-sm opacity-90'>${message}</div>
      `;
      document.body.appendChild(toast);
      setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
      }, 3000);
    }
  }">

    {{-- Header Stats --}}
    <div class="grid grid-cols-2 gap-4">
      <div class="glass-card rounded-xl p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold" x-text="pendingCount"></p>
            <p class="text-sm text-muted-foreground">Pending Review</p>
          </div>
          <div class="w-14 h-14 rounded-xl bg-highlight/10 flex items-center justify-center">
            <svg class="w-7 h-7 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>
      
      <div class="glass-card rounded-xl p-5">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-3xl font-bold text-primary" x-text="winnersCount"></p>
            <p class="text-sm text-muted-foreground">Winners Selected</p>
          </div>
          <div class="w-14 h-14 rounded-xl bg-primary/10 flex items-center justify-center">
            <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
            </svg>
          </div>
        </div>
      </div>
    </div>

    {{-- Quest Winner Progress --}}
    <template x-if="questsData.length > 0">
      <div class="glass-card rounded-xl p-5">
        <h3 class="font-semibold mb-4 flex items-center gap-2">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
          Winner Slots by Quest
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
          <template x-for="quest in questsData" :key="quest.id">
            <div class="p-3 rounded-lg border" :class="quest.winners_count >= quest.winner_limit ? 'bg-green-500/5 border-green-500/30' : 'bg-muted/30 border-border'">
              <div class="flex items-center justify-between mb-2">
                <h4 class="font-medium text-sm truncate flex-1" x-text="quest.title"></h4>
                <span 
                  class="text-xs font-bold tabular-nums ml-2"
                  :class="quest.winners_count >= quest.winner_limit ? 'text-green-500' : 'text-primary'"
                  x-text="quest.winners_count + '/' + quest.winner_limit"
                ></span>
              </div>
              <div class="h-2 bg-muted rounded-full overflow-hidden">
                <div 
                  class="h-full rounded-full transition-all duration-500"
                  :class="quest.winners_count >= quest.winner_limit ? 'bg-green-500' : 'bg-primary'"
                  :style="'width: ' + Math.min(100, (quest.winners_count / quest.winner_limit * 100)) + '%'"
                ></div>
              </div>
            </div>
          </template>
        </div>
      </div>
    </template>

    {{-- Filters & Tabs --}}
    <div class="glass-card rounded-xl p-4">
      <div class="flex flex-col lg:flex-row gap-4 items-start lg:items-center justify-between">
        {{-- Tabs --}}
        <div class="flex gap-2">
          <button
            @click="activeTab = 'pending'"
            :class="activeTab === 'pending' ? 'bg-primary text-primary-foreground shadow-lg' : 'bg-muted/50 text-muted-foreground hover:bg-muted'"
            class="px-5 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2"
          >
            <span>Pending</span>
            <span class="px-2 py-0.5 text-xs rounded-full" :class="activeTab === 'pending' ? 'bg-white/20' : 'bg-highlight/20 text-highlight-foreground'" x-text="pendingCount"></span>
          </button>
          <button
            @click="activeTab = 'winners'"
            :class="activeTab === 'winners' ? 'bg-primary text-primary-foreground shadow-lg' : 'bg-muted/50 text-muted-foreground hover:bg-muted'"
            class="px-5 py-2.5 rounded-lg font-medium transition-all flex items-center gap-2"
          >
            <span>Winners</span>
            <span class="px-2 py-0.5 text-xs rounded-full" :class="activeTab === 'winners' ? 'bg-white/20' : 'bg-primary/20 text-primary'" x-text="winnersCount"></span>
          </button>
        </div>
        
        {{-- Search & Filter --}}
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
          <div class="relative flex-1 lg:w-64">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
              type="text"
              placeholder="Search..."
              x-model="searchQuery"
              class="w-full pl-9 pr-4 py-2 rounded-lg border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50"
            />
          </div>
          
          <select 
            x-model="questFilter"
            class="w-full sm:w-44 px-4 py-2 rounded-lg border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50"
          >
            <option value="all">All Quests</option>
            <template x-for="quest in quests" :key="quest.slug">
              <option :value="quest.slug" x-text="quest.title"></option>
            </template>
          </select>
        </div>
      </div>
    </div>

    {{-- Submissions Table --}}
    <div class="glass-card rounded-xl overflow-hidden">
      {{-- Empty State --}}
      <template x-if="filteredSubmissions.length === 0">
        <div class="p-16 text-center">
          <svg class="w-16 h-16 mx-auto mb-4 text-muted-foreground/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-muted-foreground text-lg">No submissions found</p>
          <p class="text-sm text-muted-foreground/60 mt-1">Try adjusting your filters</p>
        </div>
      </template>

      {{-- Table --}}
      <template x-if="filteredSubmissions.length > 0">
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-muted/50 border-b border-border">
              <tr>
                <th class="text-left px-5 py-4 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Participant</th>
                <th class="text-left px-5 py-4 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Quest</th>
                <th class="text-left px-5 py-4 text-xs font-semibold uppercase tracking-wider text-muted-foreground hidden md:table-cell">Submitted</th>
                <th class="text-left px-5 py-4 text-xs font-semibold uppercase tracking-wider text-muted-foreground hidden lg:table-cell">Proof</th>
                <th class="text-center px-5 py-4 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Winner</th>
                <th class="text-center px-5 py-4 text-xs font-semibold uppercase tracking-wider text-muted-foreground">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-border">
              <template x-for="submission in filteredSubmissions" :key="submission.id">
                <tr class="hover:bg-muted/30 transition-colors">
                  {{-- Participant --}}
                  <td class="px-5 py-4">
                    <div class="flex items-center gap-3">
                      <template x-if="submission.user.avatar_url">
                        <img :src="submission.user.avatar_url" :alt="submission.user.name" class="w-10 h-10 rounded-full object-cover">
                      </template>
                      <template x-if="!submission.user.avatar_url">
                        <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                          <span class="text-sm font-semibold text-primary" x-text="submission.user.name.substring(0, 2).toUpperCase()"></span>
                        </div>
                      </template>
                      <div class="min-w-0">
                        <p class="font-medium truncate" x-text="submission.user.name"></p>
                        <p class="text-xs text-muted-foreground truncate md:hidden" x-text="formatDate(submission.submission_date)"></p>
                      </div>
                    </div>
                  </td>
                  
                  {{-- Quest --}}
                  <td class="px-5 py-4">
                    <span class="text-sm" x-text="submission.quest.title"></span>
                  </td>
                  
                  {{-- Submitted Date --}}
                  <td class="px-5 py-4 hidden md:table-cell">
                    <span class="text-sm text-muted-foreground" x-text="formatDate(submission.submission_date)"></span>
                  </td>
                  
                  {{-- Proof --}}
                  <td class="px-5 py-4 hidden lg:table-cell">
                    <template x-if="submission.video_url">
                      <button 
                        @click="openViewModal(submission)"
                        class="inline-flex items-center gap-1.5 text-sm text-primary hover:text-primary/80 transition-colors"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Video
                      </button>
                    </template>
                    <template x-if="!submission.video_url">
                      <span class="text-sm text-muted-foreground">—</span>
                    </template>
                  </td>
                  
                  {{-- Winner Toggle --}}
                  <td class="px-5 py-4 text-center">
                    <button
                      @click="toggleWinner(submission)"
                      :disabled="isProcessing(submission.id)"
                      class="relative w-14 h-8 rounded-full transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-primary/50 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                      :class="submission.status === 'APPROVED' ? 'bg-primary' : 'bg-muted'"
                    >
                      <span 
                        class="absolute top-1 w-6 h-6 bg-white rounded-full shadow-md transition-all duration-300 flex items-center justify-center"
                        :class="submission.status === 'APPROVED' ? 'left-7' : 'left-1'"
                      >
                        <template x-if="isProcessing(submission.id)">
                          <svg class="w-4 h-4 text-primary animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                          </svg>
                        </template>
                        <template x-if="!isProcessing(submission.id)">
                          <svg 
                            class="w-4 h-4"
                            :class="submission.status === 'APPROVED' ? 'text-primary' : 'text-muted-foreground'"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                          >
                            <path x-show="submission.status === 'APPROVED'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            <path x-show="submission.status !== 'APPROVED'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                          </svg>
                        </template>
                      </span>
                    </button>
                  </td>
                  
                  {{-- Actions --}}
                  <td class="px-5 py-4 text-center">
                    <button
                      @click="openViewModal(submission)"
                      class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium border border-border hover:bg-muted transition-colors"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      View
                    </button>
                  </td>
                </tr>
              </template>
            </tbody>
          </table>
        </div>
      </template>
    </div>

    {{-- View Modal --}}
    <div 
      x-show="viewModalOpen"
      x-cloak
      @click.self="viewModalOpen = false"
      class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
    >
      <div 
        class="glass-card rounded-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
      >
        <div class="p-6">
          {{-- Modal Header --}}
          <div class="flex items-start justify-between mb-6">
            <div>
              <h2 class="text-xl font-bold">Submission Details</h2>
              <p class="text-sm text-muted-foreground mt-1">Review participant submission</p>
            </div>
            <button 
              @click="viewModalOpen = false"
              class="rounded-full p-2 hover:bg-muted transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <template x-if="selectedSubmission">
            <div class="space-y-5">
              {{-- User Info --}}
              <div class="flex items-center gap-4 p-4 rounded-xl bg-muted/50">
                <template x-if="selectedSubmission.user.avatar_url">
                  <img :src="selectedSubmission.user.avatar_url" :alt="selectedSubmission.user.name" class="w-14 h-14 rounded-full object-cover">
                </template>
                <template x-if="!selectedSubmission.user.avatar_url">
                  <div class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-lg font-semibold text-primary" x-text="selectedSubmission.user.name.substring(0, 2).toUpperCase()"></span>
                  </div>
                </template>
                <div class="flex-1 min-w-0">
                  <p class="font-semibold text-lg" x-text="selectedSubmission.user.name"></p>
                  <p class="text-sm text-muted-foreground" x-text="selectedSubmission.quest.title"></p>
                </div>
                <span 
                  class="px-3 py-1.5 rounded-full text-sm font-medium"
                  :class="selectedSubmission.status === 'APPROVED' ? 'bg-primary/20 text-primary' : 'bg-highlight/20 text-highlight-foreground'"
                  x-text="selectedSubmission.status === 'APPROVED' ? 'Winner' : 'Pending'"
                ></span>
              </div>

              {{-- Submission Video --}}
              <template x-if="selectedSubmission.video_url">
                <div>
                  <h4 class="text-sm font-medium mb-3">Proof Video</h4>
                  <video :src="selectedSubmission.video_url" controls class="w-full rounded-xl border border-border">
                    Your browser does not support the video tag.
                  </video>
                </div>
              </template>

              {{-- Description --}}
              <div>
                <h4 class="text-sm font-medium mb-2">Description</h4>
                <p class="text-sm text-muted-foreground p-4 rounded-xl bg-muted/50" x-text="selectedSubmission.description || 'No description provided'"></p>
              </div>

              {{-- Quick Action --}}
              <div class="flex items-center justify-between pt-4 border-t border-border">
                <span class="text-sm text-muted-foreground">Toggle winner status:</span>
                <button
                  @click="toggleWinner(selectedSubmission); viewModalOpen = false;"
                  :disabled="isProcessing(selectedSubmission.id)"
                  class="px-5 py-2.5 rounded-lg font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                  :class="selectedSubmission.status === 'APPROVED' 
                    ? 'bg-destructive/10 text-destructive hover:bg-destructive/20 border border-destructive/30' 
                    : 'bg-primary text-primary-foreground hover:bg-primary/90'"
                  x-text="selectedSubmission.status === 'APPROVED' ? 'Remove from Winners' : 'Make Winner'"
                >
                </button>
              </div>
            </div>
          </template>
        </div>
      </div>
    </div>

  </div>

  <style>
    [x-cloak] { 
      display: none !important; 
    }
    
    .animate-slide-in {
      animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }
  </style>
@endsection
