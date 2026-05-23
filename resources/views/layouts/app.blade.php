<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GEMDTEK — ' . __('site.footer.tagline'))</title>
    <meta name="description" content="@yield('meta_description', __('pages.home.subline'))">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
        <div class="container-tight text-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} GEMDTEK — {{ __('site.footer.rights') }}.</p>
            <p class="font-mono text-xs">{{ __('site.footer.tagline') }}</p>
        </div>
    </footer>

</body>
</html>
