@extends('layouts.app')

@section('title', $event->title . ' — GEMDTEK')
@section('meta_description', $event->summary)

@section('content')

<section class="relative bg-navy-900 text-cream">
    @if ($event->cover_url)
        <div class="absolute inset-0">
            <img src="{{ $event->cover_url }}" alt="" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-900/70 to-transparent"></div>
        </div>
    @endif
    <div class="relative container-tight py-20 md:py-28">
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-brass-300 hover:text-brass-200 text-sm mb-6 font-mono">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Tüm etkinlikler
        </a>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="bg-navy-700/60 text-cream px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider">
                {{ $event->category_label }}
            </span>
            @if ($event->is_upcoming)
                <span class="bg-brass-500/30 text-brass-200 border border-brass-400/40 px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider">
                    Yaklaşıyor
                </span>
            @endif
        </div>
        <h1 class="text-3xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ $event->title }}
        </h1>
        @if ($event->summary)
            <p class="text-lg md:text-xl text-cream/80 max-w-3xl">{{ $event->summary }}</p>
        @endif
    </div>
</section>

<section class="bg-petrol text-cream">
    <div class="container-tight py-10 grid md:grid-cols-3 gap-6">
        <div>
            <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-2">Tarih</p>
            <p class="font-display text-xl font-bold">{{ $event->event_date->isoFormat('D MMMM YYYY') }}</p>
            <p class="text-cream/70 text-sm">{{ $event->event_date->isoFormat('HH:mm') }}</p>
        </div>
        @if ($event->location)
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-2">Lokasyon</p>
                <p class="font-display text-xl font-bold">{{ $event->location }}</p>
            </div>
        @endif
        @if ($event->registration_url)
            <div class="flex items-center md:justify-end">
                <a href="{{ $event->registration_url }}" target="_blank" rel="noopener" class="btn-accent">
                    Kayıt ol
                </a>
            </div>
        @endif
    </div>
</section>

@if ($event->description)
    <section class="container-tight py-20 max-w-3xl">
        <div class="prose prose-lg max-w-none prose-headings:font-display prose-headings:text-navy-800 prose-a:text-petrol">
            {!! $event->description !!}
        </div>
    </section>
@endif

@endsection
