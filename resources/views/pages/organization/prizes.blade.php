@extends('layouts.organization')

@section('content')
@php
$title = 'Pemberian Hadiah';
$subtitle = 'Atur pembagian hadiah kepada pemenang quest';
@endphp
  <div class="space-y-6" x-data="{
    quests: {{ Js::from($quests) }},
    searchQuery: '',
    statusFilter: 'all',
    expandedQuests: [],
    selectedQuest: null,
    distributeModalOpen: false,
    isDistributing: false,
    distributingTo: null,
    
    get filteredQuests() {
      let filtered = this.quests;
      
      // Filter by status
      if (this.statusFilter === 'pending') {
        filtered = filtered.filter(q => this.getPendingCount(q) > 0);
      } else if (this.statusFilter === 'completed') {
        filtered = filtered.filter(q => this.getPendingCount(q) === 0 && q.winners.length > 0);
      }
      
      // Filter by search
      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(q => 
          q.title.toLowerCase().includes(query)
        );
      }
      
      return filtered;
    },
    
    get totalWinners() {
      return this.quests.reduce((sum, q) => sum + q.winners.length, 0);
    },
    
    get pendingDistributions() {
      return this.quests.reduce((sum, q) => sum + this.getPendingCount(q), 0);
    },
    
    get completedDistributions() {
      return this.totalWinners - this.pendingDistributions;
    },
    
    get failedDistributions() {
      return this.quests.reduce((sum, q) => sum + this.getFailedCount(q), 0);
    },
    
    getPendingCount(quest) {
      return quest.winners.filter(w => w.prize_status === 'PENDING').length;
    },
    
    getDistributedCount(quest) {
      return quest.winners.filter(w => w.prize_status === 'DISTRIBUTED').length;
    },
    
    getFailedCount(quest) {
      return quest.winners.filter(w => w.prize_status === 'FAILED').length;
    },
    
    toggleQuest(questId) {
      const index = this.expandedQuests.indexOf(questId);
      if (index > -1) {
        this.expandedQuests.splice(index, 1);
      } else {
        this.expandedQuests.push(questId);
      }
    },
    
    isQuestExpanded(questId) {
      return this.expandedQuests.includes(questId);
    },
    
    openDistributeModal(quest) {
      this.selectedQuest = quest;
      this.distributeModalOpen = true;
    },
    
    async distributeAllPrizes() {
      if (this.isDistributing || !this.selectedQuest) return;
      
      this.isDistributing = true;
      
      try {
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        const response = await fetch('{{ route('organization.prizes.distribute') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            quest_id: this.selectedQuest.id
          })
        });
        
        const data = await response.json();
        
        if (data.success) {
          // Update winners status and store tx_hash
          const questIndex = this.quests.findIndex(q => q.id === this.selectedQuest.id);
          if (questIndex > -1) {
            this.quests[questIndex].winners = this.quests[questIndex].winners.map(w => ({
              ...w,
              prize_status: 'DISTRIBUTED',
              tx_hash: data.transaction_hash,
              distributed_at: new Date().toISOString()
            }));
          }
          
          // Show success with transaction hash
          const txMessage = data.transaction_hash 
            ? `${data.message} Transaction: ${data.transaction_hash.substring(0, 10)}...`
            : data.message;
          this.showToast('Success', txMessage, 'success');
          this.distributeModalOpen = false;
        } else {
          this.showToast('Error', data.message || 'Failed to distribute prizes', 'error');
        }
      } catch (error) {
        console.error('Distribution error:', error);
        this.showToast('Error', 'An error occurred while distributing prizes', 'error');
      } finally {
        this.isDistributing = false;
      }
    },
    
    async retryDistribution(winner, questId) {
      if (this.distributingTo === winner.id) return;
      
      this.distributingTo = winner.id;
      
      try {
        const csrfToken = document.querySelector('meta[name=csrf-token]').content;
        const response = await fetch('{{ route('organization.prizes.distribute') }}', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
          },
          body: JSON.stringify({
            quest_id: questId,
            winner_id: winner.id
          })
        });
        
        const data = await response.json();
        
        if (response.ok) {
          // Update specific winner status
          const questIndex = this.quests.findIndex(q => q.id === questId);
          if (questIndex > -1) {
            const winnerIndex = this.quests[questIndex].winners.findIndex(w => w.id === winner.id);
            if (winnerIndex > -1) {
              this.quests[questIndex].winners[winnerIndex].prize_status = 'DISTRIBUTED';
              this.quests[questIndex].winners[winnerIndex].distributed_at = new Date().toISOString();
            }
          }
          
          this.showToast('Success', 'Hadiah berhasil dibagikan!', 'success');
        } else {
          this.showToast('Error', data.message || 'Gagal membagikan hadiah', 'error');
        }
      } catch (error) {
        console.error('Distribution error:', error);
        this.showToast('Error', 'An error occurred while distributing prize', 'error');
      } finally {
        this.distributingTo = null;
      }
    },
    
    copyWalletAddress(address) {
      navigator.clipboard.writeText(address);
      this.showToast('Copied', 'Wallet address berhasil disalin ke clipboard', 'success');
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
    
    getStatusBadge(status) {
      const badges = {
        'PENDING': { class: 'bg-highlight/20 text-highlight-foreground', text: 'Pending' },
        'DISTRIBUTED': { class: 'bg-primary/20 text-primary', text: 'Distributed' },
        'FAILED': { class: 'bg-destructive/20 text-destructive', text: 'Failed' }
      };
      return badges[status] || badges['PENDING'];
    },
    
    showToast(title, message, type = 'success') {
      // Simple toast implementation
      const toast = document.createElement('div');
      toast.className = `fixed top-4 right-4 z-50 glass-card border ${type === 'success' ? 'border-primary' : 'border-destructive'} p-4 rounded-lg shadow-lg max-w-sm`;
      toast.innerHTML = `
        <div class='flex items-start gap-3'>
          <div class='flex-1'>
            <p class='font-semibold text-sm'>${title}</p>
            <p class='text-xs text-muted-foreground mt-1'>${message}</p>
          </div>
          <button onclick='this.parentElement.parentElement.remove()' class='text-muted-foreground hover:text-foreground'>
            <svg class='w-4 h-4' fill='none' stroke='currentColor' viewBox='0 0 24 24'>
              <path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M6 18L18 6M6 6l12 12'/>
            </svg>
          </button>
        </div>
      `;
      document.body.appendChild(toast);
      setTimeout(() => toast.remove(), 5000);
    }
  }">
    
    {{-- Statistics Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
      <div class="glass-card p-4 text-center">
        <svg class="w-6 h-6 text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
        </svg>
        <p class="text-2xl font-bold" x-text="totalWinners"></p>
        <p class="text-xs text-muted-foreground">Total Pemenang</p>
      </div>
      
      <div class="glass-card p-4 text-center">
        <svg class="w-6 h-6 text-highlight mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-2xl font-bold" x-text="pendingDistributions"></p>
        <p class="text-xs text-muted-foreground">Pending</p>
      </div>
      
      <div class="glass-card p-4 text-center">
        <svg class="w-6 h-6 text-primary mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-2xl font-bold" x-text="completedDistributions"></p>
        <p class="text-xs text-muted-foreground">Berhasil</p>
      </div>
      
      <div class="glass-card p-4 text-center">
        <svg class="w-6 h-6 text-destructive mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <p class="text-2xl font-bold" x-text="failedDistributions"></p>
        <p class="text-xs text-muted-foreground">Gagal</p>
      </div>
    </div>
    
    {{-- Filters --}}
    <div class="flex flex-col sm:flex-row gap-4">
      <div class="relative flex-1 max-w-sm">
        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
        </svg>
        <input 
          type="text" 
          x-model="searchQuery"
          placeholder="Cari quests..." 
          class="w-full pl-9 pr-4 py-2 rounded-lg bg-muted/50 border border-border focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm"
        >
      </div>
      
      <select 
        x-model="statusFilter"
        class="px-4 py-2 rounded-lg bg-muted/50 border border-border focus:outline-none focus:ring-2 focus:ring-primary/50 text-sm w-full sm:w-48"
      >
        <option value="all">Semua Status</option>
        <option value="pending">Pending Pemberian</option>
        <option value="completed">Selesai</option>
      </select>
    </div>
    
    {{-- Quest List --}}
    <div class="space-y-4">
      <template x-if="filteredQuests.length === 0">
        <div class="glass-card py-12 text-center">
          <svg class="w-12 h-12 mx-auto mb-4 text-muted-foreground opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"/>
          </svg>
          <p class="text-muted-foreground">Tidak ada quest dengan pemenang ditemukan</p>
        </div>
      </template>
      
      <template x-for="quest in filteredQuests" :key="quest.id">
        <div class="glass-card overflow-hidden">
          {{-- Quest Header --}}
          <div class="p-4 border-b border-border">
            <div class="flex items-start justify-between gap-4">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-2">
                  <h3 class="font-semibold text-lg" x-text="quest.title"></h3>
                  <template x-if="getPendingCount(quest) > 0">
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-highlight/20 text-highlight-foreground">
                      <span x-text="getPendingCount(quest)"></span> Pending
                    </span>
                  </template>
                </div>
                <div class="flex flex-wrap items-center gap-4 text-sm text-muted-foreground">
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span x-text="quest.winners.length + ' Pemenang' + (quest.winners.length !== 1 ? 's' : '')"></span>
                  </div>
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="getDistributedCount(quest) + ' Diberikan'"></span>
                  </div>
                  <div class="flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span x-text="'Selesai ' + formatDate(quest.quest_end_at)"></span>
                  </div>
                </div>
              </div>
              
              <div class="flex items-center gap-2">
                <template x-if="getPendingCount(quest) > 0">
                  <button 
                    @click="openDistributeModal(quest)"
                    class="btn-primary text-sm px-4 py-2 rounded-lg flex items-center gap-2"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                    Berikan ke Semua
                  </button>
                </template>
                
                <button 
                  @click="toggleQuest(quest.id)"
                  class="p-2 rounded-lg hover:bg-muted/50 transition-colors"
                >
                  <svg 
                    class="w-5 h-5 transition-transform"
                    :class="isQuestExpanded(quest.id) && 'rotate-180'"
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
          
          {{-- Winners List (Expandable) --}}
          <div 
            x-show="isQuestExpanded(quest.id)"
            x-collapse
            class="divide-y divide-border"
          >
            <template x-for="winner in quest.winners" :key="winner.id">
              <div class="p-4 hover:bg-muted/30 transition-colors">
                <div class="flex flex-col sm:flex-row gap-4">
                  {{-- Avatar --}}
                  <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                    <template x-if="winner.user.avatar_url">
                      <img :src="winner.user.avatar_url" :alt="winner.user.name" class="w-full h-full rounded-full object-cover">
                    </template>
                    <template x-if="!winner.user.avatar_url">
                      <span class="text-sm font-semibold text-primary" x-text="winner.user.name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()"></span>
                    </template>
                  </div>
                  
                  {{-- Winner Info --}}
                  <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2 mb-2">
                      <div>
                        <div class="flex items-center gap-2 flex-wrap">
                          <h4 class="font-semibold" x-text="winner.user.name"></h4>
                          <span 
                            class="px-2 py-0.5 rounded-full text-xs font-medium"
                            :class="getStatusBadge(winner.prize_status).class"
                            x-text="getStatusBadge(winner.prize_status).text"
                          ></span>
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                          <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                          </svg>
                          <code class="text-xs font-mono text-muted-foreground" x-text="winner.user.wallet_address ? winner.user.wallet_address.substring(0, 6) + '...' + winner.user.wallet_address.slice(-4) : 'No wallet'"></code>
                          <template x-if="winner.user.wallet_address">
                            <button 
                              @click="copyWalletAddress(winner.user.wallet_address)"
                              class="p-1 rounded hover:bg-muted/50 transition-colors"
                            >
                              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                              </svg>
                            </button>
                          </template>
                        </div>
                      </div>
                    </div>
                    
                    {{-- Distribution Info --}}
                    <template x-if="winner.prize_status === 'DISTRIBUTED' && winner.distributed_at">
                      <div class="mt-3 p-3 rounded-lg bg-primary/5 border border-primary/10">
                        <div class="flex items-center justify-between text-sm">
                          <span class="text-muted-foreground flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Sudah Diberikan
                          </span>
                          <span class="text-xs text-muted-foreground" x-text="formatDate(winner.distributed_at)"></span>
                        </div>
                      </div>
                    </template>
                    
                    <template x-if="winner.prize_status === 'FAILED'">
                      <div class="mt-3 p-3 rounded-lg bg-destructive/5 border border-destructive/10">
                        <div class="flex items-center justify-between">
                          <div class="flex items-center gap-2 text-sm text-destructive">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Pemberian Gagal</span>
                          </div>
                          <button 
                            @click="retryDistribution(winner, quest.id)"
                            :disabled="distributingTo === winner.id"
                            class="text-sm px-3 py-1 rounded-lg bg-destructive/10 hover:bg-destructive/20 text-destructive font-medium transition-colors disabled:opacity-50"
                          >
                            <span x-show="distributingTo !== winner.id">Retry</span>
                            <span x-show="distributingTo === winner.id">Retrying...</span>
                          </button>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      </template>
    </div>
    
    {{-- Distribute Modal --}}
    <div 
      x-show="distributeModalOpen"
      x-cloak
      class="fixed inset-0 z-50 flex items-center justify-center p-4"
      @keydown.escape.window="distributeModalOpen = false"
    >
      {{-- Backdrop --}}
      <div 
        class="absolute inset-0 bg-black/50 backdrop-blur-sm"
        @click="distributeModalOpen = false"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
      ></div>
      
      {{-- Modal --}}
      <div 
        class="relative glass-card rounded-lg shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
      >
        {{-- Header --}}
        <div class="flex items-center justify-between p-6 border-b border-border">
          <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
            </svg>
            <h3 class="text-lg font-semibold">Pemberian Hadiah</h3>
          </div>
          <button 
            @click="distributeModalOpen = false"
            class="p-1 rounded-lg hover:bg-muted/50 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        
        {{-- Content --}}
        <div class="p-6 space-y-4">
          <template x-if="selectedQuest">
            <div class="space-y-4">
              <p class="text-sm text-muted-foreground">
                Berikan hadiah NFT kepada semua pending pemenang quest ini.
              </p>
              
              {{-- Quest Info --}}
              <div class="p-4 rounded-lg bg-muted/50 space-y-2">
                <div class="flex justify-between text-sm">
                  <span class="text-muted-foreground">Quest:</span>
                  <span class="font-medium" x-text="selectedQuest.title"></span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-muted-foreground">Pending Pemenang:</span>
                  <span class="font-medium" x-text="getPendingCount(selectedQuest)"></span>
                </div>
                <div class="flex justify-between text-sm">
                  <span class="text-muted-foreground">NFTs to Mint:</span>
                  <span class="font-medium text-primary" x-text="getPendingCount(selectedQuest) * (selectedQuest.prizes?.length || 1)"></span>
                </div>
              </div>
              
              {{-- Prizes Preview --}}
              <template x-if="selectedQuest.prizes && selectedQuest.prizes.length > 0">
                <div>
                  <h4 class="text-sm font-medium mb-3">Hadiah untuk Diberikan:</h4>
                  <div class="grid grid-cols-2 gap-3">
                    <template x-for="prize in selectedQuest.prizes" :key="prize.id">
                      <div class="flex items-center gap-3 p-3 rounded-lg border border-border bg-card">
                        <template x-if="prize.image_url">
                          <img 
                            :src="prize.image_url.startsWith('http') || prize.image_url.startsWith('/') ? prize.image_url : '/PrizeStorage/' + prize.image_url" 
                            :alt="prize.name"
                            class="w-12 h-12 rounded-lg object-cover border border-border"
                          >
                        </template>
                        <div class="flex-1 min-w-0">
                          <p class="text-sm font-medium truncate" x-text="prize.name"></p>
                          <span 
                            class="text-xs px-2 py-0.5 rounded-full"
                            :class="prize.type === 'CERTIFICATE' ? 'bg-primary/20 text-primary' : 'bg-highlight/20 text-highlight-foreground'"
                            x-text="prize.type"
                          ></span>
                        </div>
                      </div>
                    </template>
                  </div>
                </div>
              </template>
              
              {{-- Blockchain Info --}}
              <div class="p-4 rounded-lg bg-primary/5 border border-primary/10">
                <div class="flex items-start gap-2 text-sm">
                  <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <div>
                    <p class="font-medium">Blockchain Minting</p>
                    <p class="text-muted-foreground mt-1">
                      Ini akan mencetak NFT di jaringan zkSync Era. Setiap pemenang akan menerima semua hadiah yang telah dikonfigurasi dalam bentuk NFT.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </template>
        </div>
        
        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 p-6 border-t border-border">
          <button 
            @click="distributeModalOpen = false"
            class="px-4 py-2 rounded-lg border border-border hover:bg-muted/50 transition-colors text-sm font-medium"
          >
            Batal
          </button>
          <button 
            @click="distributeAllPrizes()"
            :disabled="isDistributing"
            class="btn-primary px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <template x-if="isDistributing">
              <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
              </svg>
            </template>
            <template x-if="!isDistributing">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
              </svg>
            </template>
            <span x-text="isDistributing ? 'Sedang diberikan...' : 'Berikan Hadiah'"></span>
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection
