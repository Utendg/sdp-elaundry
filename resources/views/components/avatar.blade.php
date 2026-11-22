@props(['user', 'size' => 'w-10 h-10', 'text' => 'text-sm'])

@if ($user?->avatarUrl())
    <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" loading="lazy" decoding="async"
         {{ $attributes->merge(['class' => "$size rounded-full object-cover bg-gray-100 shrink-0"]) }}>
@else
    @php
        // letters only, so "Bello (Pending Approval)" gives "BP" rather than "B("
        $letters = preg_replace('/[^\p{L}\s]+/u', '', $user?->name ?? '');
        $words = collect(preg_split('/\s+/', trim($letters)))->filter()->values();
        $initials = $words->count() > 1 ? collect([$words->first(), $words->last()]) : $words;
        $initials = $initials->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('');
    @endphp
    <span {{ $attributes->merge(['class' => "$size $text rounded-full bg-aun-navy text-white font-semibold inline-flex items-center justify-center shrink-0"]) }} aria-hidden="true">{{ $initials ?: '?' }}</span>
@endif
