@extends('layouts.app')

@section('title', __('pages.events.eyebrow') . ' — '.setting('site.name', 'GEMDTEK'))

@php
    $categories = \App\Models\Event::CATEGORIES;
@endphp

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.events.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ __('pages.events.headline_lead') }} <span class="text-brass-400">{{ __('pages.events.headline_accent') }}</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">
            {{ __('pages.events.subline') }}
        </p>
    </div>
</section>

<section class="container-tight py-10">
    <div class="flex flex-wrap items-center gap-2">
        <a href="{{ route('events.index') }}"
           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $activeCat === '' ? 'bg-navy-800 text-cream' : 'bg-cream text-navy-800 hover:bg-navy-100' }}">
            {{ __('site.cta.all') }}
        </a>
        @foreach ($categories as $key => $label)
            <a href="{{ route('events.index', ['cat' => $key]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $activeCat === $key ? 'bg-navy-800 text-cream' : 'bg-cream text-navy-800 hover:bg-navy-100' }}">
                {{ $label }}
            </a>
        @endforeach
        <a href="{{ route('events.calendar') }}"
           class="ml-auto inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium bg-petrol text-cream hover:bg-navy-800 transition-colors"
           aria-label="{{ __('pages.events.calendar.view_calendar') }}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ __('pages.events.calendar.view_calendar') }}
        </a>
    </div>
</section>

<section class="container-tight pb-20">

    @if ($upcoming->isNotEmpty())
        <h2 class="font-display font-bold text-2xl md:text-3xl text-navy-800 mb-6 mt-6">{{ __('pages.events.upcoming') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-16">
            @foreach ($upcoming as $event)
                @include('events._card', ['event' => $event, 'large' => true])
            @endforeach
        </div>
    @endif

    @if ($past->isNotEmpty())
        <h2 class="font-display font-bold text-2xl md:text-3xl text-navy-800 mb-6">{{ __('pages.events.past') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($past as $event)
                @include('events._card', ['event' => $event, 'large' => false])
            @endforeach
        </div>
    @endif

    @if ($upcoming->isEmpty() && $past->isEmpty())
        <div class="max-w-2xl mx-auto text-center bg-cream rounded-xl p-12 border border-graphite/10">
            <p class="font-display text-xl text-navy-800 font-bold">{{ __('pages.events.empty') }}</p>
        </div>
    @endif
</section>

@endsection
