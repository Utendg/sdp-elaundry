<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- SEO: unique title + meta description --}}
    <title>AUN E-Laundry | Laundry for AUN residence halls</title>
    <meta name="description" content="AUN E-Laundry connects American University of Nigeria students with approved laundry agents in their residence hall, with university prices, order tracking and ratings.">
    <link rel="canonical" href="{{ url('/') }}">

    {{-- Social share image (Open Graph + Twitter) --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="AUN E-Laundry | Laundry for AUN residence halls">
    <meta property="og:description" content="Pick a laundry agent in your hall, see the price before you book, and follow your order.">
    <meta property="og:image" content="{{ asset('images/aun/campus.jpg') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="AUN E-Laundry | Laundry for AUN residence halls">
    <meta name="twitter:description" content="University prices, order tracking and ratings for AUN campus laundry.">
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
    @include('partials.pwa-head')
    @include('partials.analytics')
</head>
<body class="antialiased bg-white text-gray-900 font-sans">
    <!-- Top bar -->
    <header class="bg-aun-navy text-white sticky top-0 z-30 border-b border-white/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="AUN E-Laundry logo" class="h-11 w-11 sm:h-14 sm:w-14 bg-white rounded-full p-0.5 shrink-0">
                <div class="leading-tight">
                    <div class="font-bold text-base sm:text-lg whitespace-nowrap">AUN E-Laundry</div>
                    <div class="hidden sm:block text-xs text-white/60">American University of Nigeria</div>
                </div>
            </div>
            <nav class="flex items-center gap-1 sm:gap-4 text-sm">
                <a href="#how" class="hidden sm:inline px-2 py-2 text-white/80 hover:text-white">How it works</a>
                <a href="#faq" class="hidden sm:inline px-2 py-2 text-white/80 hover:text-white">FAQ</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-aun-orange text-white rounded-md font-medium hover:bg-aun-orange-dark">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-2 sm:px-3 py-2 text-white/90 hover:text-white whitespace-nowrap">Log in</a>
                    <a href="{{ route('register') }}" class="px-3 sm:px-4 py-2 bg-aun-orange text-white rounded-md font-medium hover:bg-aun-orange-dark whitespace-nowrap">Register</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero -->
    <section class="relative bg-aun-navy">
        <img src="{{ asset('images/aun/campus.jpg') }}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-25">
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-20 sm:py-28">
            <div class="max-w-2xl text-white">
                <p class="text-sm font-medium uppercase tracking-wider text-white/70 mb-4">American University of Nigeria, Yola</p>
                <h1 class="text-4xl sm:text-5xl font-bold leading-tight">
                    Laundry for AUN residence halls, booked and tracked online.
                </h1>
                <p class="mt-6 text-lg text-white/85">
                    Pick a laundry agent in your hall, see the university price before you book,
                    and follow your order from pickup to return.
                </p>
                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <a href="{{ route('register') }}" class="px-5 py-3 bg-aun-orange text-white rounded-md font-semibold hover:bg-aun-orange-dark">Create an account</a>
                    <a href="{{ route('login') }}" class="px-5 py-3 border border-white/50 text-white rounded-md font-semibold hover:bg-white/10">Log in</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-aun-navy">What the platform does</h2>
        <p class="mt-3 text-gray-600 max-w-2xl">It replaces the cash-on-return arrangement with the hall laundry agents with a record both sides can check.</p>
        @php
            $features = [
                ['tag', 'University prices', 'Each item is charged at the rate set by Residence Life. The total is shown before you place the order.'],
                ['map-pin', 'Order tracking', 'The agent updates the order as it moves from pickup to washing, ironing and ready for return.'],
                ['star', 'Two-way ratings', 'Students rate agents and agents rate students after each completed order.'],
                ['shield-check', 'Approved agents', 'An agent account can only take orders after an administrator approves it.'],
            ];
        @endphp
        <dl class="mt-10 grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-8">
            @foreach ($features as [$icon, $title, $desc])
                <div class="flex gap-4 border-t border-gray-200 pt-6">
                    <x-dynamic-component :component="'heroicon-o-'.$icon" class="w-6 h-6 text-aun-orange shrink-0 mt-0.5" aria-hidden="true" />
                    <div>
                        <dt class="font-semibold text-aun-navy">{{ $title }}</dt>
                        <dd class="mt-1 text-sm text-gray-600">{{ $desc }}</dd>
                    </div>
                </div>
            @endforeach
        </dl>
    </section>

    <!-- Image split -->
    <section class="bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
            <img src="{{ asset('images/aun/students.jpg') }}" alt="American University of Nigeria students on campus" class="w-full h-72 sm:h-96 object-cover rounded-md">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold text-aun-navy">Organised by residence hall</h2>
                <p class="mt-4 text-gray-600">Each of the nine halls has its own laundry agent. You see the agents in your hall first, and the Residence Life admins can follow every order.</p>
                <ul class="mt-6 space-y-3">
                    @foreach ([
                        'Agents in your hall are listed by rating',
                        'The price is fixed before you book',
                        'Complaints go to the Residence Life admins',
                    ] as $point)
                        <li class="flex items-start gap-3">
                            <x-heroicon-m-check class="mt-0.5 w-5 h-5 text-aun-green shrink-0" aria-hidden="true" />
                            <span class="text-gray-700">{{ $point }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    <!-- How it works -->
    <section id="how" class="max-w-6xl mx-auto px-4 sm:px-6 py-16 scroll-mt-24">
        <h2 class="text-2xl sm:text-3xl font-bold text-aun-navy">How an order works</h2>
        @php
            $steps = [
                ['Choose an agent', 'Open the list of approved agents in your hall and pick one.'],
                ['Place the order', 'Add your items. The total uses the university price list.'],
                ['Follow and rate', 'You get a notification at each stage. Rate the agent when your clothes are returned.'],
            ];
        @endphp
        <ol class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-8">
            @foreach ($steps as [$title, $desc])
                <li class="border-t-2 border-aun-navy pt-4">
                    <span class="text-sm font-semibold text-aun-orange">Step {{ $loop->iteration }}</span>
                    <h3 class="mt-1 font-semibold text-aun-navy">{{ $title }}</h3>
                    <p class="mt-1 text-sm text-gray-600">{{ $desc }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <!-- Reviews: only shown once real students have left them -->
    @if ($reviews->isNotEmpty())
        <section class="bg-gray-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
                <h2 class="text-2xl sm:text-3xl font-bold text-aun-navy">Recent reviews</h2>
                <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-6">
                    @foreach ($reviews as $review)
                        <figure class="bg-white border border-gray-200 rounded-md p-6">
                            <x-stars :value="$review->stars" />
                            <blockquote class="mt-3 text-gray-700">“{{ $review->comment }}”</blockquote>
                            <figcaption class="mt-3 text-sm text-gray-500">Student review</figcaption>
                        </figure>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Team -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
        <h2 class="text-2xl sm:text-3xl font-bold text-aun-navy">Project team</h2>
        <p class="mt-3 text-gray-600">Final-year students, School of IT &amp; Computing, AUN.</p>
        <ul class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-6">
            @php
                $team = [
                    ['Ibrahim Abdulmajeed Ibrahim', 'A00024501'],
                    ['Audu David Utennami', 'A00023995'],
                    ['Vanje Kefas Zawaya', 'A00024352'],
                ];
            @endphp
            @foreach ($team as [$name, $id])
                @php $initials = collect(explode(' ', $name))->map(fn ($p) => mb_substr($p, 0, 1))->take(2)->implode(''); @endphp
                <li class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-aun-navy text-white flex items-center justify-center font-bold shrink-0">{{ $initials }}</div>
                    <div>
                        <div class="font-semibold text-aun-navy">{{ $name }}</div>
                        <div class="text-xs text-gray-500">{{ $id }}</div>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    <!-- FAQ -->
    <section id="faq" class="bg-gray-50 scroll-mt-24">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16">
            <h2 class="text-2xl sm:text-3xl font-bold text-aun-navy">Questions</h2>
            <div class="mt-8 divide-y divide-gray-200 border-y border-gray-200">
                @php
                    $faqs = [
                        ['Who can use AUN E-Laundry?', 'Any AUN student can register to place laundry orders, and campus laundry agents can register to offer their services. Agent accounts are reviewed by an administrator before they can take orders.'],
                        ['How is the price decided?', 'Residence Life sets a price for each item, so you pay the official rate. You see the exact total before you place an order.'],
                        ['How do I know an agent is reliable?', 'Every agent is approved by an admin, and each has a rating and reviews from other students.'],
                        ['What if an item is damaged or missing?', 'You can file a complaint against the order. Administrators review complaints and settle disputes.'],
                        ['Does the platform charge a fee?', 'No. Creating an account is free. You only pay for the laundry itself, at the university rate.'],
                    ];
                @endphp
                @foreach ($faqs as [$q, $a])
                    <div x-data="{ open: false }">
                        <button @click="open = !open" :aria-expanded="open" class="w-full flex items-center justify-between gap-4 text-left py-4">
                            <span class="font-medium text-aun-navy">{{ $q }}</span>
                            <x-heroicon-m-chevron-down class="w-5 h-5 text-gray-500 shrink-0 transition-transform" x-bind:class="open && 'rotate-180'" aria-hidden="true" />
                        </button>
                        <div x-show="open" x-cloak class="pb-4 text-gray-600 text-sm">{{ $a }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Location / maps + directions -->
    <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16 grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
        <div>
            <h2 class="text-2xl sm:text-3xl font-bold text-aun-navy">Location</h2>
            <p class="mt-4 text-gray-600">The service runs on the American University of Nigeria campus in Yola, Adamawa State.</p>
            <address class="mt-4 not-italic text-gray-700">
                American University of Nigeria<br>
                98 Lamido Zubairu Way, Yola Township Bypass<br>
                Yola, Adamawa State, Nigeria
            </address>
            <a href="https://www.google.com/maps/dir/?api=1&destination=American+University+of+Nigeria+Yola"
               target="_blank" rel="noopener"
               class="mt-6 inline-flex items-center gap-2 px-5 py-3 bg-aun-navy text-white rounded-md font-semibold hover:bg-aun-navy-light">
                Get directions <x-heroicon-m-arrow-top-right-on-square class="w-4 h-4" aria-hidden="true" />
            </a>
        </div>
        <div class="rounded-md overflow-hidden border border-gray-200">
            <iframe title="Map to American University of Nigeria"
                    src="https://maps.google.com/maps?q=American%20University%20of%20Nigeria%20Yola&t=&z=14&ie=UTF8&iwloc=&output=embed"
                    class="w-full h-72 border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

    <!-- Closing call to action -->
    <section class="bg-aun-navy">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-14 flex flex-col sm:flex-row sm:items-center justify-between gap-6 text-white">
            <div>
                <h2 class="text-2xl font-bold">Live in an AUN hall?</h2>
                <p class="mt-2 text-white/80">Register as a student and choose your residence hall.</p>
            </div>
            <a href="{{ route('register') }}" class="self-start sm:self-auto px-5 py-3 bg-aun-orange text-white rounded-md font-semibold hover:bg-aun-orange-dark whitespace-nowrap">Create an account</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-aun-navy text-white/70 border-t border-white/10">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo.svg') }}" alt="AUN E-Laundry logo" class="h-10 w-10 bg-white rounded-full p-0.5">
                    <span class="text-white font-semibold">AUN E-Laundry</span>
                </div>
                <nav class="flex flex-wrap justify-center items-center gap-5 text-sm">
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
        <div class="sm:hidden fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 p-3 flex gap-2">
            <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-3 border border-aun-navy text-aun-navy rounded-md font-semibold">Log in</a>
            <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-3 bg-aun-orange text-white rounded-md font-semibold">Register</a>
        </div>
        <div class="sm:hidden h-20"></div>
    @endguest
    @include('partials.install-banner')
</body>
</html>
