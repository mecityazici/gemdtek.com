@extends('layouts.app')

@section('title', 'Çevrimdışı — '.setting('site.name', 'GEMDTEK'))
@section('meta_description', 'Şu anda çevrimdışısın. Bağlantı geri geldiğinde sayfa yenilenecek.')

@section('content')

<section class="container-tight py-24 max-w-2xl text-center">
    <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">Offline</p>
    <h1 class="font-display text-3xl md:text-4xl font-bold text-navy-800 mb-4">
        Bağlantı yok.
    </h1>
    <p class="text-graphite/80 leading-relaxed mb-8 max-w-xl mx-auto">
        Şu anda internete bağlı değilsin. Daha önce ziyaret ettiğin sayfalar önbellekten yüklenebilir; aksi halde bağlantı geri geldiğinde tekrar dene.
    </p>

    <div class="flex flex-wrap justify-center gap-3">
        <button onclick="location.reload()" class="btn-accent">Tekrar dene</button>
        <a href="/" class="btn-ghost">Anasayfa</a>
    </div>

    <div class="mt-12 text-sm text-graphite/60">
        <p>Önbellekten erişilebilecek sayfalar:</p>
        <ul class="mt-3 flex flex-wrap justify-center gap-x-6 gap-y-1">
            <li><a href="/" class="hover:text-petrol underline-offset-4 hover:underline">Anasayfa</a></li>
            <li><a href="/hakkimizda" class="hover:text-petrol underline-offset-4 hover:underline">Hakkımızda</a></li>
            <li><a href="/etkinlikler" class="hover:text-petrol underline-offset-4 hover:underline">Etkinlikler</a></li>
            <li><a href="/haberler" class="hover:text-petrol underline-offset-4 hover:underline">Haberler</a></li>
        </ul>
    </div>
</section>

@endsection
