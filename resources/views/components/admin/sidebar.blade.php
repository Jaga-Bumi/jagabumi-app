<aside x-data="{ collapsed: false }" :class="collapsed ? 'w-16' : 'w-64'" class="fixed left-0 top-0 h-screen bg-sidebar border-r border-sidebar-border transition-all duration-300 z-40 flex flex-col">
  {{-- Logo --}}
  <div class="h-16 flex items-center justify-between px-4 border-b border-sidebar-border">
    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
      <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center shadow-soft group-hover:scale-110 transition-transform duration-300">
        <svg class="w-5 h-5 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
      </div>
      <span x-show="!collapsed" class="text-lg font-bold gradient-text">Admin Panel</span>
    </a>
    <button @click="collapsed = !collapsed" class="rounded-full h-8 w-8 hover:bg-muted transition-colors flex items-center justify-center">
      <svg x-show="collapsed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
      </svg>
      <svg x-show="!collapsed" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
      </svg>
    </button>
  </div>

  {{-- Admin Badge --}}
  <div x-show="!collapsed" class="p-4 border-b border-sidebar-border">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-secondary flex items-center justify-center">
        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
        </svg>
      </div>
      <div>
        <p class="font-semibold text-sm">JagaBumi Admin</p>
        <p class="text-xs text-muted-foreground">Super Administrator</p>
      </div>
    </div>
  </div>

  <div x-show="collapsed" class="p-2 border-b border-sidebar-border">
    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary to-secondary flex items-center justify-center mx-auto">
      <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
      </svg>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="flex-1 p-2 space-y-1 overflow-y-auto">
    {{-- Main Section --}}
    <div x-show="!collapsed" class="px-3 py-2">
      <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Main</p>
    </div>

    {{-- Dashboard --}}
    <a href="{{ route('admin.dashboard') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
      </svg>
      <span x-show="!collapsed">Dashboard</span>
    </a>

    {{-- Moderation Section --}}
    <div x-show="!collapsed" class="px-3 py-2 mt-4">
      <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Moderation</p>
    </div>

    {{-- Organization Requests --}}
    <a href="{{ route('admin.organization-requests') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.organization-requests') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <span x-show="!collapsed">Organization Requests</span>
      @php
        $pendingOrgCount = \App\Models\OrganizationRequest::where('status', 'PENDING')->count();
      @endphp
      @if($pendingOrgCount > 0)
        <span x-show="!collapsed" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full {{ request()->routeIs('admin.organization-requests') ? 'bg-white/20 text-white' : 'bg-destructive text-destructive-foreground' }}">{{ $pendingOrgCount }}</span>
      @endif
    </a>

    {{-- Quest Review --}}
    <a href="{{ route('admin.quests') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.quests') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
      </svg>
      <span x-show="!collapsed">Quest Review</span>
      @php
        $pendingQuestCount = \App\Models\Quest::where('status', 'IN REVIEW')->count();
      @endphp
      @if($pendingQuestCount > 0)
        <span x-show="!collapsed" class="ml-auto px-2 py-0.5 text-xs font-bold rounded-full {{ request()->routeIs('admin.quests') ? 'bg-white/20 text-white' : 'bg-destructive text-destructive-foreground' }}">{{ $pendingQuestCount }}</span>
      @endif
    </a>

    {{-- Management Section --}}
    <div x-show="!collapsed" class="px-3 py-2 mt-4">
      <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Management</p>
    </div>

    {{-- Users --}}
    <a href="{{ route('admin.users') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.users') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
      <span x-show="!collapsed">Users</span>
    </a>

    {{-- Organizations --}}
    <a href="{{ route('admin.organizations') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.organizations') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <span x-show="!collapsed">Organizations</span>
    </a>

    {{-- Articles --}}
    <a href="{{ route('admin.articles') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('admin.articles') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
      </svg>
      <span x-show="!collapsed">Articles</span>
    </a>

    {{-- Analytics Section --}}
    <div x-show="!collapsed" class="px-3 py-2 mt-4">
      <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider">Analytics</p>
    </div>

    {{-- Reports --}}
    <a href="#" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 hover:bg-muted text-muted-foreground">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
      </svg>
      <span x-show="!collapsed">Reports</span>
      <span x-show="!collapsed" class="ml-auto text-xs bg-muted px-2 py-0.5 rounded-full">Soon</span>
    </a>

    {{-- Settings --}}
    <a href="#" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 hover:bg-muted text-muted-foreground">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      <span x-show="!collapsed">Settings</span>
      <span x-show="!collapsed" class="ml-auto text-xs bg-muted px-2 py-0.5 rounded-full">Soon</span>
    </a>
  </nav>

  {{-- Footer --}}
  <div class="p-2 border-t border-sidebar-border space-y-1">
    <a href="{{ route('home') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 hover:bg-muted">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
      </svg>
      <span x-show="!collapsed">Back to Main Site</span>
    </a>
  </div>
</aside>
