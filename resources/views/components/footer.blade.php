<footer class="bg-card border-t border-border">
  <div class="container mx-auto px-4 py-16">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
      {{-- Brand --}}
      <div class="space-y-4">
        <a href="{{ route('home') }}" class="flex items-center gap-2">
          <div class="w-10 h-10 rounded-xl gradient-primary flex items-center justify-center shadow-glow">
            <svg class="w-6 h-6 text-primary-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
            </svg>
          </div>
          <span class="text-xl font-bold gradient-text">JagaBumi</span>
        </a>
        <p class="text-muted-foreground text-sm leading-relaxed">
          Gamifying environmental action. Bergabunglah dengan ribuan Eco-Warriors yang memberikan dampak nyata melalui quest dan tantangan yang menyenangkan.
        </p>
        <div class="flex gap-2 pt-2">
          @php
            $socials = [
              ['icon' => 'instagram', 'url' => '#', 'label' => 'Instagram'],
              ['icon' => 'twitter', 'url' => '#', 'label' => 'Twitter'],
              ['icon' => 'facebook', 'url' => '#', 'label' => 'Facebook'],
              ['icon' => 'youtube', 'url' => '#', 'label' => 'YouTube']
            ];
          @endphp
          @foreach($socials as $social)
            <a href="{{ $social['url'] }}" 
               aria-label="{{ $social['label'] }}"
               class="w-10 h-10 rounded-full bg-muted hover:bg-primary/10 hover:text-primary flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-glow">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                @if($social['icon'] === 'instagram')
                  <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                @elseif($social['icon'] === 'twitter')
                  <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                @elseif($social['icon'] === 'facebook')
                  <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                @else
                  <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                @endif
              </svg>
            </a>
          @endforeach
        </div>
      </div>

      {{-- Quick Links --}}
      <div class="space-y-4">
        <h3 class="font-semibold text-foreground text-base">Telusuri</h3>
        <nav class="flex flex-col gap-3">
          <a href="{{ route('quests.all') }}" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Quest Aktif</a>
          <a href="{{ route('leaderboard') }}" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Leaderboard</a>
          <a href="{{ route('organizations.all') }}" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Organisasi</a>
          <a href="{{ route('articles.all') }}" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Artikel</a>
          <a href="{{ route('join-us') }}" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Join as Creator</a>
        </nav>
      </div>

      {{-- Support --}}
      <div class="space-y-4">
        <h3 class="font-semibold text-foreground text-base">Support</h3>
        <nav class="flex flex-col gap-3">
          <a href="#" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Help Center</a>
          <a href="#" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Terms of Service</a>
          <a href="#" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Privacy Policy</a>
          <a href="#" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Community Guidelines</a>
          <a href="#" class="text-sm text-muted-foreground hover:text-primary transition-all duration-300 hover:translate-x-1">Contact Us</a>
        </nav>
      </div>

      {{-- Newsletter --}}
      <div class="space-y-4">
        <h3 class="font-semibold text-foreground text-base">Stay Updated</h3>
        <p class="text-sm text-muted-foreground leading-relaxed">Dapatkan quest terbaru dan eco-tips yang dikirimkan ke inbox Anda.</p>
        <form class="space-y-3" onsubmit="event.preventDefault(); alert('Newsletter signup coming soon!');">
          <div class="relative">
            <input 
              type="email" 
              placeholder="Masukan email" 
              required
              class="w-full px-4 py-2.5 text-sm rounded-lg border border-border bg-background focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all duration-300"
            />
          </div>
          <button type="submit" class="w-full px-4 py-2.5 text-sm font-semibold rounded-lg gradient-primary text-primary-foreground shadow-glow hover:shadow-lift hover:scale-[1.02] transition-all duration-300">
            Subscribe
          </button>
        </form>
        <div class="space-y-2 pt-2">
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            <span>Jakarta, Indonesia</span>
          </div>
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
            <span>hello@jagabumi.id</span>
          </div>
        </div>
      </div>
    </div>

    <div class="border-t border-border mt-12 pt-8 flex flex-col sm:flex-row items-center justify-between gap-4">
      <p class="text-sm text-muted-foreground">
        © {{ date('Y') }} JagaBumi.id. All rights reserved.
      </p>
      <p class="text-sm text-muted-foreground">
        Made with <span class="text-primary animate-pulse">💚</span> for Mother Earth
      </p>
    </div>
  </div>
</footer>
