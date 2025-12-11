<header x-data="{ mobileMenuOpen: false, scrolled: false }" x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })" :class="scrolled ? 'glass-card shadow-card py-2' : 'bg-transparent py-4'" class="fixed top-0 left-0 right-0 z-50 transition-all duration-300">
  <div class="container mx-auto px-4 flex items-center justify-between">
    {{-- Logo --}}
    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
      <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center shadow-glow group-hover:scale-110 transition-transform duration-300">
        <svg class="w-6 h-6 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
        </svg>
      </div>
      <span class="text-xl font-bold gradient-text hidden sm:block">JagaBumi</span>
    </a>

    {{-- Desktop Navigation --}}
    <nav class="hidden lg:flex items-center gap-1">
      <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 {{ request()->routeIs('home') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground hover:bg-muted' }}">
        Home
      </a>
      <a href="{{ route('quests.all') }}" class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 {{ request()->routeIs('quests.*') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground hover:bg-muted' }}">
        Quests
      </a>
      <a href="{{ route('leaderboard') }}" class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 {{ request()->routeIs('leaderboard') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground hover:bg-muted' }}">
        Leaderboard
      </a>
      <a href="{{ route('organizations.all') }}" class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 {{ request()->routeIs('organizations.*') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground hover:bg-muted' }}">
        Organizations
      </a>
      <a href="{{ route('articles.all') }}" class="px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 {{ request()->routeIs('articles.*') ? 'bg-primary/10 text-primary' : 'text-muted-foreground hover:text-foreground hover:bg-muted' }}">
        Articles
      </a>
    </nav>

    {{-- Right Actions --}}
    <div class="flex items-center gap-2">
      <div class="hidden sm:flex items-center gap-2">
        @guest
          <button id="auth-btn" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg font-semibold text-sm transition-all duration-200 hover:bg-muted text-muted-foreground hover:text-foreground">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
            </svg>
            Login
          </button>
        @endguest
        
        <a href="{{ route('join-us') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg font-semibold text-sm gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:-translate-y-1 hover:scale-[1.02] transition-all duration-300">
          Join Us
        </a>
        
        @auth
          <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = !dropdownOpen" class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-muted transition-all duration-200">
              @if(auth()->user()->avatar_url)
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full object-cover">
              @else
                <div class="w-8 h-8 rounded-full gradient-primary flex items-center justify-center text-primary-foreground font-semibold text-sm">
                  {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
              @endif
              <span class="font-semibold hidden md:block">{{ auth()->user()->name }}</span>
              <svg class="w-4 h-4 transition-transform duration-200" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <div x-show="dropdownOpen" @click.away="dropdownOpen = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 bg-card border border-border rounded-xl shadow-lift py-2 z-50">
              <div class="px-4 py-3 border-b border-border">
                <p class="text-sm font-semibold">{{ auth()->user()->name }}</p>
                <p class="text-xs text-muted-foreground">{{ auth()->user()->email }}</p>
              </div>
              <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Profile
              </a>
              <a href="{{ route('dashboard.index') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
              </a>
              @php
                $hasOrganization = auth()->user()->organizations()->where('organization_members.status', 'ACTIVE')->exists();
                $hasApprovedRequest = !auth()->user()->createdOrganization && \App\Models\OrganizationRequest::where('user_id', auth()->id())->where('status', 'APPROVED')->exists();
              @endphp
              @if($hasOrganization)
                <a href="{{ route('organization.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium hover:bg-muted transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                  </svg>
                  Organization
                </a>
              @elseif($hasApprovedRequest)
                <a href="{{ route('organization.create') }}" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-primary hover:bg-primary/10 transition-colors">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                  </svg>
                  Create Organization
                </a>
              @endif
              <div class="border-t border-border my-2"></div>
              <button id="logout-btn" type="button" class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-destructive hover:bg-destructive/10 transition-colors w-full text-left">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
              </button>
            </div>
          </div>
        @endauth
      </div>

      {{-- Mobile Menu Toggle --}}
      <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden p-2 rounded-full hover:bg-muted transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
  </div>

  {{-- Mobile Menu --}}
  <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="lg:hidden mt-2 pb-4 px-4">
    <nav class="flex flex-col gap-1 bg-card border border-border rounded-2xl p-2 shadow-lift">
      <a href="{{ route('home') }}" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ request()->routeIs('home') ? 'bg-primary/10 text-primary' : 'hover:bg-muted' }}">
        Home
      </a>
      <a href="{{ route('quests.all') }}" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ request()->routeIs('quests.*') ? 'bg-primary/10 text-primary' : 'hover:bg-muted' }}">
        Quests
      </a>
      <a href="{{ route('leaderboard') }}" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ request()->routeIs('leaderboard') ? 'bg-primary/10 text-primary' : 'hover:bg-muted' }}">
        Leaderboard
      </a>
      <a href="{{ route('organizations.all') }}" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ request()->routeIs('organizations.*') ? 'bg-primary/10 text-primary' : 'hover:bg-muted' }}">
        Organizations
      </a>
      <a href="{{ route('articles.all') }}" class="px-4 py-3 rounded-xl font-semibold text-sm transition-all {{ request()->routeIs('articles.*') ? 'bg-primary/10 text-primary' : 'hover:bg-muted' }}">
        Articles
      </a>
      
      <div class="border-t border-border my-2"></div>
      
      @guest
        <button id="auth-btn-mobile" type="button" class="px-4 py-3 rounded-xl font-semibold text-sm hover:bg-muted text-left transition-all">
          Login / Register
        </button>
      @else
        <a href="{{ route('profile.index') }}" class="px-4 py-3 rounded-xl font-semibold text-sm hover:bg-muted transition-all">
          Profile
        </a>
        <a href="{{ route('dashboard.index') }}" class="px-4 py-3 rounded-xl font-semibold text-sm hover:bg-muted transition-all">
          Dashboard
        </a>
        <button id="logout-btn-mobile" type="button" class="px-4 py-3 rounded-xl font-semibold text-sm text-destructive hover:bg-destructive/10 text-left transition-all">
          Logout
        </button>
      @endguest
      
      <a href="{{ route('join-us') }}" class="px-4 py-3 rounded-xl font-semibold text-sm gradient-primary text-primary-foreground text-center shadow-glow">
        Join Us
      </a>
    </nav>
  </div>

  {{-- Loading Indicator --}}
  <div id="loading" style="display: none;" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center">
    <div class="bg-card rounded-2xl p-8 shadow-2xl">
      <div class="flex flex-col items-center gap-4">
        <div class="w-16 h-16 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
        <p class="text-lg font-medium">Connecting...</p>
      </div>
    </div>
  </div>

  {{-- Error Box --}}
  <div id="error-box" style="display: none;" class="fixed top-20 right-4 z-50 max-w-md">
    <div class="bg-destructive/10 border border-destructive text-destructive rounded-xl p-4 shadow-lg">
      <p id="error-message"></p>
    </div>
  </div>
</header>

@guest
<script>
  // Sync mobile auth button with desktop auth button
  document.addEventListener('DOMContentLoaded', function() {
    const mobileAuthBtn = document.getElementById('auth-btn-mobile');
    if (mobileAuthBtn) {
      mobileAuthBtn.addEventListener('click', () => {
        const authBtn = document.getElementById('auth-btn');
        if (authBtn) authBtn.click();
      });
    }
  });
</script>
@else
<script>
  // Sync mobile logout button with desktop logout button
  document.addEventListener('DOMContentLoaded', function() {
    const mobileLogoutBtn = document.getElementById('logout-btn-mobile');
    if (mobileLogoutBtn) {
      mobileLogoutBtn.addEventListener('click', () => {
        const logoutBtn = document.getElementById('logout-btn');
        if (logoutBtn) logoutBtn.click();
      });
    }
  });
</script>
@endguest
