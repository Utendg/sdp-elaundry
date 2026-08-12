<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Privacy Policy · AUN E-Laundry</title>
    <meta name="description" content="How AUN E-Laundry collects, uses, and protects your personal information.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 text-gray-900 font-sans">
    <header class="bg-aun-navy text-white">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 h-20 flex items-center gap-3">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('images/logo.svg') }}" alt="AUN E-Laundry logo" class="h-12 w-12 bg-white rounded-full p-0.5">
                <span class="font-bold text-lg">AUN E-Laundry</span>
            </a>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 sm:px-6 py-12">
        <h1 class="text-3xl font-bold text-aun-navy">Privacy Policy</h1>
        <p class="mt-2 text-sm text-gray-500">Last updated: {{ now()->format('F Y') }}</p>

        <div class="mt-8 space-y-8 text-gray-700 leading-relaxed">
            <section>
                <h2 class="text-lg font-semibold text-aun-navy">1. Who we are</h2>
                <p class="mt-2">AUN E-Laundry is a campus laundry-management platform operated as a Senior Design Project at the American University of Nigeria (AUN), Yola. It connects students with verified laundry workers under the oversight of university administrators.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-aun-navy">2. Information we collect</h2>
                <p class="mt-2">We collect the information you provide when you register and use the service, including your name, email address, phone number, residence hall, and the details of the laundry orders, ratings, and complaints you create.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-aun-navy">3. How we use your information</h2>
                <p class="mt-2">Your information is used to operate the platform: to connect students and workers, calculate order prices at the official university rate, track order progress, deliver notifications, and allow administrators to resolve disputes and ensure service quality.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-aun-navy">4. Who can see your information</h2>
                <p class="mt-2">Workers see the order details and contact information necessary to fulfil your order. Students see workers' public profiles and reviews. Administrators can view accounts, orders, ratings, and complaints for oversight. We do not sell your personal information.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-aun-navy">5. Data security</h2>
                <p class="mt-2">Passwords are stored using strong one-way hashing, and access to each area is restricted by role. We take reasonable measures to protect your data, though no system can be guaranteed perfectly secure.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-aun-navy">6. Your choices</h2>
                <p class="mt-2">You may update your profile information at any time from your account settings. To request deletion of your account or data, contact a platform administrator.</p>
            </section>
            <section>
                <h2 class="text-lg font-semibold text-aun-navy">7. Contact</h2>
                <p class="mt-2">Questions about this policy can be directed to the AUN E-Laundry administration team through the university's School of Information Technology and Computing.</p>
            </section>
        </div>

        <a href="{{ url('/') }}" class="mt-10 inline-block text-sm text-aun-navy hover:underline">← Back to home</a>
    </main>

    <footer class="bg-aun-navy text-white/60 text-center text-sm py-6">
        AUN E-Laundry — American University of Nigeria, Yola.
    </footer>
</body>
</html>
