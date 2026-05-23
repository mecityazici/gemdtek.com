<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $pageTitle       = trim($__env->yieldContent('title', 'GEMDTEK — ' . __('site.footer.tagline')));
        $pageDescription = trim($__env->yieldContent('meta_description', __('pages.home.subline')));
        $ogImage         = trim($__env->yieldContent('og_image', url('/images/og-default.png')));
        $ogType          = trim($__env->yieldContent('og_type', 'website'));
        $canonical       = url()->current();
    @endphp

    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonical }}">

    {{-- Open Graph --}}
    <meta property="og:site_name"   content="GEMDTEK">
    <meta property="og:locale"      content="{{ app()->getLocale() === 'en' ? 'en_US' : 'tr_TR' }}">
    <meta property="og:locale:alternate" content="{{ app()->getLocale() === 'en' ? 'tr_TR' : 'en_US' }}">
    <meta property="og:type"        content="{{ $ogType }}">
    <meta property="og:title"       content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url"         content="{{ $canonical }}">
    <meta property="og:image"       content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter --}}
    <meta name="twitter:card"        content="summary_large_image">
    <meta name="twitter:title"       content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image"       content="{{ $ogImage }}">

    @hasSection('no_index')
        <meta name="robots" content="noindex,nofollow">
    @endif

    {{-- Performance: preconnect to Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('head')
</head>
<body class="min-h-screen flex flex-col">

    @php
        $nav = [
            ['key' => 'about',    'href' => route('about'),          'active' => request()->routeIs('about')],
            ['key' => 'projects', 'href' => route('projects.index'), 'active' => request()->routeIs('projects.*')],
            ['key' => 'events',   'href' => route('events.index'),   'active' => request()->routeIs('events.*')],
            ['key' => 'news',     'href' => route('news.index'),     'active' => request()->routeIs('news.*')],
            ['key' => 'forms',    'href' => route('forms.index'),    'active' => request()->routeIs('forms.*')],
        ];
        $currentLocale = app()->getLocale();
    @endphp

    <header class="bg-navy-900 text-cream sticky top-0 z-50">
        <div class="container-tight flex items-center justify-between py-5">
            <a href="{{ route('home') }}" class="font-display text-xl tracking-wide">
                GEMDTEK
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm">
                @foreach ($nav as $item)
                    <a href="{{ $item['href'] }}"
                       class="transition-colors {{ $item['active'] ? 'text-brass-400' : 'hover:text-brass-300' }}">
                        {{ __('site.nav.' . $item['key']) }}
                    </a>
                @endforeach
                <div class="flex items-center gap-1 font-mono text-xs border-l border-cream/20 pl-4 ml-1">
                    @foreach (['tr', 'en'] as $loc)
                        <a href="{{ route('lang.switch', $loc) }}"
                           class="px-2 py-1 rounded {{ $currentLocale === $loc ? 'bg-brass-500 text-white' : 'text-cream/70 hover:text-cream' }}">
                            {{ strtoupper($loc) }}
                        </a>
                    @endforeach
                </div>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-navy-950 text-cream/70 py-10 mt-20">
        <div class="container-tight text-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <p>&copy; {{ date('Y') }} GEMDTEK — {{ __('site.footer.rights') }}.</p>
            <div class="flex items-center gap-6">
                <a href="{{ route('legal.privacy') }}" class="hover:text-cream">{{ __('site.footer.privacy') }}</a>
                <p class="font-mono text-xs">{{ __('site.footer.tagline') }}</p>
            </div>
        </div>
    </footer>

    @include('partials.cookie-banner')

</body>
</html>
