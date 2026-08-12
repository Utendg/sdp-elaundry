@props(['value' => 0, 'count' => null])

@php
    $full = (int) round($value);
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-0.5']) }} title="{{ number_format((float) $value, 1) }} out of 5">
    @for ($i = 1; $i <= 5; $i++)
        <svg class="h-4 w-4 {{ $i <= $full ? 'text-amber-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.955a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.364 1.118l1.287 3.955c.3.922-.755 1.688-1.54 1.118l-3.366-2.446a1 1 0 00-1.175 0l-3.366 2.446c-.784.57-1.838-.196-1.539-1.118l1.286-3.955a1 1 0 00-.363-1.118L2.343 9.372c-.783-.57-.38-1.81.588-1.81h4.163a1 1 0 00.95-.69l1.286-3.955z"/>
        </svg>
    @endfor
    @if (! is_null($count))
        <span class="ms-1 text-xs text-gray-500">({{ $count }})</span>
    @endif
</span>
