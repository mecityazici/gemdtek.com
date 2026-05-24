@extends('layouts.app')

@section('content')

{{-- HERO --------------------------------------------------------------- --}}
<section class="relative overflow-hidden bg-navy-900 text-cream">
    <div class="absolute inset-0 opacity-30 bg-[radial-gradient(ellipse_at_top_right,_var(--tw-gradient-stops))] from-petrol via-navy-900 to-navy-950"></div>
    <div class="absolute inset-0 bg-[url('data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22 viewBox=%220 0 100 100%22><path d=%22M0 50 Q 25 30 50 50 T 100 50%22 fill=%22none%22 stroke=%22%23ffffff%22 stroke-opacity=%220.05%22 stroke-width=%221%22/></svg>')] opacity-50"></div>

    <div class="relative container-tight py-24 md:py-36 animate-fade-up">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-5">
            {{ __('pages.home.eyebrow') }}
        </p>
        <h1 class="text-4xl md:text-6xl lg:text-7xl font-display font-bold mb-6 leading-[1.05] max-w-4xl">
            {{ __('pages.home.headline_lead') }}
            <span class="text-brass-400">{{ __('pages.home.headline_accent') }}</span>
        </h1>
        <p class="text-lg md:text-xl text-cream/80 max-w-2xl mb-10">
            {{ __('pages.home.subline') }}
        </p>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('sponsor.show') }}" class="btn-accent">{{ __('pages.home.cta_sponsor') }}</a>
            <a href="{{ route('about') }}" class="btn-primary border border-cream/20">{{ __('pages.home.cta_explore') }}</a>
        </div>
    </div>
</section>

{{-- METRİK SAYAÇ ------------------------------------------------------- --}}
@php
    $fallbackMetrics = collect([
        (object) ['key' => 'members',  'value' => 120, 'label' => __('pages.home.metrics.members')],
        (object) ['key' => 'projects', 'value' =>  28, 'label' => __('pages.home.metrics.projects')],
        (object) ['key' => 'events',   'value' =>  14, 'label' => __('pages.home.metrics.events')],
        (object) ['key' => 'partners', 'value' =>  22, 'label' => __('pages.home.metrics.partners')],
    ]);
    $displayMetrics = $metrics->isNotEmpty() ? $metrics : $fallbackMetrics;
@endphp
<section class="bg-cream py-16">
    <div class="container-tight grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
        @foreach ($displayMetrics as $stat)
            <div>
                <p class="font-display text-5xl md:text-6xl font-bold text-navy-800 tabular-nums">
                    {{ $stat->value }}+
                </p>
                <p class="mt-2 text-sm uppercase tracking-wider text-graphite/60">
                    {{ $stat->label }}
                </p>
            </div>
        @endforeach
    </div>
</section>

{{-- GERİ SAYIM CTA ----------------------------------------------------- --}}
@if ($nextEvent)
    <section class="bg-petrol text-cream py-20">
        <div class="container-tight md:flex md:items-center md:justify-between gap-12">
            <div class="md:max-w-xl mb-8 md:mb-0">
                <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-3">
                    {{ __('pages.home.event_eyebrow') }}
                </p>
                <h2 class="text-3xl md:text-4xl font-display font-bold mb-3">
                    {{ $nextEventTitle }}
                </h2>
                @if ($nextEvent->summary)
                    <p class="text-cream/80">{{ $nextEvent->summary }}</p>
                @endif
            </div>

            <div x-data="countdown('{{ $nextEventDate }}')" x-init="start()" class="grid grid-cols-4 gap-3 text-center min-w-[280px] md:min-w-[360px]">
                @foreach (['days', 'hours', 'minutes', 'seconds'] as $key)
                    <div class="bg-navy-900/50 rounded-lg py-4 px-2">
                        <p class="font-display text-3xl md:text-4xl font-bold tabular-nums" x-text="String({{ $key }}).padStart(2, '0')">--</p>
                        <p class="text-[10px] uppercase tracking-wider text-cream/60 mt-1">{{ __('pages.home.countdown.' . $key) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="container-tight mt-10 flex flex-wrap gap-3">
            <a href="{{ route('events.show', $nextEvent) }}" class="btn-accent">{{ __('site.cta.details') }}</a>
            @if ($nextEvent->registration_url)
                <a href="{{ $nextEvent->registration_url }}" target="_blank" rel="noopener" class="btn-primary border border-cream/20">{{ __('site.cta.register') }}</a>
            @endif
        </div>
    </section>
@endif

{{-- SPONSOR BANDI ------------------------------------------------------ --}}
<section id="sponsorlar" class="bg-cream py-20">
    <div class="container-tight mb-10 text-center">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">{{ __('pages.home.sponsor_eyebrow') }}</p>
        <h2 class="text-3xl md:text-4xl font-display font-bold text-navy-800">
            {{ __('pages.home.sponsor_title') }}
        </h2>
        <p class="mt-3 text-graphite/70 max-w-2xl mx-auto">
            {{ __('pages.home.sponsor_subline') }}
        </p>
    </div>

    @if ($sponsors->isEmpty())
        <p class="text-center text-graphite/60 italic">{{ __('pages.home.sponsor_empty') }}</p>
    @else
        <div class="relative overflow-hidden">
            <div class="flex w-max gap-12 animate-scroll-x">
                @foreach ($sponsors->concat($sponsors) as $sponsor)
                    <a
                        href="{{ $sponsor->url ?: '#' }}"
                        target="_blank"
                        rel="noopener"
                        class="flex items-center justify-center w-48 h-24 bg-white rounded-lg shadow-sm border border-graphite/5 px-4 hover:shadow-md transition-shadow"
                    >
                        @if ($sponsor->logo_url)
                            <img src="{{ $sponsor->logo_thumb_url }}" alt="{{ $sponsor->name }}" loading="lazy" decoding="async" width="160" height="80" class="max-h-14 max-w-full object-contain">
                        @else
                            <span class="font-display font-semibold text-navy-800 text-center text-sm">{{ $sponsor->name }}</span>
                        @endif
                    </a>
                @endforeach
            </div>
        </div>
    @endif
</section>

{{-- COUNTDOWN SCRIPT --------------------------------------------------- --}}
<script>
    function countdown(targetIso) {
        return {
            days: 0, hours: 0, minutes: 0, seconds: 0,
            timer: null,
            tick() {
                const diff = new Date(targetIso) - new Date();
                if (diff <= 0) {
                    this.days = this.hours = this.minutes = this.seconds = 0;
                    clearInterval(this.timer);
                    return;
                }
                this.days    = Math.floor(diff / 86400000);
                this.hours   = Math.floor((diff % 86400000) / 3600000);
                this.minutes = Math.floor((diff % 3600000) / 60000);
                this.seconds = Math.floor((diff % 60000) / 1000);
            },
            start() {
                this.tick();
                this.timer = setInterval(() => this.tick(), 1000);
            },
        }
    }
</script>

@endsection
