@extends('layouts.organization')

@section('content')
@php
$title = 'Member Management';
$subtitle = "Manage your organization's team members";
@endphp
  <div class="max-w-4xl mx-auto space-y-6" x-data="{
    inviteModalOpen: false,
    inviteEmail: '',
    searchQuery: '',
    isSubmitting: false,
    filteredMembers: [],
    members: {{ Js::from($members) }},
    errorMessage: '',
    
    init() {
      this.updateFilteredMembers();
      this.$watch('searchQuery', () => this.updateFilteredMembers());
      this.$watch('inviteEmail', () => { this.errorMessage = ''; });
    },
    
    updateFilteredMembers() {
      if (!this.searchQuery) {
        this.filteredMembers = this.members;
      } else {
        const query = this.searchQuery.toLowerCase();
        this.filteredMembers = this.members.filter(member => 
          member.user.name.toLowerCase().includes(query) ||
          member.user.email.toLowerCase().includes(query)
        );
      }
    },
    
    async submitInvite() {
      this.errorMessage = '';
      
      if (!this.inviteEmail || !this.inviteEmail.includes('@')) {
        this.errorMessage = 'Please enter a valid email address.';
        return;
      }
      
      this.isSubmitting = true;
      
      try {
        const response = await fetch('{{ route('organization.members.invite', $organization->id) }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          },
          body: JSON.stringify({ email: this.inviteEmail })
        });
        
        const data = await response.json();
        
        if (response.ok) {
          this.inviteEmail = '';
          this.errorMessage = '';
          this.inviteModalOpen = false;
          window.location.reload();
        } else {
          this.errorMessage = data.message || 'Failed to send invitation';
        }
      } catch (error) {
        this.errorMessage = 'An error occurred. Please try again.';
      } finally {
        this.isSubmitting = false;
      }
    },
    
    async removeMember(memberId) {
      if (!confirm('Are you sure you want to remove this member? They will lose access to the organization dashboard.')) {
        return;
      }
      
      try {
        const response = await fetch('{{ route('organization.members.remove', ':memberId') }}'.replace(':memberId', memberId), {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
          }
        });
        
        const data = await response.json();
        
        if (response.ok) {
          alert(data.message || 'Member removed successfully');
          window.location.reload();
        } else {
          alert(data.message || 'Failed to remove member');
        }
      } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
      }
    }
  }">
    
    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row gap-4 justify-between">
      <div class="relative flex-1 max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input 
          type="text" 
          x-model="searchQuery"
          placeholder="Search members..." 
          class="pl-9 w-full px-4 py-2.5 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all"
        >
      </div>
      
      @if($currentUserRole === 'CREATOR')
        <button 
          @click="inviteModalOpen = true"
          class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold"
        >
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg>
          Invite Manager
        </button>
      @endif
    </div>

    {{-- Members List Card --}}
    <div class="glass-card rounded-xl overflow-hidden">
      <div class="p-6 border-b border-border">
        <div class="flex items-center gap-2 mb-1">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
          <h2 class="text-base font-semibold">Team Members (<span x-text="members.length"></span>)</h2>
        </div>
        <p class="text-sm text-muted-foreground">Manage who has access to your organization dashboard</p>
      </div>

      <div class="p-6">
        <template x-if="filteredMembers.length === 0">
          <div class="text-center py-8 text-muted-foreground">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <p>No members found</p>
          </div>
        </template>

        <div class="space-y-3">
          <template x-for="member in filteredMembers" :key="member.id">
            <div class="flex items-center gap-4 p-4 rounded-xl bg-muted/50 hover:bg-muted transition-all duration-200 hover-lift">
              {{-- Avatar --}}
              <div class="w-12 h-12 rounded-full flex-shrink-0 overflow-hidden bg-primary/10 flex items-center justify-center">
                <template x-if="member.user.avatar_url">
                  <img :src="member.user.avatar_url" :alt="member.user.name" class="w-full h-full object-cover">
                </template>
                <template x-if="!member.user.avatar_url">
                  <span class="text-sm font-semibold text-primary" x-text="member.user.name.substring(0, 2).toUpperCase()"></span>
                </template>
              </div>

              {{-- Member Info --}}
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                  <p class="font-medium" x-text="member.user.name"></p>
                  
                  {{-- Role Badge --}}
                  <template x-if="member.role === 'CREATOR'">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-highlight/10 text-highlight border border-highlight/20">
                      <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z" />
                      </svg>
                      Creator
                    </span>
                  </template>
                  <template x-if="member.role === 'MANAGER'">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-secondary/10 text-secondary border border-secondary/20">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                      </svg>
                      Manager
                    </span>
                  </template>
                  
                  {{-- Status Badge --}}
                  <template x-if="member.status === 'ACTIVE'">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-green-500/10 text-green-600 dark:text-green-400 border border-green-500/20">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Active
                    </span>
                  </template>
                  <template x-if="member.status === 'PENDING'">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs font-medium bg-yellow-500/10 text-yellow-600 dark:text-yellow-400 border border-yellow-500/20">
                      <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                      Pending
                    </span>
                  </template>
                </div>
                
                <p class="text-sm text-muted-foreground truncate" x-text="member.user.email"></p>
                <p class="text-xs text-muted-foreground mt-1">
                  <template x-if="member.status === 'ACTIVE'">
                    <span>Joined <span x-text="new Date(member.joined_at).toLocaleDateString('en-US', { month: 'short', year: 'numeric' })"></span></span>
                  </template>
                  <template x-if="member.status === 'PENDING'">
                    <span>Invitation pending</span>
                  </template>
                </p>
              </div>

              {{-- Actions (Only for non-creator roles and if current user is creator) --}}
              @if($currentUserRole === 'CREATOR')
                <template x-if="member.role !== 'CREATOR'">
                  <div class="relative flex-shrink-0" x-data="{ open: false }">
                    <button 
                      @click="open = !open"
                      @click.away="open = false"
                      class="rounded-full h-9 w-9 hover:bg-muted transition-colors flex items-center justify-center"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                      </svg>
                    </button>
                    
                    <div 
                      x-show="open"
                      x-transition:enter="transition ease-out duration-100"
                      x-transition:enter-start="transform opacity-0 scale-95"
                      x-transition:enter-end="transform opacity-100 scale-100"
                      x-transition:leave="transition ease-in duration-75"
                      x-transition:leave-start="transform opacity-100 scale-100"
                      x-transition:leave-end="transform opacity-0 scale-95"
                      class="absolute right-0 bottom-full mb-2 w-48 rounded-lg shadow-lg bg-card border border-border z-50"
                      style="display: none;"
                    >
                      <div class="py-1">
                        <template x-if="member.status === 'PENDING'">
                          <button class="w-full text-left px-4 py-2 text-sm hover:bg-muted transition-colors flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            Resend Invitation
                          </button>
                        </template>
                        <button 
                          @click="removeMember(member.id); open = false"
                          class="w-full text-left px-4 py-2 text-sm text-destructive hover:bg-destructive/10 transition-colors flex items-center gap-2"
                        >
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                          Remove Member
                        </button>
                      </div>
                    </div>
                  </div>
                </template>
              @endif
            </div>
          </template>
        </div>
      </div>
    </div>

    {{-- Invite Modal --}}
    <div 
      x-show="inviteModalOpen"
      x-cloak
      class="fixed inset-0 z-50 overflow-y-auto"
      style="display: none;"
    >
      {{-- Backdrop --}}
      <div 
        @click="inviteModalOpen = false"
        x-show="inviteModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-black/50 backdrop-blur-sm"
      ></div>

      {{-- Modal Content --}}
      <div class="flex min-h-full items-center justify-center p-4">
        <div 
          x-show="inviteModalOpen"
          x-transition:enter="transition ease-out duration-200"
          x-transition:enter-start="opacity-0 scale-95"
          x-transition:enter-end="opacity-100 scale-100"
          x-transition:leave="transition ease-in duration-150"
          x-transition:leave-start="opacity-100 scale-100"
          x-transition:leave-end="opacity-0 scale-95"
          @click.away="inviteModalOpen = false"
          class="relative w-full max-w-md glass-card rounded-xl shadow-xl"
        >
          {{-- Modal Header --}}
          <div class="p-6 border-b border-border">
            <div class="flex items-center gap-2 mb-1">
              <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
              </svg>
              <h3 class="text-lg font-semibold">Invite New Manager</h3>
            </div>
            <p class="text-sm text-muted-foreground">Send an invitation to join your organization as a manager.</p>
          </div>

          {{-- Modal Body --}}
          <div class="p-6 space-y-4">
            <div class="space-y-2">
              <label class="text-sm font-medium">Email Address</label>
              <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <input 
                  type="email"
                  x-model="inviteEmail"
                  placeholder="manager@example.com"
                  :class="errorMessage && 'border-destructive focus:ring-destructive focus:border-destructive'"
                  class="pl-9 w-full px-4 py-2.5 rounded-lg bg-muted/50 border border-border focus:ring-2 focus:ring-primary focus:border-primary text-sm transition-all"
                >
              </div>
              
              {{-- Error Message --}}
              <div 
                x-show="errorMessage"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="flex items-start gap-2 p-3 rounded-lg bg-destructive/10 border border-destructive/20"
                style="display: none;"
              >
                <svg class="w-4 h-4 text-destructive mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-destructive" x-text="errorMessage"></p>
              </div>
            </div>

            <div class="p-3 rounded-lg bg-muted/50 text-sm text-muted-foreground">
              <p class="font-medium text-foreground mb-1">Manager Permissions:</p>
              <ul class="list-disc list-inside space-y-1">
                <li>Create and manage quests</li>
                <li>Review submissions</li>
                <li>Select winners</li>
                <li>Distribute prizes</li>
              </ul>
            </div>
          </div>

          {{-- Modal Footer --}}
          <div class="p-6 border-t border-border flex items-center justify-end gap-3">
            <button 
              @click="inviteModalOpen = false"
              class="px-4 py-2 rounded-lg border border-border hover:bg-muted transition-colors font-medium"
            >
              Cancel
            </button>
            <button 
              @click="submitInvite()"
              :disabled="isSubmitting"
              :class="isSubmitting && 'opacity-50 cursor-not-allowed'"
              class="px-4 py-2.5 rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300 font-semibold"
            >
              <span x-show="!isSubmitting">Send Invitation</span>
              <span x-show="isSubmitting">Sending...</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
