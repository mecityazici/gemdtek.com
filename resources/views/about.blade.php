@extends('layouts.app')

@section('title', __('pages.about.eyebrow') . ' — '.setting('site.name', 'GEMDTEK'))

@section('content')

{{-- HERO INTRO --------------------------------------------------------- --}}
<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.about.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ __('pages.about.headline_lead') }} <span class="text-brass-400">{{ __('pages.about.headline_accent') }}</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">
            {{ __('pages.about.subline') }}
        </p>
    </div>
</section>

{{-- MİSYON VİZYON ------------------------------------------------------ --}}
<section class="container-tight py-20 grid md:grid-cols-2 gap-12">
    <div>
        <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">{{ __('pages.about.mission_eyebrow') }}</p>
        <h2 class="text-2xl md:text-3xl font-display font-bold text-navy-800 mb-4">
            {{ __('pages.about.mission_title') }}
        </h2>
        <p class="text-graphite/80 leading-relaxed">
            {{ __('pages.about.mission_body') }}
        </p>
    </div>
    <div>
        <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">{{ __('pages.about.vision_eyebrow') }}</p>
        <h2 class="text-2xl md:text-3xl font-display font-bold text-navy-800 mb-4">
            {{ __('pages.about.vision_title') }}
        </h2>
        <p class="text-graphite/80 leading-relaxed">
            {{ __('pages.about.vision_body') }}
        </p>
    </div>
</section>

{{-- YÖNETİM KARTLARI ---------------------------------------------------- --}}
<section class="bg-cream py-20">
    <div class="container-tight">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3 text-center">{{ __('pages.about.team_eyebrow') }}</p>
        <h2 class="text-3xl md:text-4xl font-display font-bold text-navy-800 mb-12 text-center">
            {{ __('pages.about.team_title') }}
        </h2>

        @if ($team->isEmpty())
            <p class="text-center text-graphite/60 italic">{{ __('pages.about.team_empty') }}</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($team as $member)
                    <article class="bg-white rounded-xl overflow-hidden shadow-sm border border-graphite/5 hover:shadow-lg transition-shadow">
                        <div class="aspect-square bg-navy-50 flex items-center justify-center overflow-hidden">
                            @if ($member->photo_url)
                                <img src="{{ $member->photo_web_url }}" srcset="{{ $member->photo_thumb_url }} 160w, {{ $member->photo_web_url }} 400w" sizes="(max-width: 768px) 50vw, 25vw" alt="{{ $member->name }}" loading="lazy" decoding="async" width="400" height="400" class="w-full h-full object-cover">
                            @else
                                <span class="font-display text-5xl text-navy-300 font-bold">
                                    {{ collect(explode(' ', $member->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}
                                </span>
                            @endif
                        </div>
                        <div class="p-5">
                            <h3 class="font-display font-bold text-lg text-navy-800">{{ $member->name }}</h3>
                            <p class="text-brass-600 text-sm font-medium mt-1">{{ $member->position }}</p>
                            @if ($member->bio)
                                <p class="text-graphite/70 text-sm mt-3 leading-relaxed">{{ $member->bio }}</p>
                            @endif
                            @if ($member->linkedin_url)
                                <a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-4 text-sm font-medium text-petrol hover:text-navy-800">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                    LinkedIn
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
</section>

{{-- TIMELINE ----------------------------------------------------------- --}}
<section class="container-tight py-20">
    <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3 text-center">{{ __('pages.about.timeline_eyebrow') }}</p>
    <h2 class="text-3xl md:text-4xl font-display font-bold text-navy-800 mb-12 text-center">
        {{ __('pages.about.timeline_title') }}
    </h2>

    @if ($timeline->isEmpty())
        <p class="text-center text-graphite/60 italic">{{ __('pages.about.timeline_empty') }}</p>
    @else
        <div class="relative max-w-3xl mx-auto pl-12 md:pl-20">
            <div class="absolute left-3 md:left-6 top-2 bottom-2 w-0.5 bg-navy-200"></div>

            @foreach ($timeline as $event)
                <div class="relative mb-10 last:mb-0">
                    <div class="absolute -left-12 md:-left-20 top-1 flex items-center justify-center">
                        <div class="w-6 h-6 rounded-full bg-brass-500 ring-4 ring-cream relative z-10"></div>
                    </div>
                    <p class="font-mono text-sm text-brass-600 font-medium mb-1">{{ $event->year }}</p>
                    <h3 class="font-display font-bold text-xl text-navy-800 mb-2">{{ $event->title }}</h3>
                    @if ($event->description)
                        <p class="text-graphite/70 leading-relaxed">{{ $event->description }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</section>

{{-- ALUMNI CTA --}}
<section class="bg-petrol text-cream py-16">
    <div class="container-tight md:flex md:items-center md:justify-between gap-8">
        <div class="mb-6 md:mb-0">
            <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-2">{{ __('pages.alumni.eyebrow') }}</p>
            <h2 class="font-display text-2xl md:text-3xl font-bold mb-2">
                {{ __('pages.alumni.headline_lead') }} <span class="text-brass-400">{{ __('pages.alumni.headline_accent') }}</span>
            </h2>
            <p class="text-cream/80 max-w-2xl">{{ __('pages.alumni.subline') }}</p>
        </div>
        <a href="{{ route('alumni.index') }}" class="btn-accent shrink-0">
            {{ __('pages.alumni.cta_about') }}
        </a>
    </div>
</section>

@endsection
