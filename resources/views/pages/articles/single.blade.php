@extends('layouts.main')

@section('title', 'Artikel - JagaBumi.id')

@section('content')

<section class="relative py-20 bg-cover bg-center" style="background-image: url('{{ asset('images/article_header.jpg') }}');">
    
    <div class="absolute inset-0 bg-gray-900 opacity-20"></div>
    
    <div class="container mx-auto px-4 relative z-10">
        <div class="max-w-4xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow-2xl">
            
            {{-- Title of the Article --}}
            <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-6 leading-tight">
                {{ $article->title }}
            </h1>

            {{-- Author and Date --}}
            <div class="flex items-center text-lg text-gray-600">
                <img src="{{ asset($writer->avatar_url ?? 'images/default_author.jpg') }}" 
                     alt="{{ $writer->name ?? 'Author' }}" 
                     class="w-12 h-12 rounded-full mr-4 object-cover">
                
                <div class="flex flex-col md:flex-row md:items-center">
                    {{-- Author Name --}}
                    <span class="font-semibold text-gray-800 mr-4">
                        {{ $writer->name  }}
                    </span>
                    
                    {{-- Date --}}
                    <div class="flex items-center mt-1 md:mt-0">
                        <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        {{-- Format date to match the "Dec 5, 2024" style --}}
                        {{ $article->created_at->format('M j, Y') }}
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</section>

{{-- Article Body Content Section --}}
<section class="py-12 md:py-16">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto">

            {{-- Main Content Section --}}
            <div class="prose prose-lg max-w-none text-gray-800">
                {!! $article->body !!}
                
            </div>
            
        </div>
    </div>
</section>

@endsection

@push('scripts')
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
@endpush