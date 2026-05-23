@extends('layouts.app')

@section('title', __('pages.search.heading') . ' — GEMDTEK')
@section('no_index', true)

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-16 md:py-20">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.search.eyebrow') }}</p>
        <h1 class="text-3xl md:text-4xl font-display font-bold mb-6">{{ __('pages.search.heading') }}</h1>

        <form action="{{ route('search') }}" method="GET" class="max-w-2xl">
            <div class="relative">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-cream/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="search"
                       name="q"
                       value="{{ $q }}"
                       autofocus
                       placeholder="{{ __('pages.search.placeholder') }}"
                       class="w-full pl-12 pr-32 py-4 rounded-lg bg-navy-950 border border-cream/10 text-cream placeholder:text-cream/40 focus:border-brass-400 focus:ring-brass-400/20 focus:outline-none">
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-brass-500 hover:bg-brass-600 text-white px-5 py-2 rounded-md font-medium transition-colors">
                    {{ __('pages.search.submit') }}
                </button>
            </div>
        </form>
    </div>
</section>

<section class="container-tight py-12">

    @if ($tooShort)
        <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-lg max-w-2xl mb-8">
            <p class="text-amber-900 text-sm">{{ __('pages.search.too_short', ['n' => $minLength]) }}</p>
        </div>
    @elseif ($q === '')
        <p class="text-graphite/60 italic">{{ __('pages.search.hint') }}</p>
    @elseif ($total === 0)
        <div class="bg-cream rounded-xl p-12 max-w-2xl border border-graphite/10">
            <p class="font-display text-2xl text-navy-800 font-bold mb-2">{{ __('pages.search.no_results', ['q' => '"' . $q . '"']) }}</p>
            <p class="text-graphite/70 text-sm">{{ __('pages.search.no_results_hint') }}</p>
        </div>
    @else
        <p class="text-sm text-graphite/60 font-mono mb-10">
            {{ __('pages.search.results_count', ['n' => $total, 'q' => '"' . $q . '"']) }}
        </p>

        {{-- PROJECTS --}}
        @if ($results['projects']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-navy-800">{{ __('pages.search.groups.projects') }} ({{ $results['projects']->count() }})</h2>
                    <a href="{{ route('projects.index') }}" class="text-sm text-brass-600 hover:text-brass-700">{{ __('pages.search.see_all') }} →</a>
                </div>
                <ul class="space-y-2">
                    @foreach ($results['projects'] as $p)
                        <li>
                            <a href="{{ route('projects.show', $p) }}" class="block bg-white p-4 rounded-lg border border-graphite/10 hover:border-brass-300 transition-colors">
                                <p class="font-display font-semibold text-navy-800">{{ $p->name }}</p>
                                <p class="text-sm text-graphite/70 mt-1 line-clamp-2">{{ $p->summary }}</p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- NEWS --}}
        @if ($results['news']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-navy-800">{{ __('pages.search.groups.news') }} ({{ $results['news']->count() }})</h2>
                    <a href="{{ route('news.index') }}" class="text-sm text-brass-600 hover:text-brass-700">{{ __('pages.search.see_all') }} →</a>
                </div>
                <ul class="space-y-2">
                    @foreach ($results['news'] as $n)
                        <li>
                            <a href="{{ route('news.show', $n) }}" class="block bg-white p-4 rounded-lg border border-graphite/10 hover:border-brass-300 transition-colors">
                                <p class="font-display font-semibold text-navy-800">{{ $n->title }}</p>
                                @if ($n->excerpt)
                                    <p class="text-sm text-graphite/70 mt-1 line-clamp-2">{{ $n->excerpt }}</p>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- EVENTS --}}
        @if ($results['events']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-navy-800">{{ __('pages.search.groups.events') }} ({{ $results['events']->count() }})</h2>
                    <a href="{{ route('events.index') }}" class="text-sm text-brass-600 hover:text-brass-700">{{ __('pages.search.see_all') }} →</a>
                </div>
                <ul class="space-y-2">
                    @foreach ($results['events'] as $e)
                        <li>
                            <a href="{{ route('events.show', $e) }}" class="block bg-white p-4 rounded-lg border border-graphite/10 hover:border-brass-300 transition-colors">
                                <p class="font-display font-semibold text-navy-800">{{ $e->title }}</p>
                                <p class="text-xs font-mono text-brass-600 mt-1">
                                    {{ $e->event_date->isoFormat('D MMM YYYY') }}@if ($e->location) · {{ $e->location }} @endif
                                </p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ALUMNI --}}
        @if ($results['alumni']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-navy-800">{{ __('pages.search.groups.alumni') }} ({{ $results['alumni']->count() }})</h2>
                    <a href="{{ route('alumni.index') }}" class="text-sm text-brass-600 hover:text-brass-700">{{ __('pages.search.see_all') }} →</a>
                </div>
                <ul class="space-y-2">
                    @foreach ($results['alumni'] as $a)
                        <li class="block bg-white p-4 rounded-lg border border-graphite/10">
                            <p class="font-display font-semibold text-navy-800">{{ $a->name }}</p>
                            <p class="text-sm text-graphite/70 mt-1">
                                {{ $a->position }}@if ($a->company) — {{ $a->company }} @endif
                                @if ($a->graduation_year) <span class="text-xs font-mono text-graphite/50">· {{ $a->graduation_year }}</span> @endif
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

</section>

@endsection
