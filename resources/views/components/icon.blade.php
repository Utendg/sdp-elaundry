@props(['name', 'alt' => ''])
{{-- Custom AUN E-Laundry icon (made in Canva), stored in public/icons/ui/{name}.png --}}
<img src="{{ asset('icons/ui/'.$name.'.png') }}" alt="{{ $alt }}" @if ($alt === '') aria-hidden="true" @endif
     {{ $attributes->merge(['class' => 'inline-block object-contain']) }} loading="lazy" decoding="async">
