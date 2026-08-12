<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO: unique title + meta description --}}
    <title>AUN E-Laundry — Campus laundry, done right</title>
    <meta name="description" content="AUN E-Laundry connects American University of Nigeria students with verified laundry workers in their dorm — transparent university pricing, real-time order tracking, and trusted reviews.">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Social share image (Open Graph + Twitter) --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="AUN E-Laundry — Campus laundry, done right">
    <meta property="og:description" content="Find a trusted laundry worker in your dorm, see the exact price up front, and track every step.">
    <meta property="og:image" content="{{ asset('images/aun/campus.jpg') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="AUN E-Laundry — Campus laundry, done right">
    <meta name="twitter:description" content="Transparent pricing, real-time tracking, and trusted reviews for AUN campus laundry.">
    <meta name="twitter:image" content="{{ asset('images/aun/campus.jpg') }}">

    {{-- Local business structured data (built as an array to avoid Blade @-directive collisions) --}}
    @php
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'LocalBusiness',
            'name' => 'AUN E-Laundry',
            'description' => 'Centralised campus laundry platform for the American University of Nigeria.',
            'url' => url('/'),
            'image' => asset('images/aun/campus.jpg'),
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => '98 Lamido Zubairu Way, Yola Township Bypass',
                'addressLocality' => 'Yola',
                'addressRegion' => 'Adamawa',
                'addressCountry' => 'NG',
            ],
            'parentOrganization' => [
                '@type' => 'CollegeOrUniversity',
                'name' => 'American University of Nigeria',
            ],
        ];
    @endphp
    <script type="application/ld+json">
        {!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
    </script>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics')
</head>
<body class="antialiased bg-white text-gray-900 font-sans">
    <!-- Top bar -->
    <header class="bg-aun-navy text-white sticky top-0 z-30 shadow-md">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="AUN E-Laundry logo" class="h-14 w-14 bg-white rounded-full p-0.5">
                <div class="leading-tight">
                    <div class="font-bold text-lg">AUN E-Laundry</div>
                    <div class="text-xs text-white/60">American University of Nigeria</div>
                </div>
            </div>
            <nav class="flex items-center gap-1 sm:gap-4 text-sm">
                <a href="#how" class="hidden sm:inline px-2 py-2 text-white/80 hover:text-white">How it works</a>
                <a href="#faq" class="hidden sm:inline px-2 py-2 text-white/80 hover:text-white">FAQ</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-aun-orange text-white rounded-full font-medium hover:bg-aun-orange-dark">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 text-white/90 hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="px-4 py-2 bg-aun-orange text-white rounded-full font-medium hover:bg-aun-orange-dark">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative">
        <div class="absolute inset-0 bg-cover bg-center" style="background-image:url('{{ asset('images/aun/campus.jpg') }}');"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-aun-navy/95 via-aun-navy/85 to-aun-navy/60"></div>
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-24 sm:py-32">
            <div class="max-w-2xl text-white">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-sm text-white/90 mb-5">🧺 Built for AUN resident life</span>
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
                <p class="mt-4 text-sm text-white/70">⏱️ Workers typically respond to new orders within a few hours.</p>
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

    <!-- Image split -->
    <section class="bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <div class="rounded-3xl overflow-hidden shadow-lg">
                <img src="{{ asset('images/aun/students.jpg') }}" alt="American University of Nigeria students on campus" class="w-full h-72 sm:h-96 object-cover">
            </div>
            <div>
                <h2 class="text-3xl font-bold text-aun-navy">Made for campus life</h2>
                <p class="mt-4 text-gray-600">Whether you're a freshman who just moved in or a final-year student with a packed schedule, E-Laundry connects you to the right people — right in your hall.</p>
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
    <section id="how" class="max-w-6xl mx-auto px-4 sm:px-6 py-16 scroll-mt-24">
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

    <!-- Reviews -->
    <section class="bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-3xl font-bold text-center text-aun-navy">What students say</h2>
            <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6">
                @php
                    $testimonials = $reviews->isNotEmpty()
                        ? $reviews->map(fn ($r) => ['stars' => $r->stars, 'text' => $r->comment, 'name' => 'Verified student'])->all()
                        : [
                            ['stars' => 5, 'text' => 'Booked in minutes and my clothes came back the same day. No haggling over price!', 'name' => 'Student, New Dorm A'],
                            ['stars' => 5, 'text' => 'As a fresher I had no idea who to trust. The ratings made it so easy.', 'name' => 'Student, Ladies Village'],
                            ['stars' => 4, 'text' => 'Love being able to track my order and get a notification when it is ready.', 'name' => 'Student, Old Dorm 2'],
                        ];
                @endphp
                @foreach ($testimonials as $t)
                    <div class="bg-white rounded-2xl ring-1 ring-gray-100 p-6">
                        <div class="text-aun-orange text-lg">{{ str_repeat('★', (int) $t['stars']) }}<span class="text-gray-200">{{ str_repeat('★', 5 - (int) $t['stars']) }}</span></div>
                        <p class="mt-3 text-gray-700">“{{ $t['text'] }}”</p>
                        <div class="mt-3 text-sm font-medium text-aun-navy">— {{ $t['name'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Team -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-3xl font-bold text-center text-aun-navy">Meet the team</h2>
        <p class="mt-3 text-center text-gray-600">Built by final-year students at AUN's School of IT &amp; Computing.</p>
        <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6 max-w-3xl mx-auto">
            @php
                $team = [
                    ['Ibrahim Abdulmajeed Ibrahim', 'A00024501'],
                    ['Audu David Utennami', 'A00023995'],
                    ['Vanje Kefas Zawaya', 'A00024352'],
                ];
            @endphp
            @foreach ($team as [$name, $id])
                @php $initials = collect(explode(' ', $name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode(''); @endphp
                <div class="bg-white rounded-2xl ring-1 ring-gray-100 p-6 text-center">
                    <div class="mx-auto w-16 h-16 rounded-full bg-aun-navy text-white flex items-center justify-center text-xl font-bold">{{ $initials }}</div>
                    <div class="mt-3 font-semibold text-aun-navy">{{ $name }}</div>
                    <div class="text-xs text-gray-500">{{ $id }}</div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="bg-gray-50 scroll-mt-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-3xl font-bold text-center text-aun-navy">Frequently asked questions</h2>
            <div class="mt-10 space-y-3">
                @php
                    $faqs = [
                        ['Who can use AUN E-Laundry?', 'Any AUN student can register to place laundry orders, and campus laundry workers can register to offer their services. Worker accounts are reviewed by an administrator before they can take orders.'],
                        ['How is the price decided?', 'Prices are set by the university administration per item, so you always pay the official rate. You see the exact total before you place an order.'],
                        ['How do I know a worker is trustworthy?', 'Every worker is approved by an admin, and each carries a public rating and reviews from other students so you can choose with confidence.'],
                        ['What if an item is damaged or missing?', 'You can file a complaint against an order. Administrators review complaints and can resolve disputes, keeping everyone accountable.'],
                        ['How much does it cost to use the platform?', 'Creating an account and using E-Laundry is free — you only pay for the laundry service itself, at the official university rate.'],
                    ];
                @endphp
                @foreach ($faqs as [$q, $a])
                    <div class="bg-white rounded-xl ring-1 ring-gray-100" x-data="{ open: false }">
                        <button @click="open = !open" class="w-full flex items-center justify-between text-left px-5 py-4">
                            <span class="font-medium text-aun-navy">{{ $q }}</span>
                            <span class="text-aun-orange text-xl" x-text="open ? '−' : '+'"></span>
                        </button>
                        <div x-show="open" x-cloak class="px-5 pb-4 text-gray-600 text-sm">{{ $a }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Location / maps + directions -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="text-3xl font-bold text-aun-navy">Find us on campus</h2>
            <p class="mt-4 text-gray-600">AUN E-Laundry serves the American University of Nigeria community in Yola, Adamawa State.</p>
            <address class="mt-4 not-italic text-gray-700">
                American University of Nigeria<br>
                98 Lamido Zubairu Way, Yola Township Bypass<br>
                Yola, Adamawa State, Nigeria
            </address>
            <a href="https://www.google.com/maps/dir/?api=1&destination=American+University+of+Nigeria+Yola"
               target="_blank" rel="noopener"
               class="mt-6 inline-block px-6 py-3 bg-aun-navy text-white rounded-full font-semibold hover:bg-aun-navy-light">Get directions →</a>
        </div>
        <div class="rounded-3xl overflow-hidden shadow-lg ring-1 ring-gray-100">
            <iframe title="Map to American University of Nigeria"
                    src="https://maps.google.com/maps?q=American%20University%20of%20Nigeria%20Yola&t=&z=14&ie=UTF8&iwloc=&output=embed"
                    class="w-full h-72 border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

    <!-- CTA band -->
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
                    <img src="{{ asset('images/logo.svg') }}" alt="AUN E-Laundry logo" class="h-10 w-10 bg-white rounded-full p-0.5">
                    <span class="text-white font-semibold">AUN E-Laundry</span>
                </div>
                <nav class="flex items-center gap-5 text-sm">
                    <a href="#how" class="hover:text-white">How it works</a>
                    <a href="#faq" class="hover:text-white">FAQ</a>
                    <a href="{{ route('privacy') }}" class="hover:text-white">Privacy</a>
                    <a href="{{ route('login') }}" class="hover:text-white">Log in</a>
                    <a href="{{ route('register') }}" class="hover:text-white">Register</a>
                </nav>
            </div>
            <div class="mt-6 pt-6 border-t border-white/10 text-center text-sm">
                Senior Design Project · School of Information Technology &amp; Computing · American University of Nigeria, Yola
            </div>
        </div>
    </footer>

    <!-- Sticky mobile CTA -->
    @guest
        <div class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 p-3 flex gap-2 shadow-[0_-4px_12px_rgba(0,0,0,0.06)]">
            <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-3 border border-aun-navy text-aun-navy rounded-full font-semibold">Log in</a>
            <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-3 bg-aun-orange text-white rounded-full font-semibold">Get started</a>
        </div>
        <div class="sm:hidden h-20"></div>
    @endguest
</body>
</html>
