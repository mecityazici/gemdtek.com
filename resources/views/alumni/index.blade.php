@extends('layouts.app')

@section('title', __('pages.alumni.eyebrow') . ' — '.setting('site.name', 'GEMDTEK'))
@section('meta_description', __('pages.alumni.subline'))

@php
    $sectors = \App\Models\Alumni::SECTORS;
@endphp

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.alumni.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ __('pages.alumni.headline_lead') }} <span class="text-brass-400">{{ __('pages.alumni.headline_accent') }}</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">{{ __('pages.alumni.subline') }}</p>
    </div>
</section>

<section class="container-tight py-10 space-y-4">
    {{-- Sector chips --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('alumni.index') }}"
           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $activeSec === '' ? 'bg-navy-800 text-cream' : 'bg-cream text-navy-800 hover:bg-navy-100' }}">
            {{ __('site.cta.all') }}
        </a>
        @foreach ($sectors as $key => $label)
            <a href="{{ route('alumni.index', ['sector' => $key]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $activeSec === $key ? 'bg-navy-800 text-cream' : 'bg-cream text-navy-800 hover:bg-navy-100' }}">
                {{ __('models.alumni.sectors.' . $key) }}
            </a>
        @endforeach
    </div>

    {{-- Year filter --}}
    @if ($years->isNotEmpty())
        <form action="{{ route('alumni.index') }}" method="GET" class="flex items-center gap-2 text-sm">
            @if ($activeSec) <input type="hidden" name="sector" value="{{ $activeSec }}"> @endif
            <label for="year-filter" class="text-graphite/60 font-mono uppercase text-xs">{{ __('pages.alumni.year_label') }}:</label>
            <select id="year-filter" name="year" onchange="this.form.submit()"
                    class="rounded-md border-graphite/20 text-sm py-1 px-2 focus:border-petrol focus:ring-petrol/30">
                <option value="">{{ __('pages.alumni.year_all') }}</option>
                @foreach ($years as $y)
                    <option value="{{ $y }}" @selected($activeYear == $y)>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    @endif
</section>

<section class="container-tight pb-20">
    @if ($alumni->isEmpty())
        <div class="max-w-2xl mx-auto text-center bg-cream rounded-xl p-12 border border-graphite/10">
            <p class="font-display text-xl text-navy-800 font-bold">{{ __('pages.alumni.empty') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach ($alumni as $person)
                <article class="bg-white rounded-xl overflow-hidden shadow-sm border border-graphite/5 hover:shadow-md transition-shadow">
                    <div class="aspect-square bg-navy-50 flex items-center justify-center overflow-hidden">
                        @if ($person->photo_url)
                            <img src="{{ $person->photo_thumb_url }}" srcset="{{ $person->photo_thumb_url }} 160w, {{ $person->photo_web_url }} 320w" sizes="(max-width: 768px) 33vw, 200px" alt="{{ $person->name }}" loading="lazy" decoding="async" width="320" height="320" class="w-full h-full object-cover">
                        @else
                            <span class="font-display text-4xl text-navy-300 font-bold">
                                {{ collect(explode(' ', $person->name))->map(fn ($w) => mb_substr($w, 0, 1))->take(2)->implode('') }}
                            </span>
                        @endif
                    </div>
                    <div class="p-4">
                        <h3 class="font-display font-bold text-navy-800">{{ $person->name }}</h3>
                        <p class="text-sm text-brass-600 mt-1">{{ $person->position }}</p>
                        @if ($person->company)
                            <p class="text-xs text-graphite/70 mt-1">{{ $person->company }}</p>
                        @endif
                        <div class="flex items-center justify-between mt-3 pt-3 border-t border-graphite/10 text-xs">
                            <span class="font-mono text-graphite/60">
                                {{ $person->sector_label }}
                                @if ($person->graduation_year) · {{ $person->graduation_year }} @endif
                            </span>
                            @if ($person->linkedin_url)
                                <a href="{{ $person->linkedin_url }}" target="_blank" rel="noopener" class="text-petrol hover:text-navy-800" aria-label="LinkedIn">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $alumni->links() }}
        </div>
    @endif
</section>

@endsection
