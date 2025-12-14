@extends('layouts.organization')

@section('content')

{{-- Articles Management List (pages.organization.articles.index) --}}
<div class="max-w-6xl mx-auto space-y-6">
    <div class="glass-card p-6 rounded-2xl">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Organization Articles</h2>
            {{-- Link to the Organization Articles Create route --}}
            <a href="{{ route('organization.articles.create') }}" class="inline-flex items-center gap-1 text-sm font-semibold text-primary hover:text-primary-foreground hover:bg-primary transition-colors px-4 py-2 rounded-lg border border-primary/50">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                New Article
            </a>
        </div>
        
        {{-- Use the $articles variable passed from the organizationArticles controller method --}}
        @if($articles->count() > 0)
            <div class="space-y-4">
                @foreach($articles as $article)
                    {{-- Article List Item --}}
                    <div class="flex items-center p-4 rounded-xl bg-muted/50 hover:bg-muted transition-colors border border-border/50">
                        
                        {{-- Thumbnail/Title --}}
                        <div class="flex items-center gap-4 flex-1 min-w-0 pr-4">
                            {{-- Use the thumbnail field from your model --}}
                            @if($article->thumbnail)
                                <img src="{{ asset('storage/' . $article->thumbnail) }}" alt="{{ $article->title }}" class="w-16 h-12 object-cover rounded-lg flex-shrink-0">
                            @else
                                <div class="w-16 h-12 rounded-lg flex-shrink-0 bg-primary/10 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-primary/70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                            @endif
                            
                            <div class="min-w-0">
                                <h3 class="font-semibold text-lg text-foreground truncate">{{ $article->title }}</h3>
                                <p class="text-sm text-muted-foreground">
                                    Created: {{ $article->created_at->format('M j, Y') }}
                                    @if ($article->user)
                                        <span class="ml-2">| By {{ $article->user->name }}</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        {{-- Actions: Edit and Delete --}}
                        <div class="flex items-center gap-2 flex-shrink-0">
                            
                            {{-- Edit Link --}}
                            <a href="{{ route('organization.articles.edit', $article->id) }}" class="text-primary hover:text-primary/80 transition-colors p-2 rounded-full hover:bg-primary/10" title="Edit Article">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </a>

                            {{-- Delete Form --}}
                            <form action="{{ route('organization.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article? This action cannot be undone.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-400 transition-colors p-2 rounded-full hover:bg-red-500/10" title="Delete Article">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State --}}
            <div class="text-center py-12">
                <div class="w-20 h-20 mx-auto mb-4 rounded-full bg-muted/50 flex items-center justify-center">
                    <svg class="w-10 h-10 text-muted-foreground" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold mb-2">No Articles Yet</h3>
                <p class="text-muted-foreground mb-4">Your organization hasn't published any articles.</p>
                <a href="{{ route('organization.articles.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg gradient-primary text-primary-foreground font-semibold shadow-glow hover:shadow-lift hover:-translate-y-1 transition-all duration-300">
                    Create First Article
                </a>
            </div>
        @endif
    </div>
</div>

@endsection