<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AUN E-Laundry</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 font-sans">
    <!-- Top bar (AUN-style navy) -->
    <header class="bg-aun-navy text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="AUN E-Laundry" class="h-14 w-14 bg-white rounded-full p-0.5">
                <div class="leading-tight">
                    <div class="font-bold text-lg">AUN E-Laundry</div>
                    <div class="text-xs text-white/60">American University of Nigeria</div>
                </div>
            </div>
            <nav class="flex items-center gap-2 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-aun-orange text-white rounded-md font-medium hover:bg-aun-orange-dark">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-white/90 hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-aun-orange text-white rounded-md font-medium hover:bg-aun-orange-dark">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="bg-aun-navy text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 pt-10 pb-24 text-center">
            <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight">
                Campus laundry, <span class="text-aun-orange">done right.</span>
            </h1>
            <p class="mt-5 max-w-2xl mx-auto text-lg text-white/80">
                A centralised platform connecting American University of Nigeria students with verified
                laundry workers — transparent pricing, real-time tracking, and accountability for everyone.
            </p>
            <div class="mt-8 flex items-center justify-center gap-3">
                <a href="{{ route('register') }}" class="px-6 py-3 bg-aun-orange text-white rounded-md font-semibold hover:bg-aun-orange-dark">Get started</a>
                <a href="{{ route('login') }}" class="px-6 py-3 border border-white/40 text-white rounded-md font-semibold hover:bg-white/10">Log in</a>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 -mt-14 pb-16">
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
                <div class="bg-white rounded-lg shadow-sm p-6 border-t-4 border-aun-orange">
                    <div class="text-3xl">{{ $icon }}</div>
                    <h3 class="mt-3 font-semibold text-aun-navy">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- How it works -->
    <section class="bg-white border-y border-gray-100">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-2xl font-bold text-center text-aun-navy">How it works</h2>
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
                        <div class="mx-auto w-11 h-11 rounded-full bg-aun-navy text-white flex items-center justify-center font-bold text-lg">{{ $n }}</div>
                        <h3 class="mt-3 font-semibold text-aun-navy">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <footer class="bg-aun-navy text-white/70">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-8 text-center text-sm">
            AUN E-Laundry — Senior Design Project, School of Information Technology &amp; Computing.
        </div>
    </footer>
</body>
</html>
