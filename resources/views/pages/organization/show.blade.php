@extends('layouts.main')

@section('content')
    {{-- Organization Banner --}}
    <div class="relative w-full h-64 md:h-80 lg:h-96 overflow-hidden">
        @if($organization->banner_img)
            <img src="{{ asset('storage/' . $organization->banner_img) }}" 
                 alt="{{ $organization->name }} Banner" 
                 class="w-full h-full object-cover">
        @else
            <div class="w-full h-full bg-gradient-to-br from-green-500 via-emerald-600 to-teal-700"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/20 to-transparent"></div>
    </div>

    {{-- Organization Header --}}
    <div class="container mx-auto px-4 -mt-24 relative z-10 mb-12">
        <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl p-8 shadow-2xl">
            <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                {{-- Organization Logo --}}
                <div class="flex-shrink-0">
                    @if($organization->logo_img)
                        <img src="{{ asset('storage/' . $organization->logo_img) }}" 
                             alt="{{ $organization->name }}" 
                             class="w-32 h-32 rounded-2xl object-cover border-4 border-white/30 shadow-xl">
                    @else
                        <div class="w-32 h-32 rounded-2xl bg-gradient-to-br from-green-400 to-emerald-600 flex items-center justify-center border-4 border-white/30 shadow-xl">
                            <span class="text-4xl font-bold text-white">{{ substr($organization->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>

                {{-- Organization Info --}}
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h1 class="text-4xl font-bold text-white">{{ $organization->name }}</h1>
                        @if($organization->is_verified)
                            <svg class="w-8 h-8 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        @endif
                    </div>
                    @if($organization->motto)
                        <p class="text-lg text-white/90 italic mb-4">"{{ $organization->motto }}"</p>
                    @endif
                    <p class="text-white/80">@<span class="font-medium">{{ $organization->handle }}</span></p>
                </div>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-8 pt-8 border-t border-white/20">
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-1">{{ $stats['total_quests'] ?? 0 }}</div>
                    <div class="text-sm text-white/70">Total Quests</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-1">{{ $stats['active_quests'] ?? 0 }}</div>
                    <div class="text-sm text-white/70">Active Quests</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-1">{{ $stats['participants'] ?? 0 }}</div>
                    <div class="text-sm text-white/70">Participants</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-white mb-1">{{ $stats['members'] ?? 0 }}</div>
                    <div class="text-sm text-white/70">Members</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container mx-auto px-4 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left Column - About & Quests --}}
            <div class="lg:col-span-2 space-y-8">
                {{-- About Section --}}
                <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl p-8 shadow-xl">
                    <h2 class="text-2xl font-bold text-white mb-4">About</h2>
                    <div class="text-white/80 leading-relaxed whitespace-pre-line">
                        {{ $organization->desc ?? 'No description available.' }}
                    </div>
                </div>

                {{-- Quests Section --}}
                <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl p-8 shadow-xl">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-white">Recent Quests</h2>
                        @if($quests->count() > 6)
                            <a href="{{ route('quests.all', ['organization' => $organization->id]) }}" 
                               class="px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-xl transition-colors text-sm font-medium">
                                View All
                            </a>
                        @endif
                    </div>

                    @if($quests->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($quests->take(6) as $quest)
                                <a href="{{ route('quests.detail', $quest->slug) }}" 
                                   class="group backdrop-blur-lg bg-white/5 border border-white/10 rounded-2xl p-5 hover:bg-white/10 hover:border-white/20 transition-all duration-300">
                                    @if($quest->image_url)
                                        <img src="{{ asset('storage/' . $quest->image_url) }}" 
                                             alt="{{ $quest->title }}" 
                                             class="w-full h-32 object-cover rounded-xl mb-4">
                                    @else
                                        <div class="w-full h-32 bg-gradient-to-br from-green-500/20 to-emerald-600/20 rounded-xl mb-4 flex items-center justify-center">
                                            <svg class="w-12 h-12 text-white/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    <h3 class="text-white font-semibold mb-2 group-hover:text-emerald-300 transition-colors line-clamp-2">
                                        {{ $quest->title }}
                                    </h3>
                                    
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-white/60">{{ $quest->participants_count ?? 0 }} participants</span>
                                        <span class="px-2 py-1 bg-emerald-500/20 text-emerald-300 rounded-lg text-xs">
                                            {{ ucfirst($quest->status ?? 'active') }}
                                        </span>
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if($quests->count() > 6)
                            <div class="mt-6 text-center">
                                <a href="{{ route('quests.all', ['organization' => $organization->id]) }}" 
                                   class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-emerald-500 to-green-600 text-white rounded-xl hover:from-emerald-600 hover:to-green-700 transition-all duration-300 font-medium shadow-lg">
                                    View All Quests
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-12">
                            <svg class="w-16 h-16 text-white/20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="text-white/60">No quests available yet.</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right Column - Contact & Members --}}
            <div class="space-y-8">
                {{-- Contact Section --}}
                <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl p-8 shadow-xl">
                    <h2 class="text-2xl font-bold text-white mb-6">Contact</h2>
                    
                    <div class="space-y-4">
                        @if($organization->org_email)
                            <a href="mailto:{{ $organization->org_email }}" 
                               class="flex items-center gap-3 text-white/80 hover:text-white transition-colors group">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <span class="text-sm">{{ $organization->org_email }}</span>
                            </a>
                        @endif

                        @if($organization->website_url)
                            <a href="{{ $organization->website_url }}" 
                               target="_blank"
                               class="flex items-center gap-3 text-white/80 hover:text-white transition-colors group">
                                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center group-hover:bg-white/20 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                    </svg>
                                </div>
                                <span class="text-sm">Website</span>
                            </a>
                        @endif
                    </div>

                    {{-- Social Media --}}
                    @if($organization->instagram_url || $organization->x_url || $organization->facebook_url)
                        <div class="mt-6 pt-6 border-t border-white/20">
                            <h3 class="text-sm font-semibold text-white/60 mb-4">SOCIAL MEDIA</h3>
                            <div class="flex gap-3">
                                @if($organization->instagram_url)
                                    <a href="{{ $organization->instagram_url }}" 
                                       target="_blank"
                                       class="w-10 h-10 rounded-xl bg-gradient-to-br from-purple-500 to-pink-500 flex items-center justify-center hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($organization->x_url)
                                    <a href="{{ $organization->x_url }}" 
                                       target="_blank"
                                       class="w-10 h-10 rounded-xl bg-black flex items-center justify-center hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                                        </svg>
                                    </a>
                                @endif

                                @if($organization->facebook_url)
                                    <a href="{{ $organization->facebook_url }}" 
                                       target="_blank"
                                       class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Members Section --}}
                <div class="backdrop-blur-xl bg-white/10 border border-white/20 rounded-3xl p-8 shadow-xl">
                    <h2 class="text-2xl font-bold text-white mb-6">Team</h2>
                    
                    <div class="space-y-4">
                        {{-- Creator --}}
                        @if($organization->creator)
                            <div class="flex items-center gap-4 pb-4 border-b border-white/10">
                                <img src="{{ $organization->creator->profile_picture ?? asset('images/default-avatar.png') }}" 
                                     alt="{{ $organization->creator->name }}" 
                                     class="w-12 h-12 rounded-xl object-cover">
                                <div class="flex-1">
                                    <div class="text-white font-medium">{{ $organization->creator->name }}</div>
                                    <div class="text-xs text-emerald-300 font-semibold">CREATOR</div>
                                </div>
                            </div>
                        @endif

                        {{-- Managers --}}
                        @forelse($organization->managers ?? [] as $manager)
                            <div class="flex items-center gap-4">
                                <img src="{{ $manager->user->profile_picture ?? asset('images/default-avatar.png') }}" 
                                     alt="{{ $manager->user->name }}" 
                                     class="w-12 h-12 rounded-xl object-cover">
                                <div class="flex-1">
                                    <div class="text-white font-medium">{{ $manager->user->name }}</div>
                                    <div class="text-xs text-blue-300 font-semibold">MANAGER</div>
                                </div>
                            </div>
                        @empty
                            @if(!$organization->creator)
                                <p class="text-white/60 text-sm text-center py-4">No team members yet.</p>
                            @endif
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
