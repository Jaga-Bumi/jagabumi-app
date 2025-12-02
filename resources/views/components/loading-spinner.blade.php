@props(['message' => 'Loading...'])

<div {{ $attributes->merge(['class' => 'text-center']) }} role="status" aria-live="polite">
    <div class="inline-block animate-spin rounded-full h-10 w-10 border-4 border-green-100 border-t-green-600"></div>
    <p class="text-sm text-slate-600 mt-3 font-medium">{{ $message }}</p>
</div>
