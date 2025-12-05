<footer class="bg-forest text-primary-foreground">
    <div class="container mx-auto px-4 py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <!-- Brand -->
            <div class="space-y-4">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary to-emerald-light flex items-center justify-center">
                        <x-heroicon-o-cube class="w-5 h-5 text-primary-foreground" />
                    </div>
                    <span class="text-xl font-bold">
                        Jaga<span class="text-emerald-light">Bumi</span>.id
                    </span>
                </div>
                <p class="text-primary-foreground/70 text-sm leading-relaxed">
                    Platform komunitas untuk menjaga bumi bersama. Ubah aksi kecilmu menjadi dampak besar bagi lingkungan.
                </p>
                <div class="flex gap-3">
                    @php
                        $socials = ['instagram', 'twitter', 'facebook', 'youtube'];
                    @endphp
                    @foreach($socials as $social)
                        <a href="#" class="w-10 h-10 rounded-xl bg-primary-foreground/10 flex items-center justify-center hover:bg-emerald-light/20 transition-colors duration-200">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                @if($social === 'instagram')
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                @elseif($social === 'twitter')
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                @elseif($social === 'facebook')
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                @else
                                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                @endif
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Links -->
            <div>
                <h4 class="font-semibold mb-4">Platform</h4>
                <ul class="space-y-3 text-sm text-primary-foreground/70">
                    <li><a href="{{ route('quests.index') }}" class="hover:text-emerald-light transition-colors">Quest</a></li>
                    <li><a href="{{ route('articles.index') }}" class="hover:text-emerald-light transition-colors">Artikel</a></li>
                    <li><a href="{{ route('leaderboard') }}" class="hover:text-emerald-light transition-colors">Leaderboard</a></li>
                    <li><a href="#" class="hover:text-emerald-light transition-colors">Tentang Kami</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Dukungan</h4>
                <ul class="space-y-3 text-sm text-primary-foreground/70">
                    <li><a href="#" class="hover:text-emerald-light transition-colors">Bantuan</a></li>
                    <li><a href="#" class="hover:text-emerald-light transition-colors">FAQ</a></li>
                    <li><a href="#" class="hover:text-emerald-light transition-colors">Hubungi Kami</a></li>
                    <li><a href="#" class="hover:text-emerald-light transition-colors">Partnership</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-semibold mb-4">Legal</h4>
                <ul class="space-y-3 text-sm text-primary-foreground/70">
                    <li><a href="#" class="hover:text-emerald-light transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="#" class="hover:text-emerald-light transition-colors">Syarat & Ketentuan</a></li>
                    <li><a href="#" class="hover:text-emerald-light transition-colors">Kebijakan Cookie</a></li>
                </ul>
            </div>
        </div>

        <div class="border-t border-primary-foreground/20 mt-12 pt-8 text-center text-sm text-primary-foreground/50">
            <p>© {{ date('Y') }} JagaBumi.id. Bersama menjaga bumi untuk generasi mendatang.</p>
        </div>
    </div>
</footer>
