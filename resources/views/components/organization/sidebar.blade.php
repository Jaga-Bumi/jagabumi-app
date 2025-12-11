<aside x-data="{ collapsed: false }" :class="collapsed ? 'w-16' : 'w-64'" class="fixed left-0 top-0 h-screen bg-sidebar border-r border-sidebar-border transition-all duration-300 z-40 flex flex-col">
  {{-- Logo --}}
  <div class="h-16 flex items-center justify-between px-4 border-b border-sidebar-border">
    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
      <div class="w-9 h-9 rounded-xl gradient-primary flex items-center justify-center shadow-soft group-hover:scale-110 transition-transform duration-300">
        <svg class="w-5 h-5 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
      </div>
      <span x-show="!collapsed" class="text-lg font-bold gradient-text">JagaBumi</span>
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

  {{-- Organization Selector --}}
  <div x-show="!collapsed" class="p-4 border-b border-sidebar-border">
    <form method="POST" id="org-switch-form">
      @csrf
      <select name="org_id" onchange="switchOrg(this.value)" class="w-full px-3 py-2 rounded-lg bg-muted border-0 focus:ring-2 focus:ring-primary">
        @foreach($userOrganizations as $org)
          <option value="{{ $org['id'] }}" {{ $org['id'] == $currentOrg['id'] ? 'selected' : '' }}>
            {{ $org['name'] }} ({{ ucfirst($org['role']) }})
          </option>
        @endforeach
      </select>
    </form>
    <script>
      function switchOrg(orgId) {
        const form = document.getElementById('org-switch-form');
        form.action = `/organization/switch/${orgId}`;
        form.submit();
      }
    </script>
    <p class="text-xs text-muted-foreground mt-2 capitalize">
      Role: {{ $currentOrg['role'] }}
    </p>
  </div>

  <div x-show="collapsed" class="p-2 border-b border-sidebar-border">
    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center mx-auto">
      <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
    </div>
  </div>

  {{-- Navigation --}}
  <nav class="flex-1 p-2 space-y-1 overflow-y-auto">
    {{-- Dashboard --}}
    <a href="{{ route('organization.dashboard') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('organization.dashboard') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
      </svg>
      <span x-show="!collapsed">Dashboard</span>
    </a>

    {{-- Organization Profile --}}
    <a href="{{ route('organization.profile') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('organization.profile') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
      </svg>
      <span x-show="!collapsed">Organization Profile</span>
    </a>

    {{-- Member Management (CREATOR only) --}}
    @if($currentOrg['role'] === 'CREATOR')
    <a href="{{ route('organization.members.index') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('organization.members.index') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
      </svg>
      <span x-show="!collapsed">Member Management</span>
    </a>
    @endif

    {{-- Quest Management --}}
    <a href="{{ route('organization.quests.index') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('organization.quests*') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
      </svg>
      <span x-show="!collapsed">Quest Management</span>
    </a>

    {{-- Submission Review --}}
    <a href="{{ route('organization.submissions') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('organization.submissions') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <span x-show="!collapsed">Submission Review</span>
    </a>

    {{-- Prize Distribution --}}
    <a href="{{ route('organization.prizes') }}" 
       :class="collapsed ? 'justify-center px-2' : 'px-3'"
       class="flex items-center gap-3 py-2.5 rounded-xl font-medium text-sm transition-all duration-200 {{ request()->routeIs('organization.prizes') ? 'bg-primary text-primary-foreground shadow-glow' : 'hover:bg-muted' }}">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
      </svg>
      <span x-show="!collapsed">Prize Distribution</span>
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
