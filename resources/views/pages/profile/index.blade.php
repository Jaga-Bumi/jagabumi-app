@extends('layouts.main')

@section('title', 'My Profile - JagaBumi.id')

@section('content')
<!-- Profile Header -->
<section class="bg-gradient-to-b from-secondary/50 to-background py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto">
            <div class="glass-card p-8 rounded-2xl">
                <div class="flex flex-col md:flex-row gap-6 items-center md:items-start">
                    {{-- Avatar --}}
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full ring-4 ring-primary/20 object-cover">
                    @else
                        <div class="w-24 h-24 rounded-full ring-4 ring-primary/20 bg-gradient-to-br from-primary/20 to-secondary/20 flex items-center justify-center">
                            <span class="text-3xl font-bold text-primary">{{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}</span>
                        </div>
                    @endif
                    
                    <div class="flex-1 text-center md:text-left">
                        <h1 class="text-3xl font-bold text-foreground mb-1">{{ $user->name ?? 'Unnamed' }}</h1>
                        @if($user->handle)
                            <p class="text-muted-foreground mb-1">{{ '@' . $user->handle }}</p>
                        @endif
                        @if($user->bio)
                            <p class="text-sm text-muted-foreground mb-4 max-w-lg">{{ $user->bio }}</p>
                        @endif
                        
                        {{-- Stats --}}
                        <div class="flex flex-wrap gap-6 justify-center md:justify-start">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ number_format($totalPoints) }}</div>
                                <div class="text-sm text-muted-foreground">Points</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ $rank }}</div>
                                <div class="text-sm text-muted-foreground">Rank</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ $completedQuests }}</div>
                                <div class="text-sm text-muted-foreground">Completed</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary">{{ $questWins->count() }}</div>
                                <div class="text-sm text-muted-foreground">Wins</div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Contact Info & Edit Button --}}
                    <div class="flex flex-col items-center md:items-end gap-3">
                        <a href="{{ route('dashboard.index') }}" class="btn-glass px-6 py-2 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Profile
                        </a>
                        <div class="text-sm text-muted-foreground text-center md:text-right">
                            @if($user->email)
                                <p>{{ $user->email }}</p>
                            @endif
                            @if($user->wallet_address)
                                <p class="font-mono text-xs mt-1">{{ substr($user->wallet_address, 0, 6) }}...{{ substr($user->wallet_address, -4) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Content -->
<section class="py-12">
    <div class="container mx-auto px-4">
        <div class="max-w-5xl mx-auto space-y-8">
            
            {{-- Quest History --}}
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-foreground flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                        Quest History
                    </h2>
                    <a href="{{ route('quests.all') }}" class="text-sm text-primary hover:underline">Find more quests →</a>
                </div>
                
                @if($questParticipations->count() > 0)
                    <div class="space-y-3">
                        @foreach($questParticipations->take(10) as $participation)
                            <div class="flex items-center justify-between p-4 bg-secondary/30 rounded-xl hover:bg-secondary/50 transition-colors">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-3">
                                        @if($participation->quest->thumbnail_url)
                                            <img src="{{ $participation->quest->thumbnail_url }}" alt="{{ $participation->quest->title }}" class="w-12 h-12 rounded-lg object-cover flex-shrink-0">
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                                <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <h3 class="font-medium text-foreground truncate">{{ $participation->quest->title }}</h3>
                                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                <span>{{ $participation->quest->organization->name ?? 'Unknown' }}</span>
                                                <span>•</span>
                                                <span>{{ $participation->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 flex-shrink-0 ml-4">
                                    @php
                                        $statusColors = [
                                            'JOINED' => 'bg-blue-500/20 text-blue-500',
                                            'SUBMITTED' => 'bg-yellow-500/20 text-yellow-500',
                                            'COMPLETED' => 'bg-green-500/20 text-green-500',
                                            'REJECTED' => 'bg-red-500/20 text-red-500',
                                            'CANCELLED' => 'bg-gray-500/20 text-gray-500',
                                        ];
                                        $statusLabels = [
                                            'JOINED' => 'Joined',
                                            'SUBMITTED' => 'Submitted',
                                            'COMPLETED' => 'Completed',
                                            'REJECTED' => 'Rejected',
                                            'CANCELLED' => 'Cancelled',
                                        ];
                                    @endphp
                                    <span class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$participation->status] ?? 'bg-gray-500/20 text-gray-500' }}">
                                        {{ $statusLabels[$participation->status] ?? $participation->status }}
                                    </span>
                                    @if($participation->status === 'COMPLETED' && $participation->quest->reward_points)
                                        <span class="text-sm font-semibold text-primary">+{{ $participation->quest->reward_points }} pts</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    @if($questParticipations->count() > 10)
                        <div class="mt-4 text-center">
                            <p class="text-sm text-muted-foreground">Showing 10 of {{ $questParticipations->count() }} quest participations</p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted/50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">No Quests Yet</h3>
                        <p class="text-sm text-muted-foreground mb-4">You haven't joined any quests. Start your journey!</p>
                        <a href="{{ route('quests.all') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-primary text-primary-foreground rounded-xl hover:opacity-90 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            Browse Quests
                        </a>
                    </div>
                @endif
            </div>

            {{-- Prize Wins --}}
            @if($questWins->count() > 0)
                <div class="glass-card p-6 rounded-2xl">
                    <h2 class="text-xl font-semibold text-foreground mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        Prize Wins
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($questWins as $win)
                            <div class="p-4 bg-gradient-to-r from-yellow-500/10 to-orange-500/10 rounded-xl border border-yellow-500/20">
                                <div class="flex items-start gap-3">
                                    <div class="w-12 h-12 rounded-lg bg-yellow-500/20 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                        </svg>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="font-semibold text-foreground">{{ $win->prize->name ?? 'Prize' }}</h3>
                                        <p class="text-sm text-muted-foreground">{{ $win->quest->title ?? 'Quest' }}</p>
                                        <p class="text-xs text-muted-foreground mt-1">Won on {{ $win->created_at->format('M d, Y') }}</p>
                                        @if($win->prize->value ?? null)
                                            <p class="text-sm font-semibold text-yellow-500 mt-2">{{ $win->prize->value }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Organizations --}}
            @if($organizations->count() > 0)
                <div class="glass-card p-6 rounded-2xl">
                    <h2 class="text-xl font-semibold text-foreground mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        My Organizations
                    </h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($organizations as $membership)
                            <a href="{{ route('organizations.show', $membership->organization->slug) }}" class="flex items-center gap-4 p-4 bg-secondary/30 rounded-xl hover:bg-secondary/50 transition-colors">
                                @if($membership->organization->logo_url)
                                    <img src="{{ $membership->organization->logo_url }}" alt="{{ $membership->organization->name }}" class="w-12 h-12 rounded-lg object-cover">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-primary/10 flex items-center justify-center">
                                        <span class="text-lg font-bold text-primary">{{ strtoupper(substr($membership->organization->name, 0, 2)) }}</span>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-foreground truncate">{{ $membership->organization->name }}</h3>
                                    <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                        <span class="px-2 py-0.5 rounded-full text-xs bg-primary/10 text-primary">{{ $membership->role }}</span>
                                        <span>•</span>
                                        <span>Joined {{ $membership->joined_at ? \Carbon\Carbon::parse($membership->joined_at)->format('M Y') : $membership->created_at->format('M Y') }}</span>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- My Articles --}}
            <div class="glass-card p-6 rounded-2xl">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-foreground flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9.5a2.5 2.5 0 00-2.5-2.5H15" />
                        </svg>
                        My Articles
                    </h2>
                    <a href="{{ route('articles.create') }}" class="text-sm text-primary hover:underline flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Write Article
                    </a>
                </div>
                
                @if($articles->count() > 0)
                    <div class="space-y-3">
                        @foreach($articles as $article)
                            <a href="{{ route('articles.single', $article->id) }}" class="flex items-center gap-4 p-4 bg-secondary/30 rounded-xl hover:bg-secondary/50 transition-colors">
                                @if($article->thumbnail_url)
                                    <img src="{{ $article->thumbnail_url }}" alt="{{ $article->title }}" class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-lg bg-primary/10 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-foreground truncate">{{ $article->title }}</h3>
                                    <p class="text-sm text-muted-foreground line-clamp-1">{{ Str::limit(strip_tags($article->content), 100) }}</p>
                                    <p class="text-xs text-muted-foreground mt-1">Published {{ $article->created_at->format('M d, Y') }}</p>
                                </div>
                                <svg class="w-5 h-5 text-muted-foreground flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-muted/50 flex items-center justify-center">
                            <svg class="w-8 h-8 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold mb-2">No Articles Yet</h3>
                        <p class="text-sm text-muted-foreground mb-4">Share your thoughts and experiences with the community!</p>
                        <a href="{{ route('articles.create') }}" class="inline-flex items-center gap-2 px-6 py-2 bg-primary text-primary-foreground rounded-xl hover:opacity-90 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Write Your First Article
                        </a>
                    </div>
                @endif
            </div>

            {{-- Account Info --}}
            <div class="glass-card p-6 rounded-2xl">
                <h2 class="text-xl font-semibold text-foreground mb-6 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Account Information
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Email</p>
                        <p class="text-foreground">{{ $user->email ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Phone</p>
                        <p class="text-foreground">{{ $user->phone ?? 'Not set' }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Wallet Address</p>
                        @if($user->wallet_address)
                            <div class="flex items-center gap-2">
                                <code class="text-sm bg-muted px-2 py-1 rounded font-mono break-all">{{ $user->wallet_address }}</code>
                                <button 
                                    onclick="navigator.clipboard.writeText('{{ $user->wallet_address }}'); this.innerHTML = '<svg class=\'w-4 h-4 text-primary\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M5 13l4 4L19 7\' /></svg>'; setTimeout(() => { this.innerHTML = '<svg class=\'w-4 h-4 text-muted-foreground\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z\' /></svg>' }, 1500)"
                                    class="p-1 hover:bg-muted rounded transition-colors flex-shrink-0"
                                    title="Copy wallet address"
                                >
                                    <svg class="w-4 h-4 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                </button>
                            </div>
                        @else
                            <p class="text-muted-foreground">Not connected</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Auth Provider</p>
                        <span class="px-2.5 py-1 text-xs font-medium rounded-full bg-muted">{{ ucfirst($user->auth_provider ?? 'Unknown') }}</span>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs font-semibold text-muted-foreground uppercase tracking-wider mb-1">Member Since</p>
                        <p class="text-foreground">{{ $user->created_at->format('F d, Y') }} ({{ $user->created_at->diffForHumans() }})</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
