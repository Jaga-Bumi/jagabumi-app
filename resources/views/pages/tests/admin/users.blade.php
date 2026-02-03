@extends('layouts.admin')

@php
$title = 'Users Management';
$subtitle = 'View and manage all registered users on the platform.';
@endphp

@section('content')
  {{-- Stats Cards --}}
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    {{-- Total Users --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Total Users</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($totalUsers) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Admins --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Administrators</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($adminCount) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-highlight/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
          </svg>
        </div>
      </div>
    </div>

    {{-- Regular Users --}}
    <div class="glass-card hover-lift p-5 rounded-xl">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-muted-foreground">Regular Users</p>
          <p class="text-3xl font-bold mt-1">{{ number_format($totalUsers - $adminCount) }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center">
          <svg class="w-6 h-6 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
          </svg>
        </div>
      </div>
    </div>
  </div>

  {{-- Search & Filters --}}
  <div class="glass-card rounded-xl p-4 mb-6">
    <form method="GET" action="{{ route('admin.users') }}" class="flex flex-col md:flex-row gap-4">
      {{-- Search --}}
      <div class="flex-1 relative">
        <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        <input 
          type="text" 
          name="search" 
          value="{{ request('search') }}" 
          placeholder="Search by name, email, handle, or wallet..."
          class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50 transition-all"
        />
      </div>
      
      {{-- Role Filter --}}
      <div class="flex items-center gap-2">
        <select name="role" class="px-4 py-2.5 rounded-xl border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50">
          <option value="all" {{ request('role') === 'all' || !request('role') ? 'selected' : '' }}>All Roles</option>
          <option value="SUPER_ADMIN" {{ request('role') === 'SUPER_ADMIN' ? 'selected' : '' }}>Admin</option>
          <option value="USER" {{ request('role') === 'USER' ? 'selected' : '' }}>User</option>
        </select>
        
        <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-primary-foreground font-medium hover:opacity-90 transition-all">
          Search
        </button>
        
        @if(request('search') || (request('role') && request('role') !== 'all'))
          <a href="{{ route('admin.users') }}" class="px-4 py-2.5 rounded-xl border border-border hover:bg-muted transition-all">
            Clear
          </a>
        @endif
      </div>
    </form>
  </div>

  {{-- Users List --}}
  <div class="glass-card rounded-xl overflow-hidden">
    <div class="p-5 border-b border-border">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center">
          <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <div>
          <h2 class="font-semibold">All Users</h2>
          <p class="text-xs text-muted-foreground">{{ $users->total() }} users found</p>
        </div>
      </div>
    </div>

    @if($users->count() > 0)
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-muted/50">
            <tr>
              <th class="px-5 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">User</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">Contact</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">Wallet</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">Role</th>
              <th class="px-5 py-3 text-left text-xs font-semibold text-muted-foreground uppercase tracking-wider">Joined</th>
              <th class="px-5 py-3 text-right text-xs font-semibold text-muted-foreground uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-border">
            @foreach($users as $user)
              <tr x-data="{ showDetailModal: false, showRemoveModal: false, showAdminModal: false, userDetail: null, loading: false }" class="hover:bg-muted/30 transition-colors">
                <td class="px-5 py-4">
                  <div class="flex items-center gap-3">
                    @if($user->avatar_url)
                      <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover" />
                    @else
                      <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-primary font-bold text-sm">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                      </div>
                    @endif
                    <div>
                      <p class="font-semibold text-sm">{{ $user->name ?? 'Unnamed' }}</p>
                      @if($user->handle)
                        <p class="text-xs text-muted-foreground">{{ '@' . $user->handle }}</p>
                      @endif
                    </div>
                  </div>
                </td>
                <td class="px-5 py-4">
                  <div class="space-y-1">
                    @if($user->email)
                      <p class="text-sm">{{ $user->email }}</p>
                    @endif
                    @if($user->phone)
                      <p class="text-xs text-muted-foreground">{{ $user->phone }}</p>
                    @endif
                  </div>
                </td>
                <td class="px-5 py-4">
                  @if($user->wallet_address)
                    <div class="flex items-center gap-2">
                      <code class="text-xs bg-muted px-2 py-1 rounded font-mono">
                        {{ substr($user->wallet_address, 0, 6) }}...{{ substr($user->wallet_address, -4) }}
                      </code>
                      <button 
                        @click="navigator.clipboard.writeText('{{ $user->wallet_address }}'); $el.innerHTML = '<svg class=\'w-4 h-4 text-primary\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\' /></svg>'; setTimeout(() => { $el.innerHTML = '<svg class=\'w-4 h-4 text-muted-foreground\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\' /></svg>' }, 1500)"
                        class="p-1 hover:bg-muted rounded transition-colors"
                        title="Copy wallet address"
                      >
                        <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                      </button>
                    </div>
                  @else
                    <span class="text-xs text-muted-foreground">No wallet</span>
                  @endif
                </td>
                <td class="px-5 py-4">
                  @if($user->role === 'SUPER_ADMIN')
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-highlight/10 text-highlight">Admin</span>
                  @else
                    <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-secondary/10 text-secondary">User</span>
                  @endif
                </td>
                <td class="px-5 py-4">
                  <p class="text-sm">{{ $user->created_at->format('M d, Y') }}</p>
                  <p class="text-xs text-muted-foreground">{{ $user->created_at->diffForHumans() }}</p>
                </td>
                <td class="px-5 py-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    {{-- View Details Button --}}
                    <button 
                      @click="loading = true; fetch('{{ route('admin.users.detail', $user->id) }}').then(r => r.json()).then(data => { userDetail = data.data; loading = false; showDetailModal = true; }).catch(() => { loading = false; })"
                      class="p-2 hover:bg-muted rounded-lg transition-colors" 
                      title="View Details"
                    >
                      <svg x-show="!loading" class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg x-show="loading" class="w-5 h-5 animate-spin text-primary" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                      </svg>
                    </button>
                    
                    {{-- Toggle Admin Button - Don't show for self --}}
                    @if($user->id !== auth()->id())
                      <button 
                        @click="showAdminModal = true"
                        class="p-2 hover:bg-highlight/10 text-highlight rounded-lg transition-colors" 
                        title="{{ $user->role === 'SUPER_ADMIN' ? 'Remove Admin' : 'Make Admin' }}"
                      >
                        @if($user->role === 'SUPER_ADMIN')
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        @else
                          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                          </svg>
                        @endif
                      </button>
                    @endif
                    
                    @if($user->role !== 'SUPER_ADMIN')
                      {{-- Remove User Button --}}
                      <button 
                        @click="showRemoveModal = true"
                        class="p-2 hover:bg-destructive/10 text-destructive rounded-lg transition-colors" 
                        title="Remove User"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                      </button>
                    @endif
                  </div>

                  {{-- View Details Modal --}}
                  <div x-show="showDetailModal" x-cloak 
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    class="fixed inset-0 z-[100] overflow-y-auto" 
                    @keydown.escape.window="showDetailModal = false">
                    <div class="flex min-h-screen items-center justify-center p-4">
                      <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showDetailModal = false"></div>
                      <div x-show="showDetailModal"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 scale-95"
                        x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 scale-100"
                        x-transition:leave-end="opacity-0 scale-95"
                        class="relative glass-card rounded-2xl p-6 w-full max-w-lg shadow-2xl" @click.stop>
                      <button @click="showDetailModal = false" class="absolute top-4 right-4 p-2 hover:bg-muted rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                      </button>
                      
                      <template x-if="userDetail">
                        <div>
                          {{-- User Header --}}
                          <div class="flex items-center gap-4 mb-6">
                            <template x-if="userDetail.avatar_url">
                              <img :src="userDetail.avatar_url" :alt="userDetail.name" class="w-16 h-16 rounded-full object-cover" />
                            </template>
                            <template x-if="!userDetail.avatar_url">
                              <div class="w-16 h-16 rounded-full bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center text-primary font-bold text-xl">
                                <span x-text="(userDetail.name || 'U').substring(0, 2).toUpperCase()"></span>
                              </div>
                            </template>
                            <div>
                              <h3 class="text-xl font-bold" x-text="userDetail.name || 'Unnamed'"></h3>
                              <p class="text-sm text-muted-foreground" x-show="userDetail.handle">@<span x-text="userDetail.handle"></span></p>
                              <span 
                                class="inline-block mt-1 px-2.5 py-0.5 text-xs font-medium rounded-full"
                                :class="userDetail.role === 'SUPER_ADMIN' ? 'bg-highlight/10 text-highlight' : 'bg-secondary/10 text-secondary'"
                                x-text="userDetail.role === 'SUPER_ADMIN' ? 'Administrator' : 'User'"
                              ></span>
                            </div>
                          </div>

                          {{-- User Info --}}
                          <div class="space-y-4">
                            {{-- Bio --}}
                            <div x-show="userDetail.bio">
                              <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Bio</p>
                              <p class="text-sm" x-text="userDetail.bio"></p>
                            </div>

                            {{-- Contact --}}
                            <div class="grid grid-cols-2 gap-4">
                              <div x-show="userDetail.email">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Email</p>
                                <p class="text-sm" x-text="userDetail.email"></p>
                              </div>
                              <div x-show="userDetail.phone">
                                <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Phone</p>
                                <p class="text-sm" x-text="userDetail.phone"></p>
                              </div>
                            </div>

                            {{-- Wallet --}}
                            <div x-show="userDetail.wallet_address">
                              <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Wallet Address</p>
                              <div class="flex items-center gap-2">
                                <code class="text-xs bg-muted px-2 py-1 rounded font-mono break-all" x-text="userDetail.wallet_address"></code>
                                <button 
                                  @click="navigator.clipboard.writeText(userDetail.wallet_address)"
                                  class="p-1 hover:bg-muted rounded transition-colors flex-shrink-0"
                                  title="Copy"
                                >
                                  <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                  </svg>
                                </button>
                              </div>
                            </div>

                            {{-- Auth Provider --}}
                            <div>
                              <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Auth Provider</p>
                              <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-muted" x-text="userDetail.auth_provider || 'Unknown'"></span>
                            </div>

                            {{-- Stats --}}
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-border">
                              <div class="text-center p-3 bg-muted/50 rounded-xl">
                                <p class="text-2xl font-bold" x-text="userDetail.articles_count || 0"></p>
                                <p class="text-xs text-muted-foreground">Articles</p>
                              </div>
                              <div class="text-center p-3 bg-muted/50 rounded-xl">
                                <p class="text-2xl font-bold" x-text="userDetail.quest_participations_count || 0"></p>
                                <p class="text-xs text-muted-foreground">Quest Participations</p>
                              </div>
                            </div>

                            {{-- Member Since --}}
                            <div class="pt-4 border-t border-border">
                              <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Member Since</p>
                              <p class="text-sm"><span x-text="userDetail.created_at"></span> (<span x-text="userDetail.created_at_human"></span>)</p>
                            </div>
                          </div>

                          {{-- Close Button --}}
                          <div class="mt-6 flex justify-end">
                            <button @click="showDetailModal = false" class="px-4 py-2 text-sm font-medium rounded-xl border border-border hover:bg-muted transition-all">
                              Close
                            </button>
                          </div>
                        </div>
                      </template>
                    </div>
                    </div>
                  </div>

                  {{-- Remove User Modal --}}
                  @if($user->role !== 'SUPER_ADMIN')
                    <div x-show="showRemoveModal" x-cloak 
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-150"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="fixed inset-0 z-[100] overflow-y-auto" 
                      @keydown.escape.window="showRemoveModal = false">
                      <div class="flex min-h-screen items-center justify-center p-4">
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showRemoveModal = false"></div>
                        <div x-show="showRemoveModal"
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 scale-95"
                          x-transition:enter-end="opacity-100 scale-100"
                          x-transition:leave="transition ease-in duration-150"
                          x-transition:leave-start="opacity-100 scale-100"
                          x-transition:leave-end="opacity-0 scale-95"
                          class="relative glass-card rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
                        <div class="flex items-center gap-3 mb-4">
                          <div class="w-12 h-12 rounded-full bg-destructive/10 flex items-center justify-center">
                            <svg class="w-6 h-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                          </div>
                          <div>
                            <h3 class="text-lg font-bold">Remove User</h3>
                            <p class="text-sm text-muted-foreground">This action cannot be undone.</p>
                          </div>
                        </div>
                        <p class="text-sm text-muted-foreground mb-4">
                          Are you sure you want to remove <strong class="text-foreground">{{ $user->name ?? $user->handle ?? 'this user' }}</strong>? 
                          The user will no longer be able to access the platform.
                        </p>
                          <form method="POST" action="{{ route('admin.users.remove', $user->id) }}">
                            @csrf
                            <div class="flex justify-end gap-3">
                              <button type="button" @click="showRemoveModal = false" class="px-4 py-2 text-sm font-medium rounded-xl border border-border hover:bg-muted transition-all">
                                Cancel
                              </button>
                              <button type="submit" class="px-4 py-2 text-sm font-medium rounded-xl bg-destructive text-destructive-foreground hover:opacity-90 transition-all">
                                Remove User
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endif

                  {{-- Toggle Admin Modal --}}
                  @if($user->id !== auth()->id())
                    <div x-show="showAdminModal" x-cloak 
                      x-transition:enter="transition ease-out duration-200"
                      x-transition:enter-start="opacity-0"
                      x-transition:enter-end="opacity-100"
                      x-transition:leave="transition ease-in duration-150"
                      x-transition:leave-start="opacity-100"
                      x-transition:leave-end="opacity-0"
                      class="fixed inset-0 z-[100] overflow-y-auto" 
                      @keydown.escape.window="showAdminModal = false">
                      <div class="flex min-h-screen items-center justify-center p-4">
                        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAdminModal = false"></div>
                        <div x-show="showAdminModal"
                          x-transition:enter="transition ease-out duration-200"
                          x-transition:enter-start="opacity-0 scale-95"
                          x-transition:enter-end="opacity-100 scale-100"
                          x-transition:leave="transition ease-in duration-150"
                          x-transition:leave-start="opacity-100 scale-100"
                          x-transition:leave-end="opacity-0 scale-95"
                          class="relative glass-card rounded-2xl p-6 w-full max-w-md shadow-2xl" @click.stop>
                          <div class="flex items-center gap-3 mb-4">
                            <div class="w-12 h-12 rounded-full bg-highlight/10 flex items-center justify-center">
                              @if($user->role === 'SUPER_ADMIN')
                                <svg class="w-6 h-6 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                              @else
                                <svg class="w-6 h-6 text-highlight" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                              @endif
                            </div>
                            <div>
                              <h3 class="text-lg font-bold">{{ $user->role === 'SUPER_ADMIN' ? 'Remove Admin Role' : 'Grant Admin Role' }}</h3>
                              <p class="text-sm text-muted-foreground">{{ $user->role === 'SUPER_ADMIN' ? 'This user will lose admin privileges.' : 'This user will gain admin privileges.' }}</p>
                            </div>
                          </div>
                          <p class="text-sm text-muted-foreground mb-4">
                            @if($user->role === 'SUPER_ADMIN')
                              Are you sure you want to remove admin privileges from <strong class="text-foreground">{{ $user->name ?? $user->handle ?? 'this user' }}</strong>? 
                              They will no longer be able to access the admin panel.
                            @else
                              Are you sure you want to grant admin privileges to <strong class="text-foreground">{{ $user->name ?? $user->handle ?? 'this user' }}</strong>? 
                              They will be able to access all admin features.
                            @endif
                          </p>
                          <form method="POST" action="{{ route('admin.users.toggle-admin', $user->id) }}">
                            @csrf
                            <div class="flex justify-end gap-3">
                              <button type="button" @click="showAdminModal = false" class="px-4 py-2 text-sm font-medium rounded-xl border border-border hover:bg-muted transition-all">
                                Cancel
                              </button>
                              <button type="submit" class="px-4 py-2 text-sm font-medium rounded-xl bg-highlight text-white hover:opacity-90 transition-all">
                                {{ $user->role === 'SUPER_ADMIN' ? 'Remove Admin' : 'Make Admin' }}
                              </button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- Pagination --}}
      @if($users->hasPages())
        <div class="p-4 border-t border-border">
          {{ $users->withQueryString()->links() }}
        </div>
      @endif
    @else
      <div class="p-12 text-center">
        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted/50 flex items-center justify-center">
          <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
          </svg>
        </div>
        <h3 class="text-lg font-semibold mb-1">No Users Found</h3>
        <p class="text-sm text-muted-foreground">
          @if(request('search'))
            No users match your search criteria.
          @else
            There are no users registered yet.
          @endif
        </p>
      </div>
    @endif
  </div>
@endsection
