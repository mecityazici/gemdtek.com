<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'GEMDTEK — Gemi İnşaatı ve Deniz Teknolojileri Kulübü')</title>
    <meta name="description" content="@yield('meta_description', 'GEMDTEK; Ar-Ge takımları, sektör etkinlikleri ve kurumsal partnerlikleriyle gemi inşaatı ve deniz teknolojileri alanında öğrenci platformu.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col">

    <header class="bg-navy-900 text-cream">
        <div class="container-tight flex items-center justify-between py-5">
            <a href="{{ url('/') }}" class="font-display text-xl tracking-wide">
                GEMDTEK
            </a>
            <nav class="hidden md:flex items-center gap-8 text-sm">
                <a href="#" class="hover:text-brass-300">Hakkımızda</a>
                <a href="#" class="hover:text-brass-300">Ar-Ge & Projeler</a>
                <a href="#" class="hover:text-brass-300">Etkinlikler</a>
                <a href="#" class="hover:text-brass-300">Başvurular</a>
                <a href="#" class="hover:text-brass-300">İletişim</a>
            </nav>
        </div>
    </header>

    <main class="flex-1">
        @yield('content')
    </main>

    <footer class="bg-navy-950 text-cream/70 py-10 mt-20">
        <div class="container-tight text-sm flex flex-col md:flex-row items-center justify-between gap-4">
            <p>&copy; {{ date('Y') }} GEMDTEK — Tüm hakları saklıdır.</p>
            <p class="font-mono text-xs">v0.1 · scaffold</p>
        </div>
    </footer>

</body>
</html>
