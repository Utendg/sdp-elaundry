<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>AUN E-Laundry — Campus laundry, done right</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-white text-gray-900 font-sans">
    <!-- Top bar -->
    <header class="bg-aun-navy text-white sticky top-0 z-30 shadow-md">
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
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-aun-orange text-white rounded-full font-medium hover:bg-aun-orange-dark">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-white/90 hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-aun-orange text-white rounded-full font-medium hover:bg-aun-orange-dark">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero with campus photo -->
    <section class="relative">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('{{ asset('images/aun/campus.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-aun-navy/95 via-aun-navy/85 to-aun-navy/60"></div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-24 sm:py-32">
            <div class="max-w-2xl text-white">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-sm text-white/90 mb-5">
                    🧺 Built for AUN resident life
                </span>
                <h1 class="text-4xl sm:text-6xl font-extrabold tracking-tight leading-tight">
                    Campus laundry,<br><span class="text-aun-orange">done right.</span>
                </h1>
                <p class="mt-6 text-lg text-white/85">
                    Skip the guesswork. Find a trusted laundry worker in your dorm, see the exact price
                    up front, and track every step — from pickup to fresh, folded, and ready.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('register') }}" class="px-6 py-3 bg-aun-orange text-white rounded-full font-semibold hover:bg-aun-orange-dark shadow-lg">Get started — it's free</a>
                    <a href="{{ route('login') }}" class="px-6 py-3 border border-white/40 text-white rounded-full font-semibold hover:bg-white/10">I already have an account</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Trust strip -->
    <section class="bg-aun-navy text-white/90">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 grid grid-cols-2 sm:grid-cols-4 gap-6 text-center">
            <div><div class="text-2xl font-extrabold text-aun-orange">9</div><div class="text-xs text-white/70">Residence halls</div></div>
            <div><div class="text-2xl font-extrabold text-aun-orange">100%</div><div class="text-xs text-white/70">Official pricing</div></div>
            <div><div class="text-2xl font-extrabold text-aun-orange">Live</div><div class="text-xs text-white/70">Order tracking</div></div>
            <div><div class="text-2xl font-extrabold text-aun-orange">Verified</div><div class="text-xs text-white/70">Laundry workers</div></div>
        </div>
    </section>

    <!-- Features -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <div class="text-center max-w-2xl mx-auto">
            <h2 class="text-3xl font-bold text-aun-navy">Everything you need for laundry day</h2>
            <p class="mt-3 text-gray-600">No more chasing people around the dorm or arguing over prices.</p>
        </div>
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @php
                $features = [
                    ['💰', 'Fair, fixed prices', 'Every item is priced at the official university rate — no overcharging, no surprises.'],
                    ['📍', 'Real-time tracking', 'Follow your laundry from pickup through washing, ironing, and ready for return.'],
                    ['⭐', 'Ratings you can trust', 'Two-way reviews keep workers reliable and students accountable.'],
                    ['🛡️', 'Verified workers', 'Every laundry worker is approved by the administration before taking orders.'],
                ];
            @endphp
            @foreach ($features as [$icon, $title, $desc])
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-6 hover:shadow-md transition">
                    <div class="w-12 h-12 rounded-xl bg-orange-50 flex items-center justify-center text-2xl">{{ $icon }}</div>
                    <h3 class="mt-4 font-semibold text-aun-navy">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Image split: made for campus life -->
    <section class="bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="rounded-3xl overflow-hidden shadow-lg">
                <img src="{{ asset('images/aun/students.jpg') }}" alt="AUN students on campus" class="w-full h-72 sm:h-96 object-cover">
            </div>
            <div>
                <h2 class="text-3xl font-bold text-aun-navy">Made for campus life</h2>
                <p class="mt-4 text-gray-600">
                    Whether you're a freshman who just moved in or a final-year student with a packed schedule,
                    E-Laundry connects you to the right people — right in your hall.
                </p>
                <ul class="mt-6 space-y-3">
                    @foreach ([
                        'Browse verified workers in your dorm, ranked by rating',
                        'See a clear price before you book — pay the university rate',
                        'Report a problem and let admins step in when needed',
                    ] as $point)
                        <li class="flex items-start gap-3">
                            <span class="mt-0.5 w-5 h-5 rounded-full bg-aun-green text-white text-xs flex items-center justify-center shrink-0">✓</span>
                            <span class="text-gray-700">{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
                <a href="{{ route('register') }}" class="mt-8 inline-block px-6 py-3 bg-aun-navy text-white rounded-full font-semibold hover:bg-aun-navy-light">Join your dorm's network</a>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-3xl font-bold text-center text-aun-navy">How it works</h2>
        <p class="mt-3 text-center text-gray-600">Three simple steps.</p>
        <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-8">
            @php
                $steps = [
                    ['1', '👕', 'Find a worker', 'Browse verified workers in your dorm, sorted by rating.'],
                    ['2', '🧾', 'Place your order', 'Pick your items and see the exact price before you book.'],
                    ['3', '🔔', 'Track & rate', 'Watch progress in real time, get notified, then rate the service.'],
                ];
            @endphp
            @foreach ($steps as [$n, $icon, $title, $desc])
                <div class="relative bg-white rounded-2xl ring-1 ring-gray-100 p-6 text-center">
                    <div class="mx-auto w-14 h-14 rounded-full bg-aun-navy text-white flex items-center justify-center text-2xl">{{ $icon }}</div>
                    <div class="absolute top-4 right-5 text-4xl font-extrabold text-gray-100">{{ $n }}</div>
                    <h3 class="mt-4 font-semibold text-aun-navy">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- CTA band with community photo -->
    <section class="relative">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('{{ asset('images/aun/orientation.jpg') }}');"></div>
        <div class="absolute inset-0 bg-aun-navy/85"></div>
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 py-20 text-center text-white">
            <h2 class="text-3xl sm:text-4xl font-extrabold">Ready for stress-free laundry?</h2>
            <p class="mt-4 text-white/85 max-w-xl mx-auto">Join the AUN students already using E-Laundry to keep their wardrobe fresh — without the hassle.</p>
            <a href="{{ route('register') }}" class="mt-8 inline-block px-8 py-3 bg-aun-orange text-white rounded-full font-semibold hover:bg-aun-orange-dark shadow-lg">Create your free account</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-aun-navy text-white/70">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="AUN E-Laundry" class="h-10 w-10 bg-white rounded-full p-0.5">
                    <span class="text-white font-semibold">AUN E-Laundry</span>
                </div>
                <div class="text-sm text-center sm:text-right">
                    Senior Design Project · School of Information Technology &amp; Computing<br>
                    American University of Nigeria, Yola
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
