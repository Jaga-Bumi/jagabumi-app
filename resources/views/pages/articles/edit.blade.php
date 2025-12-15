@extends('layouts.main')

@section('title', 'Edit Artikel - ' . $article->title)

@section('content')

<div class="max-w-4xl mx-auto pt-20 px-4 sm:px-6 lg:px-8">
    <div class="bg-white shadow-xl rounded-lg p-6">
        
        {{-- Form Header --}}
        <h1 class="text-3xl font-bold text-green-700 mb-6 border-b pb-2">
            Edit Artikel
        </h1>

        {{-- The Form for Updating an Existing Article --}}
        <form action="{{ route('articles.update', ['id' => $article->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            {{-- Essential for the Route::put('/articles/{id}') to work --}}
            @method('PUT') 

            {{-- Title Field --}}
            <div class="mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Judul Artikel</label>
                <input type="text" name="title" id="title" required
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-3"
                        placeholder="Masukkan judul artikel..."
                        {{-- Pre-fill with existing data (or old input if validation failed) --}}
                        value="{{ old('title', $article->title) }}">
                @error('title')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Body/Content Field --}}
            <div class="mb-4">
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Isi Artikel</label>
                <textarea name="body" id="body" rows="10" required
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-green-500 focus:ring-green-500 sm:text-sm p-3"
                              placeholder="Tulis konten artikel di sini...">{{ old('body', $article->body) }}</textarea>
                @error('body')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Current Thumbnail Display (Only when editing) --}}
            @if ($article->thumbnail_path)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Thumbnail Saat Ini</label>
                    <img src="{{ asset('storage/' . $article->thumbnail_path) }}" alt="Current Thumbnail" class="w-48 h-auto rounded-md shadow-md object-cover">
                    <p class="text-xs text-gray-500 mt-1">Unggah file baru di bawah untuk mengganti.</p>
                </div>
            @endif

            {{-- Thumbnail Field (Not Required for Update) --}}
            <div class="mb-6">
                <label for="thumbnail" class="block text-sm font-medium text-gray-700 mb-1">Gambar Thumbnail (Opsional)</label>
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md bg-green-50">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-green-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28l-4-4m4-4L24 16" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600">
                            <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-green-600 hover:text-green-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-green-500 p-1">
                                <span>Unggah file</span>
                                <input id="file-upload" name="thumbnail" type="file" class="sr-only">
                            </label>
                            <p class="pl-1 text-gray-500">atau tarik dan lepas</p>
                        </div>
                        <p class="text-xs text-gray-500">
                            PNG, JPG, GIF up to 10MB
                        </p>
                    </div>
                </div>
                @error('thumbnail')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end">
                <button type="submit"
                        class="inline-flex justify-center py-2 px-6 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition duration-150 ease-in-out">
                    Perbarui Artikel
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
  @vite(['resources/js/auth.js', 'resources/js/logout.js'])
@endpush