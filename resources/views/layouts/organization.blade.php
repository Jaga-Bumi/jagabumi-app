<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $title ?? '' ? ($title ?? '') . ' - ' : '' }}Organization Dashboard - JagaBumi</title>
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-background text-foreground" x-data="{ sidebarOpen: false, isDesktop: window.innerWidth >= 1024 }" x-init="window.addEventListener('resize', () => { isDesktop = window.innerWidth >= 1024 })">
  
  {{-- Desktop Sidebar --}}
  <div x-show="isDesktop" x-cloak>
    @include('components.organization.sidebar', ['userOrganizations' => $userOrganizations ?? [], 'currentOrg' => $currentOrg ?? null])
  </div>

  {{-- Mobile Sidebar Overlay --}}
  <div x-show="!isDesktop && sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

  {{-- Mobile Sidebar --}}
  <div x-show="!isDesktop && sidebarOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full" class="fixed left-0 top-0 z-40 w-64">
    @include('components.organization.sidebar', ['userOrganizations' => $userOrganizations ?? [], 'currentOrg' => $currentOrg ?? null])
  </div>

  {{-- Main Content --}}
  <div :class="isDesktop && 'ml-64'" class="min-h-screen transition-all duration-300">
    {{-- Header --}}
    <header class="sticky top-0 z-30 h-16 glass-card border-b border-border flex items-center justify-between px-4 lg:px-6">
      <div class="flex items-center gap-4">
        <button x-show="!isDesktop" @click="sidebarOpen = !sidebarOpen" class="rounded-full h-10 w-10 hover:bg-muted transition-colors flex items-center justify-center lg:hidden">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
          </svg>
        </button>
        <div>
          @if(isset($title) && $title)
            <h1 class="text-lg font-bold">{{ $title }}</h1>
          @endif
          @if(isset($subtitle) && $subtitle)
            <p class="text-xs text-muted-foreground">{{ $subtitle }}</p>
          @endif
        </div>
      </div>

      <div class="flex items-center gap-2">
        <div class="hidden md:flex relative">
          <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
          </svg>
          <input type="text" placeholder="Search..." class="pl-9 pr-4 py-2 w-64 rounded-lg bg-muted/50 border-0 focus:ring-2 focus:ring-primary text-sm">
        </div>
        <button class="rounded-full h-10 w-10 hover:bg-muted transition-colors flex items-center justify-center relative">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
          </svg>
          <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-destructive text-destructive-foreground text-[10px] font-bold rounded-full flex items-center justify-center">3</span>
        </button>
        @if(auth()->user()->avatar_url)
          <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-9 h-9 rounded-full object-cover">
        @else
          <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center">
            <span class="text-sm font-semibold text-primary">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
          </div>
        @endif
      </div>
    </header>

    {{-- Page Content --}}
    <main class="p-4 lg:p-6">
      @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-primary/10 border border-primary/20 text-primary">
          {{ session('success') }}
        </div>
      @endif
      
      @if(session('error'))
        <div class="mb-6 p-4 rounded-xl bg-destructive/10 border border-destructive/20 text-destructive">
          {{ session('error') }}
        </div>
      @endif

      @yield('content')
    </main>
  </div>

</body>
</html>
