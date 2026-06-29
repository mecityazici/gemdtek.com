@extends('layouts.app')

@section('title', __('pages.privacy.headline') . ' — '.setting('site.name', 'GEMDTEK'))

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-16 md:py-20">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.privacy.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-4">{{ __('pages.privacy.headline') }}</h1>
        <p class="text-sm text-cream/60 font-mono">{{ __('pages.privacy.updated') }}: 2026-05-24</p>
    </div>
</section>

<article class="container-tight py-16 max-w-3xl prose prose-lg prose-headings:font-display prose-headings:text-navy-800">

    <p class="lead">{{ __('pages.privacy.intro') }}</p>

    @foreach (['controller', 'data', 'purpose', 'transfer', 'retention', 'rights', 'cookies'] as $section)
        <h2>{{ __('pages.privacy.sections.' . $section . '.heading') }}</h2>
        <p>{{ __('pages.privacy.sections.' . $section . '.body') }}</p>
    @endforeach

    <hr class="my-12">

    <p class="text-sm text-graphite/60 italic">{{ __('pages.privacy.disclaimer') }}</p>
</article>

@endsection
