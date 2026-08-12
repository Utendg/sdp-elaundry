<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('code') · AUN E-Laundry</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:400,600,700,800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', system-ui, sans-serif; background: #222454; color: #fff;
               min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { text-align: center; max-width: 520px; }
        .logo { width: 96px; height: 96px; background: #fff; border-radius: 9999px; padding: 6px; }
        .code { font-size: 96px; font-weight: 800; line-height: 1; color: #F75B30; margin-top: 16px; }
        h1 { font-size: 24px; font-weight: 700; margin-top: 8px; }
        p { color: rgba(255,255,255,.75); margin-top: 12px; }
        .btn { display: inline-block; margin-top: 28px; background: #F75B30; color: #fff; text-decoration: none;
               padding: 12px 24px; border-radius: 9999px; font-weight: 600; }
        .btn:hover { background: #EF6D00; }
    </style>
</head>
<body>
    <div class="card">
        <img class="logo" src="{{ asset('images/logo.svg') }}" alt="AUN E-Laundry logo">
        <div class="code">@yield('code')</div>
        <h1>@yield('title')</h1>
        <p>@yield('message')</p>
        <a class="btn" href="{{ url('/') }}">Back to home</a>
    </div>
</body>
</html>
