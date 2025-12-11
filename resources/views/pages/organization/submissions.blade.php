@extends('layouts.organization')

@section('content')
@php
$title = 'Submission Review';
$subtitle = 'Review participant submissions - Approved = Winner';
@endphp
  <div class="space-y-6" x-data="{
    activeTab: 'pending',
    searchQuery: '',
    questFilter: 'all',
    submissions: {{ Js::from($submissions) }},
    filteredSubmissions: [],
    selectedSubmission: null,
    reviewModalOpen: false,
    proofModalOpen: false,
    adminNotes: '',
    isSubmitting: false,
    
    init() {
      this.updateFilteredSubmissions();
      this.$watch('activeTab', () => this.updateFilteredSubmissions());
      this.$watch('searchQuery', () => this.updateFilteredSubmissions());
      this.$watch('questFilter', () => this.updateFilteredSubmissions());
    },
    
    updateFilteredSubmissions() {
      let filtered = this.submissions;
      
      // Filter by tab
      if (this.activeTab === 'pending') {
        filtered = filtered.filter(sub => sub.status === 'COMPLETED');
      } else if (this.activeTab === 'approved') {
        filtered = filtered.filter(sub => sub.status === 'APPROVED');
      } else if (this.activeTab === 'rejected') {
        filtered = filtered.filter(sub => sub.status === 'REJECTED');
      }
      
      // Filter by search
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(sub => 
          sub.user.name.toLowerCase().includes(query) ||
          sub.quest.title.toLowerCase().includes(query)
        );
      }
      
      // Filter by quest
      if (this.questFilter !== 'all') {
        filtered = filtered.filter(sub => sub.quest.slug === this.questFilter);
      }
      
      this.filteredSubmissions = filtered;
    },
    
    get pendingCount() {
      return this.submissions.filter(s => s.status === 'COMPLETED').length;
    },
    
    get approvedCount() {
      return this.submissions.filter(s => s.status === 'APPROVED').length;
    },
    
    get rejectedCount() {
      return this.submissions.filter(s => s.status === 'REJECTED').length;
    },
    
    get quests() {
      const uniqueQuests = [...new Map(this.submissions.map(s => [s.quest.slug, s.quest])).values()];
      return uniqueQuests;
    },
    
    getStatusBadge(status) {
      const badges = {
        'COMPLETED': { class: 'bg-highlight/20 text-highlight-foreground', icon: 'clock', text: 'Pending Review' },
        'APPROVED': { class: 'bg-primary/20 text-primary', icon: 'check-circle', text: 'Approved (Winner)' },
        'REJECTED': { class: 'bg-destructive/20 text-destructive', icon: 'x-circle', text: 'Rejected' }
      };
      return badges[status] || badges['COMPLETED'];
    },
    
    formatDate(date) {
      if (!date) return 'N/A';
      return new Date(date).toLocaleDateString('en-US', { 
        month: 'short', 
        day: 'numeric', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },
    
    openReviewModal(submission) {
      this.selectedSubmission = submission;
      this.adminNotes = submission.admin_notes || '';
      this.reviewModalOpen = true;
    },
    
    openProofModal(submission) {
      this.selectedSubmission = submission;
      this.proofModalOpen = true;
    },
    
    async approveSubmission() {
      if (this.isSubmitting) return;
      
      if (!confirm('Approve this submission? The participant will become a WINNER and eligible for prizes.')) {
        return;
      }
      
      this.isSubmitting = true;
      
      try {
        const response = await fetch('{{ route('organization.submissions.approve') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
          },
          body: JSON.stringify({
            submission_id: this.selectedSubmission.id,
            admin_notes: this.adminNotes
          })
        });
        
        const data = await response.json();
        
        if (response.ok) {
          // Update local submission
          const index = this.submissions.findIndex(s => s.id === this.selectedSubmission.id);
          if (index !== -1) {
            this.submissions[index].status = 'APPROVED';
            this.submissions[index].admin_notes = this.adminNotes;
          }
          
          this.updateFilteredSubmissions();
          this.reviewModalOpen = false;
          
          // Show success message
          this.showToast('success', 'Submission Approved', data.message || 'Participant is now a winner!');
        } else {
          this.showToast('error', 'Error', data.message || 'Failed to approve submission');
        }
      } catch (error) {
        console.error('Error:', error);
        this.showToast('error', 'Error', 'An error occurred while approving the submission');
      } finally {
        this.isSubmitting = false;
      }
    },
    
    async rejectSubmission() {
      if (this.isSubmitting) return;
      
      if (!this.adminNotes.trim()) {
        this.showToast('error', 'Notes Required', 'Please provide a reason for rejection');
        return;
      }
      
      if (!confirm('Reject this submission? This action can be undone later.')) {
        return;
      }
      
      this.isSubmitting = true;
      
      try {
        const response = await fetch('{{ route('organization.submissions.reject') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
          },
          body: JSON.stringify({
            submission_id: this.selectedSubmission.id,
            admin_notes: this.adminNotes
          })
        });
        
        const data = await response.json();
        
        if (response.ok) {
          // Update local submission
          const index = this.submissions.findIndex(s => s.id === this.selectedSubmission.id);
          if (index !== -1) {
            this.submissions[index].status = 'REJECTED';
            this.submissions[index].admin_notes = this.adminNotes;
          }
          
          this.updateFilteredSubmissions();
          this.reviewModalOpen = false;
          
          // Show success message
          this.showToast('error', 'Submission Rejected', data.message || 'Submission has been rejected');
        } else {
          this.showToast('error', 'Error', data.message || 'Failed to reject submission');
        }
      } catch (error) {
        console.error('Error:', error);
        this.showToast('error', 'Error', 'An error occurred while rejecting the submission');
      } finally {
        this.isSubmitting = false;
      }
    },
    
    showToast(type, title, message) {
      // Simple toast notification - you can enhance this
      const toast = document.createElement('div');
      toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg ${type === 'success' ? 'bg-primary text-primary-foreground' : 'bg-destructive text-destructive-foreground'} animate-slide-in`;
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

    {{-- Info Banner --}}
    <div class="glass-card rounded-xl p-4 border border-primary/20 bg-primary/5">
      <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-primary mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
          <h3 class="font-semibold text-primary mb-1">Automatic Winner Selection</h3>
          <p class="text-sm text-muted-foreground">
            When you approve a submission, the participant automatically becomes a winner and will appear in the Prize Distribution list.
          </p>
        </div>
      </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="glass-card rounded-xl p-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-highlight/10 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold" x-text="pendingCount"></p>
            <p class="text-sm text-muted-foreground">Pending Review</p>
          </div>
        </div>
      </div>
      
      <div class="glass-card rounded-xl p-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold" x-text="approvedCount"></p>
            <p class="text-sm text-muted-foreground">Winners</p>
          </div>
        </div>
      </div>
      
      <div class="glass-card rounded-xl p-4">
        <div class="flex items-center gap-4">
          <div class="w-12 h-12 rounded-xl bg-destructive/10 flex items-center justify-center flex-shrink-0">
            <svg class="w-6 h-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div>
            <p class="text-2xl font-bold" x-text="rejectedCount"></p>
            <p class="text-sm text-muted-foreground">Rejected</p>
          </div>
        </div>
      </div>
    </div>

    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="relative flex-1 max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input
          type="text"
          placeholder="Search by user or quest name..."
          x-model="searchQuery"
          class="w-full pl-9 pr-4 py-2 rounded-lg border border-border bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
        />
      </div>
      
      <select 
        x-model="questFilter"
        class="w-full sm:w-48 px-4 py-2 rounded-lg border border-border bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
      >
        <option value="all">All Quests</option>
        <template x-for="quest in quests" :key="quest.slug">
          <option :value="quest.slug" x-text="quest.title"></option>
        </template>
      </select>
    </div>

    {{-- Tabs --}}
    <div class="glass-card rounded-xl overflow-hidden">
      <div class="flex border-b border-border overflow-x-auto">
        <button
          @click="activeTab = 'pending'"
          :class="activeTab === 'pending' ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground hover:text-foreground'"
          class="px-6 py-3 font-medium transition-colors whitespace-nowrap"
        >
          <span>Pending Review</span>
          <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-highlight/20 text-highlight-foreground" x-text="pendingCount"></span>
        </button>
        <button
          @click="activeTab = 'approved'"
          :class="activeTab === 'approved' ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground hover:text-foreground'"
          class="px-6 py-3 font-medium transition-colors whitespace-nowrap"
        >
          <span>Winners</span>
          <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-primary/20 text-primary" x-text="approvedCount"></span>
        </button>
        <button
          @click="activeTab = 'rejected'"
          :class="activeTab === 'rejected' ? 'border-b-2 border-primary text-primary' : 'text-muted-foreground hover:text-foreground'"
          class="px-6 py-3 font-medium transition-colors whitespace-nowrap"
        >
          <span>Rejected</span>
          <span class="ml-2 px-2 py-0.5 text-xs rounded-full bg-destructive/20 text-destructive" x-text="rejectedCount"></span>
        </button>
      </div>
    </div>

    {{-- Submissions List --}}
    <div class="space-y-4">
      <template x-if="filteredSubmissions.length === 0">
        <div class="glass-card rounded-xl p-12 text-center">
          <svg class="w-12 h-12 mx-auto mb-4 text-muted-foreground opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-muted-foreground">No submissions found</p>
        </div>
      </template>

      <template x-for="submission in filteredSubmissions" :key="submission.id">
        <div class="glass-card rounded-xl p-4 hover-lift transition-all duration-300">
          <div class="flex flex-col sm:flex-row gap-4">
            {{-- User Avatar --}}
            <div class="flex-shrink-0">
              <template x-if="submission.user.avatar_url">
                <img :src="submission.user.avatar_url" :alt="submission.user.name" class="w-12 h-12 rounded-full object-cover">
              </template>
              <template x-if="!submission.user.avatar_url">
                <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center">
                  <span class="text-sm font-semibold text-primary" x-text="submission.user.name.substring(0, 2).toUpperCase()"></span>
                </div>
              </template>
            </div>

            {{-- Submission Details --}}
            <div class="flex-1 min-w-0">
              <div class="flex items-start justify-between gap-2 mb-2 flex-wrap">
                <div>
                  <div class="flex items-center gap-2 flex-wrap mb-1">
                    <h3 class="font-semibold" x-text="submission.user.name"></h3>
                    <span 
                      class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium"
                      :class="getStatusBadge(submission.status).class"
                    >
                      <svg x-show="getStatusBadge(submission.status).icon === 'clock'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <svg x-show="getStatusBadge(submission.status).icon === 'check-circle'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <svg x-show="getStatusBadge(submission.status).icon === 'x-circle'" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      <span x-text="getStatusBadge(submission.status).text"></span>
                    </span>
                  </div>
                  <p class="text-sm text-muted-foreground flex items-center gap-1 mt-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                    </svg>
                    <span x-text="submission.quest.title"></span>
                  </p>
                </div>
                <span class="text-xs text-muted-foreground flex items-center gap-1 whitespace-nowrap">
                  <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                  <span x-text="formatDate(submission.submission_date)"></span>
                </span>
              </div>

              {{-- Proof Description --}}
              <p class="text-sm text-muted-foreground line-clamp-2 mb-3" x-text="submission.proof_description"></p>

              {{-- Proof Image Preview --}}
              <template x-if="submission.proof_image_url">
                <button 
                  @click="openProofModal(submission)"
                  class="mb-3 rounded-lg overflow-hidden border border-border hover:border-primary transition-all group relative max-w-xs"
                >
                  <img :src="submission.proof_image_url" alt="Proof" class="w-full h-32 object-cover group-hover:scale-105 transition-transform duration-300">
                  <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                    <svg class="w-8 h-8 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                  </div>
                </button>
              </template>

              {{-- Admin Notes (if any) --}}
              <template x-if="submission.admin_notes && submission.status !== 'COMPLETED'">
                <div class="mb-3 p-3 rounded-lg bg-muted/50 text-sm">
                  <p class="font-medium text-muted-foreground mb-1">Admin Notes:</p>
                  <p class="text-foreground" x-text="submission.admin_notes"></p>
                </div>
              </template>

              {{-- Actions --}}
              <div class="flex flex-wrap items-center gap-2">
                <template x-if="submission.status === 'COMPLETED'">
                  <button
                    @click="openReviewModal(submission)"
                    class="inline-flex items-center gap-1 px-4 py-2 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 transition-colors text-sm font-medium"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    Review
                  </button>
                </template>
                <template x-if="submission.status !== 'COMPLETED'">
                  <button
                    @click="openReviewModal(submission)"
                    class="inline-flex items-center gap-1 px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors text-sm font-medium"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View Details
                  </button>
                </template>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>

    {{-- Review Modal --}}
    <div 
      x-show="reviewModalOpen"
      x-cloak
      @click.self="reviewModalOpen = false"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
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
              <h2 class="text-xl font-bold flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                Review Submission
              </h2>
              <p class="text-sm text-muted-foreground mt-1">Review the submission and select a status</p>
            </div>
            <button 
              @click="reviewModalOpen = false"
              class="rounded-full p-2 hover:bg-muted transition-colors"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>

          <template x-if="selectedSubmission">
            <div class="space-y-4">
              {{-- User Info --}}
              <div class="flex items-center gap-3 p-3 rounded-lg bg-muted/50">
                <template x-if="selectedSubmission.user.avatar_url">
                  <img :src="selectedSubmission.user.avatar_url" :alt="selectedSubmission.user.name" class="w-10 h-10 rounded-full object-cover">
                </template>
                <template x-if="!selectedSubmission.user.avatar_url">
                  <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center">
                    <span class="text-sm font-semibold text-primary" x-text="selectedSubmission.user.name.substring(0, 2).toUpperCase()"></span>
                  </div>
                </template>
                <div>
                  <p class="font-medium" x-text="selectedSubmission.user.name"></p>
                  <p class="text-sm text-muted-foreground" x-text="selectedSubmission.quest.title"></p>
                </div>
              </div>

              {{-- Submission Details --}}
              <div>
                <h4 class="text-sm font-medium mb-2">Proof Description</h4>
                <p class="text-sm text-muted-foreground p-3 rounded-lg bg-muted/50" x-text="selectedSubmission.proof_description"></p>
              </div>

              {{-- Proof Image --}}
              <template x-if="selectedSubmission.proof_image_url">
                <div>
                  <h4 class="text-sm font-medium mb-2">Proof Image</h4>
                  <img :src="selectedSubmission.proof_image_url" alt="Proof" class="w-full rounded-lg border border-border">
                </div>
              </template>

              {{-- Admin Notes Input (for pending submissions) --}}
              <template x-if="selectedSubmission.status === 'COMPLETED'">
                <div>
                  <h4 class="text-sm font-medium mb-2">Admin Notes <span class="text-destructive" x-show="activeTab === 'pending' && !adminNotes.trim()">*</span></h4>
                  <textarea
                    x-model="adminNotes"
                    rows="3"
                    placeholder="Add notes for this review (required for rejection)..."
                    class="w-full px-4 py-2 rounded-lg border border-border bg-background text-foreground focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all resize-none"
                  ></textarea>
                </div>
              </template>

              {{-- Display Admin Notes (for reviewed submissions) --}}
              <template x-if="selectedSubmission.status !== 'COMPLETED' && selectedSubmission.admin_notes">
                <div>
                  <h4 class="text-sm font-medium mb-2">Admin Notes</h4>
                  <p class="text-sm text-muted-foreground p-3 rounded-lg bg-muted/50" x-text="selectedSubmission.admin_notes"></p>
                </div>
              </template>

              {{-- Current Status --}}
              <div class="border-t pt-4">
                <h4 class="text-sm font-medium mb-3">Current Status</h4>
                <span 
                  class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-sm font-medium"
                  :class="getStatusBadge(selectedSubmission.status).class"
                  x-text="getStatusBadge(selectedSubmission.status).text"
                ></span>
              </div>

              {{-- Action Buttons (for pending submissions) --}}
              <template x-if="selectedSubmission.status === 'COMPLETED'">
                <div class="flex gap-3 pt-4 border-t">
                  <button
                    @click="approveSubmission()"
                    :disabled="isSubmitting"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-primary text-primary-foreground hover:bg-primary/90 disabled:opacity-50 disabled:cursor-not-allowed transition-all font-medium"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="isSubmitting ? 'Processing...' : 'Approve (Winner)'"></span>
                  </button>
                  <button
                    @click="rejectSubmission()"
                    :disabled="isSubmitting"
                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 rounded-lg bg-destructive text-destructive-foreground hover:bg-destructive/90 disabled:opacity-50 disabled:cursor-not-allowed transition-all font-medium"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span x-text="isSubmitting ? 'Processing...' : 'Reject'"></span>
                  </button>
                </div>
              </template>

              {{-- Close Button (for reviewed submissions) --}}
              <template x-if="selectedSubmission.status !== 'COMPLETED'">
                <div class="flex justify-end pt-4 border-t">
                  <button
                    @click="reviewModalOpen = false"
                    class="px-6 py-2 rounded-lg border border-border hover:bg-muted transition-colors font-medium"
                  >
                    Close
                  </button>
                </div>
              </template>
            </div>
          </template>
        </div>
      </div>
    </div>

    {{-- Proof Image Modal --}}
    <div 
      x-show="proofModalOpen"
      x-cloak
      @click="proofModalOpen = false"
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
      x-transition:enter="transition ease-out duration-200"
      x-transition:enter-start="opacity-0"
      x-transition:enter-end="opacity-100"
      x-transition:leave="transition ease-in duration-150"
      x-transition:leave-start="opacity-100"
      x-transition:leave-end="opacity-0"
    >
      <div 
        class="relative max-w-4xl w-full"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        @click.stop
      >
        <button 
          @click="proofModalOpen = false"
          class="absolute -top-12 right-0 rounded-full p-2 bg-white/10 hover:bg-white/20 transition-colors text-white"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
        </button>
        <template x-if="selectedSubmission && selectedSubmission.proof_image_url">
          <img :src="selectedSubmission.proof_image_url" alt="Proof" class="w-full rounded-lg shadow-2xl">
        </template>
      </div>
    </div>

  </div>

  <style>
    [x-cloak] { 
      display: none !important; 
    }
    
    .hover-lift {
      transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .hover-lift:hover {
      transform: translateY(-2px);
      box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
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
