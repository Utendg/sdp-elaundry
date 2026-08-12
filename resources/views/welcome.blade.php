<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AUN E-Laundry</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900">
    <!-- Top bar -->
    <header class="border-b border-gray-100 bg-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <span class="text-2xl">🧺</span>
                <span class="font-bold text-lg">AUN E-Laundry</span>
            </div>
            <nav class="flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-700 hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-20 text-center">
        <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
            Campus laundry, <span class="text-indigo-600">done right.</span>
        </h1>
        <p class="mt-5 max-w-2xl mx-auto text-lg text-gray-600">
            A centralised platform connecting American University of Nigeria students with verified
            laundry workers — transparent pricing, real-time tracking, and accountability for everyone.
        </p>
        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="{{ route('register') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-md font-medium hover:bg-indigo-700">Get started</a>
            <a href="{{ route('login') }}" class="px-6 py-3 border border-gray-300 rounded-md font-medium hover:bg-gray-100">Log in</a>
        </div>
    </section>

    <!-- Features -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 pb-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $features = [
                    ['💰', 'Transparent pricing', 'Every item is priced at the official university rate — no overcharging, no surprises.'],
                    ['📍', 'Real-time tracking', 'Follow your laundry from pickup through washing, ironing, and ready for return.'],
                    ['⭐', 'Ratings & trust', 'Two-way reviews keep workers reliable and students accountable.'],
                    ['🛡️', 'Verified workers', 'Every laundry worker is approved by the administration before taking orders.'],
                ];
            @endphp
            @foreach ($features as [$icon, $title, $desc])
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-3xl">{{ $icon }}</div>
                    <h3 class="mt-3 font-semibold text-gray-900">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- How it works -->
    <section class="bg-white border-y border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-2xl font-bold text-center">How it works</h2>
            <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-8">
                @php
                    $steps = [
                        ['1', 'Find a worker', 'Browse verified workers in your dorm, sorted by rating.'],
                        ['2', 'Place your order', 'Pick your items and see the exact price before you book.'],
                        ['3', 'Track & rate', 'Watch progress in real time, then rate the service.'],
                    ];
                @endphp
                @foreach ($steps as [$n, $title, $desc])
                    <div class="text-center">
                        <div class="mx-auto w-10 h-10 rounded-full bg-indigo-600 text-white flex items-center justify-center font-bold">{{ $n }}</div>
                        <h3 class="mt-3 font-semibold">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <footer class="max-w-6xl mx-auto px-4 sm:px-6 py-10 text-center text-sm text-gray-500">
        AUN E-Laundry — Senior Design Project, School of IT & Computing.
    </footer>
</body>
</html>
