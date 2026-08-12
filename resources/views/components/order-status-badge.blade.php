@props(['status'])

@php
    $map = [
        'pending'   => ['Pending', 'bg-yellow-100 text-yellow-800'],
        'accepted'  => ['Accepted', 'bg-blue-100 text-blue-800'],
        'picked_up' => ['Picked Up', 'bg-indigo-100 text-indigo-800'],
        'washing'   => ['Washing', 'bg-cyan-100 text-cyan-800'],
        'ironing'   => ['Ironing', 'bg-purple-100 text-purple-800'],
        'ready'     => ['Ready', 'bg-teal-100 text-teal-800'],
        'completed' => ['Completed', 'bg-green-100 text-green-800'],
        'cancelled' => ['Cancelled', 'bg-gray-200 text-gray-700'],
        'rejected'  => ['Rejected', 'bg-red-100 text-red-800'],
    ];
    [$label, $classes] = $map[$status] ?? [ucfirst($status), 'bg-gray-100 text-gray-800'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $classes"]) }}>
    {{ $label }}
</span>
