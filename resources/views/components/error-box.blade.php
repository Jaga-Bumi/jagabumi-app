@props(['id' => null])

<div 
    {{ $attributes->merge(['class' => 'hidden bg-red-50 text-red-700 text-sm p-4 rounded-lg text-center border border-red-200']) }}
    @if($id) id="{{ $id }}" @endif
    role="alert"
    aria-live="assertive"
>
    {{ $slot }}
</div>
