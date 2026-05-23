@extends('layouts.app')

@section('content')

    <section class="bg-navy-800 text-cream">
        <div class="container-tight py-24 md:py-32">
            <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">
                Sprint 0 · Platform iskeleti hazır
            </p>
            <h1 class="text-4xl md:text-6xl font-display font-bold mb-6 leading-tight">
                Gemi İnşaatı ve<br>
                <span class="text-brass-400">Deniz Teknolojileri</span> Kulübü
            </h1>
            <p class="text-lg md:text-xl text-cream/80 max-w-2xl mb-10">
                Ar-Ge takımlarımız, sektör etkinliklerimiz ve mühendislik projelerimizle
                üniversite ile denizcilik endüstrisi arasında bir köprü kuruyoruz.
            </p>
            <div class="flex flex-wrap gap-4">
                <a href="#" class="btn-accent">Ar-Ge Projelerini Keşfet</a>
                <a href="#" class="btn-primary border border-cream/20">Sponsor Ol</a>
            </div>
        </div>
    </section>

    <section class="container-tight py-20">
        <h2 class="text-2xl md:text-3xl font-display font-bold text-navy-800 mb-12 text-center">
            Marka paleti önizleme
        </h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="rounded-lg overflow-hidden shadow">
                <div class="h-24 bg-navy-800"></div>
                <div class="p-3 bg-white font-mono text-xs">
                    <p class="font-display font-semibold text-navy-800">Deep Navy</p>
                    <p class="text-graphite/60">#0B2545</p>
                </div>
            </div>
            <div class="rounded-lg overflow-hidden shadow">
                <div class="h-24 bg-petrol"></div>
                <div class="p-3 bg-white font-mono text-xs">
                    <p class="font-display font-semibold text-navy-800">Petrol Blue</p>
                    <p class="text-graphite/60">#13315C</p>
                </div>
            </div>
            <div class="rounded-lg overflow-hidden shadow">
                <div class="h-24 bg-brass-500"></div>
                <div class="p-3 bg-white font-mono text-xs">
                    <p class="font-display font-semibold text-navy-800">Brass Copper</p>
                    <p class="text-graphite/60">#B87333</p>
                </div>
            </div>
            <div class="rounded-lg overflow-hidden shadow">
                <div class="h-24 bg-graphite"></div>
                <div class="p-3 bg-white font-mono text-xs">
                    <p class="font-display font-semibold text-navy-800">Graphite</p>
                    <p class="text-graphite/60">#1F2937</p>
                </div>
            </div>
        </div>
    </section>

@endsection
