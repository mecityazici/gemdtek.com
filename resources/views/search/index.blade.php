@extends('layouts.app')

@section('title', __('pages.search.heading') . ' — GEMDTEK')
@section('no_index', true)

@php
    use App\Support\SearchHelper;

    $typeLabels = [
        '' => __('pages.search.types.all'),
        'projects' => __('pages.search.groups.projects'),
        'news' => __('pages.search.groups.news'),
        'events' => __('pages.search.groups.events'),
        'alumni' => __('pages.search.groups.alumni'),
    ];
@endphp

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
            @if ($type)
                <input type="hidden" name="type" value="{{ $type }}">
            @endif
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
        {{-- Type chips --}}
        <div class="flex flex-wrap gap-2 mb-8">
            @foreach ($typeLabels as $key => $label)
                @php
                    $count = $key === '' ? $total : ($totals[$key] ?? 0);
                    $isActive = $type === $key;
                @endphp
                <a href="{{ route('search', array_filter(['q' => $q, 'type' => $key ?: null])) }}"
                   @class([
                       'inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-sm font-medium transition-colors',
                       'bg-navy-800 text-cream' => $isActive,
                       'bg-cream text-navy-800 hover:bg-navy-100' => ! $isActive,
                       'opacity-50 pointer-events-none' => $count === 0 && $key !== '',
                   ])
                   @if ($count === 0 && $key !== '') aria-disabled="true" @endif>
                    {{ $label }}
                    <span class="font-mono text-xs {{ $isActive ? 'text-cream/70' : 'text-graphite/60' }}">{{ $count }}</span>
                </a>
            @endforeach
        </div>

        <p class="text-sm text-graphite/60 font-mono mb-10">
            {{ __('pages.search.results_count', ['n' => $total, 'q' => '"' . $q . '"']) }}
        </p>

        {{-- PROJECTS --}}
        @if (($type === '' || $type === 'projects') && $results['projects']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-navy-800">
                        {{ __('pages.search.groups.projects') }}
                        <span class="font-mono text-sm text-graphite/60">({{ $totals['projects'] }})</span>
                    </h2>
                    @if ($type === '' && $totals['projects'] > $results['projects']->count())
                        <a href="{{ route('search', ['q' => $q, 'type' => 'projects']) }}" class="text-sm text-brass-600 hover:text-brass-700">{{ __('pages.search.see_all_in_type') }} →</a>
                    @endif
                </div>
                <ul class="space-y-2">
                    @foreach ($results['projects'] as $p)
                        <li>
                            <a href="{{ route('projects.show', $p) }}" class="block bg-white p-4 rounded-lg border border-graphite/10 hover:border-brass-300 transition-colors">
                                <p class="font-display font-semibold text-navy-800">{!! SearchHelper::highlight($p->name, $q) !!}</p>
                                <p class="text-sm text-graphite/70 mt-1 line-clamp-3">
                                    {!! SearchHelper::highlight(SearchHelper::excerpt($p->summary ?: $p->description, $q), $q) !!}
                                </p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- NEWS --}}
        @if (($type === '' || $type === 'news') && $results['news']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-navy-800">
                        {{ __('pages.search.groups.news') }}
                        <span class="font-mono text-sm text-graphite/60">({{ $totals['news'] }})</span>
                    </h2>
                    @if ($type === '' && $totals['news'] > $results['news']->count())
                        <a href="{{ route('search', ['q' => $q, 'type' => 'news']) }}" class="text-sm text-brass-600 hover:text-brass-700">{{ __('pages.search.see_all_in_type') }} →</a>
                    @endif
                </div>
                <ul class="space-y-2">
                    @foreach ($results['news'] as $n)
                        <li>
                            <a href="{{ route('news.show', $n) }}" class="block bg-white p-4 rounded-lg border border-graphite/10 hover:border-brass-300 transition-colors">
                                <p class="font-display font-semibold text-navy-800">{!! SearchHelper::highlight($n->title, $q) !!}</p>
                                <p class="text-sm text-graphite/70 mt-1 line-clamp-3">
                                    {!! SearchHelper::highlight(SearchHelper::excerpt($n->excerpt ?: $n->content, $q), $q) !!}
                                </p>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- EVENTS --}}
        @if (($type === '' || $type === 'events') && $results['events']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-navy-800">
                        {{ __('pages.search.groups.events') }}
                        <span class="font-mono text-sm text-graphite/60">({{ $totals['events'] }})</span>
                    </h2>
                    @if ($type === '' && $totals['events'] > $results['events']->count())
                        <a href="{{ route('search', ['q' => $q, 'type' => 'events']) }}" class="text-sm text-brass-600 hover:text-brass-700">{{ __('pages.search.see_all_in_type') }} →</a>
                    @endif
                </div>
                <ul class="space-y-2">
                    @foreach ($results['events'] as $e)
                        <li>
                            <a href="{{ route('events.show', $e) }}" class="block bg-white p-4 rounded-lg border border-graphite/10 hover:border-brass-300 transition-colors">
                                <p class="font-display font-semibold text-navy-800">{!! SearchHelper::highlight($e->title, $q) !!}</p>
                                <p class="text-xs font-mono text-brass-600 mt-1">
                                    {{ $e->event_date->isoFormat('D MMM YYYY') }}@if ($e->location) · {!! SearchHelper::highlight($e->location, $q) !!} @endif
                                </p>
                                @if ($e->summary)
                                    <p class="text-sm text-graphite/70 mt-2 line-clamp-2">
                                        {!! SearchHelper::highlight(SearchHelper::excerpt($e->summary ?: $e->description, $q), $q) !!}
                                    </p>
                                @endif
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ALUMNI --}}
        @if (($type === '' || $type === 'alumni') && $results['alumni']->isNotEmpty())
            <div class="mb-12">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="font-display text-xl font-bold text-navy-800">
                        {{ __('pages.search.groups.alumni') }}
                        <span class="font-mono text-sm text-graphite/60">({{ $totals['alumni'] }})</span>
                    </h2>
                    @if ($type === '' && $totals['alumni'] > $results['alumni']->count())
                        <a href="{{ route('search', ['q' => $q, 'type' => 'alumni']) }}" class="text-sm text-brass-600 hover:text-brass-700">{{ __('pages.search.see_all_in_type') }} →</a>
                    @endif
                </div>
                <ul class="space-y-2">
                    @foreach ($results['alumni'] as $a)
                        <li class="block bg-white p-4 rounded-lg border border-graphite/10">
                            <p class="font-display font-semibold text-navy-800">{!! SearchHelper::highlight($a->name, $q) !!}</p>
                            <p class="text-sm text-graphite/70 mt-1">
                                {!! SearchHelper::highlight($a->position, $q) !!}@if ($a->company) — {!! SearchHelper::highlight($a->company, $q) !!} @endif
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
