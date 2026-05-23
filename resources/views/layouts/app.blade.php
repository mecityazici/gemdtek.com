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
            ['key' => 'contact',  'href' => route('contact'),        'active' => request()->routeIs('contact')],
        ];
        $currentLocale = app()->getLocale();
    @endphp

    <div x-data="{ open: false }" @keydown.escape.window="open = false">
        <header class="bg-navy-900 text-cream sticky top-0 z-40">
            <div class="container-tight flex items-center justify-between py-5">
                <a href="{{ route('home') }}" class="font-display text-xl tracking-wide">
                    GEMDTEK
                </a>

                {{-- Desktop nav --}}
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

                {{-- Mobile hamburger --}}
                <button @click="open = true"
                        class="md:hidden p-2 -mr-2 text-cream hover:text-brass-300 transition-colors"
                        aria-label="Menüyü aç">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </header>

        {{-- Mobile drawer --}}
        <div x-show="open" x-cloak class="md:hidden fixed inset-0 z-50" x-transition.opacity>
            <div @click="open = false" class="absolute inset-0 bg-navy-950/70 backdrop-blur-sm"></div>

            <aside x-show="open"
                   x-transition:enter="transition ease-out duration-200 transform"
                   x-transition:enter-start="translate-x-full"
                   x-transition:enter-end="translate-x-0"
                   x-transition:leave="transition ease-in duration-150 transform"
                   x-transition:leave-start="translate-x-0"
                   x-transition:leave-end="translate-x-full"
                   class="absolute right-0 top-0 h-full w-80 max-w-[85vw] bg-navy-900 text-cream shadow-2xl flex flex-col">
                <div class="flex items-center justify-between p-5 border-b border-cream/10">
                    <span class="font-display text-lg">GEMDTEK</span>
                    <button @click="open = false"
                            class="p-2 -mr-2 text-cream/70 hover:text-cream"
                            aria-label="Menüyü kapat">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <nav class="flex flex-col py-4 flex-1 overflow-y-auto">
                    @foreach ($nav as $item)
                        <a href="{{ $item['href'] }}"
                           @click="open = false"
                           class="px-6 py-3 text-base font-display transition-colors {{ $item['active'] ? 'text-brass-400 bg-navy-800/50 border-l-4 border-brass-400' : 'hover:text-brass-300 hover:bg-navy-800/30' }}">
                            {{ __('site.nav.' . $item['key']) }}
                        </a>
                    @endforeach
                </nav>

                <div class="border-t border-cream/10 p-5 flex items-center gap-2 font-mono text-xs">
                    <span class="text-cream/50 uppercase tracking-wider">Dil:</span>
                    @foreach (['tr', 'en'] as $loc)
                        <a href="{{ route('lang.switch', $loc) }}"
                           class="px-3 py-1.5 rounded {{ $currentLocale === $loc ? 'bg-brass-500 text-white' : 'text-cream/70 hover:text-cream border border-cream/20' }}">
                            {{ strtoupper($loc) }}
                        </a>
                    @endforeach
                </div>
            </aside>
        </div>
    </div>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-navy-950 text-cream/70 py-10 mt-20">
        <div class="container-tight text-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <p>&copy; {{ date('Y') }} GEMDTEK — {{ __('site.footer.rights') }}.</p>
            <div class="flex flex-wrap items-center gap-6">
                <a href="{{ route('alumni.index') }}" class="hover:text-cream">{{ __('pages.alumni.eyebrow') }}</a>
                <a href="{{ route('legal.privacy') }}" class="hover:text-cream">{{ __('site.footer.privacy') }}</a>
                <p class="font-mono text-xs">{{ __('site.footer.tagline') }}</p>
            </div>
        </div>
    </footer>

    @include('partials.cookie-banner')

</body>
</html>
