@extends('layouts.app')

@section('title', $project->name . ' — GEMDTEK')
@section('meta_description', $project->summary)
@section('og_type', 'article')
@if ($project->og_image_url)
    @section('og_image', url($project->og_image_url))
@endif

@section('content')

{{-- HERO --}}
<section class="relative bg-navy-900 text-cream">
    @if ($project->hero_url)
        <div class="absolute inset-0">
            <img src="{{ $project->hero_web_url }}" alt="" fetchpriority="high" decoding="async" width="1280" height="720" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-900/70 to-transparent"></div>
        </div>
    @endif
    <div class="relative container-tight py-24 md:py-36">
        <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 text-brass-300 hover:text-brass-200 text-sm mb-6 font-mono">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('pages.projects.all_projects') }}
        </a>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            @if ($project->year)
                <span class="font-mono text-xs text-brass-300 px-3 py-1 border border-brass-300/30 rounded-full">{{ $project->year }}</span>
            @endif
            <span @class([
                'font-mono text-xs px-3 py-1 rounded-full',
                'bg-emerald-500/20 text-emerald-300 border border-emerald-300/30' => $project->status === 'active',
                'bg-cream/10 text-cream/70 border border-cream/20'                => $project->status === 'completed',
                'bg-brass-400/20 text-brass-200 border border-brass-300/30'       => $project->status === 'upcoming',
            ])>{{ $project->status_label }}</span>
        </div>
        <h1 class="text-3xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ $project->name }}
        </h1>
        <p class="text-lg md:text-xl text-cream/80 max-w-3xl">{{ $project->summary }}</p>
    </div>
</section>

{{-- PROBLEM STATEMENT --}}
@if ($project->problem_statement)
    <section class="bg-petrol text-cream">
        <div class="container-tight py-12">
            <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-3">{{ __('pages.projects.problem_eyebrow') }}</p>
            <p class="text-xl md:text-2xl font-display leading-snug max-w-4xl">
                {{ $project->problem_statement }}
            </p>
        </div>
    </section>
@endif

{{-- DESCRIPTION + SPECS --}}
<section class="container-tight py-20 grid lg:grid-cols-3 gap-12">

    @if ($project->description)
        <div class="lg:col-span-2 prose prose-lg max-w-none prose-headings:font-display prose-headings:text-navy-800 prose-a:text-petrol">
            {!! $project->description !!}
        </div>
    @endif

    @if ($specsByCategory->isNotEmpty())
        <aside class="{{ $project->description ? 'lg:col-span-1' : 'lg:col-span-3' }}">
            <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">{{ __('pages.projects.specs_eyebrow') }}</p>
            <h2 class="text-2xl font-display font-bold text-navy-800 mb-6">{{ __('pages.projects.specs_title') }}</h2>

            <div class="space-y-6">
                @foreach ($specsByCategory as $category => $specs)
                    <div class="bg-white rounded-lg border border-graphite/10 overflow-hidden">
                        <div class="bg-navy-50 px-4 py-2 border-b border-graphite/10">
                            <p class="font-mono text-xs uppercase tracking-wider text-navy-700 font-semibold">
                                {{ __('models.project.spec_categories.' . $category) }}
                            </p>
                        </div>
                        <dl class="divide-y divide-graphite/5">
                            @foreach ($specs as $spec)
                                <div class="grid grid-cols-5 gap-2 px-4 py-3 text-sm">
                                    <dt class="col-span-2 text-graphite/60">{{ $spec->key }}</dt>
                                    <dd class="col-span-3 font-mono text-graphite font-medium">{{ $spec->value }}</dd>
                                </div>
                            @endforeach
                        </dl>
                    </div>
                @endforeach
            </div>
        </aside>
    @endif
</section>

{{-- TAKIM --}}
@if ($captain || $crew->isNotEmpty())
    <section class="bg-cream py-20">
        <div class="container-tight">
            <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3 text-center">{{ __('pages.projects.team_eyebrow') }}</p>
            <h2 class="text-3xl md:text-4xl font-display font-bold text-navy-800 mb-12 text-center">
                {{ __('pages.projects.team_title') }}
            </h2>

            @if ($captain)
                <div class="max-w-md mx-auto mb-12 bg-white rounded-xl shadow-md p-6 text-center border-2 border-brass-400">
                    <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-2">{{ __('pages.projects.captain') }}</p>
                    <h3 class="font-display font-bold text-2xl text-navy-800">{{ $captain->name }}</h3>
                    <p class="text-graphite/70 mt-1">{{ $captain->role }}</p>
                    @if ($captain->linkedin_url)
                        <a href="{{ $captain->linkedin_url }}" target="_blank" rel="noopener" class="inline-flex items-center gap-2 mt-4 text-sm font-medium text-petrol hover:text-navy-800">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            LinkedIn
                        </a>
                    @endif
                </div>
            @endif

            @if ($crew->isNotEmpty())
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 max-w-4xl mx-auto">
                    @foreach ($crew as $member)
                        <div class="bg-white rounded-lg p-5 shadow-sm border border-graphite/5 text-center">
                            <h3 class="font-display font-bold text-navy-800">{{ $member->name }}</h3>
                            <p class="text-brass-600 text-sm mt-1">{{ $member->role }}</p>
                            @if ($member->linkedin_url)
                                <a href="{{ $member->linkedin_url }}" target="_blank" rel="noopener" class="inline-block mt-3 text-petrol hover:text-navy-800" aria-label="LinkedIn">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                </a>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
@endif

{{-- GALERİ + DOCS --}}
@php
    $gallery = $project->getMedia('gallery');
    $documents = $project->getMedia('documents');
@endphp

@if ($gallery->isNotEmpty())
    <section class="container-tight py-20">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">{{ __('pages.projects.gallery_eyebrow') }}</p>
        <h2 class="text-3xl md:text-4xl font-display font-bold text-navy-800 mb-8">{{ __('pages.projects.gallery_title') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($gallery as $image)
                <a href="{{ $image->getUrl() }}" target="_blank" rel="noopener" class="block aspect-square rounded-lg overflow-hidden bg-navy-50 hover:shadow-lg transition-shadow">
                    <img src="{{ $image->getUrl('thumb') ?: $image->getUrl() }}" alt="" loading="lazy" decoding="async" width="400" height="400" class="w-full h-full object-cover hover:scale-105 transition-transform">
                </a>
            @endforeach
        </div>
    </section>
@endif

@if ($documents->isNotEmpty())
    <section class="bg-cream py-20">
        <div class="container-tight">
            <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">{{ __('pages.projects.docs_eyebrow') }}</p>
            <h2 class="text-3xl md:text-4xl font-display font-bold text-navy-800 mb-8">{{ __('pages.projects.docs_title') }}</h2>
            <ul class="space-y-3 max-w-2xl">
                @foreach ($documents as $doc)
                    <li>
                        <a href="{{ $doc->getUrl() }}" target="_blank" rel="noopener"
                           class="flex items-center gap-4 bg-white p-4 rounded-lg shadow-sm border border-graphite/5 hover:border-brass-300 transition-colors">
                            <div class="w-10 h-10 rounded bg-brass-50 flex items-center justify-center text-brass-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-display font-semibold text-navy-800">{{ $doc->name }}</p>
                                <p class="font-mono text-xs text-graphite/60">{{ round($doc->size / 1024, 1) }} KB · PDF</p>
                            </div>
                            <svg class="w-5 h-5 text-graphite/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>
@endif

@endsection
