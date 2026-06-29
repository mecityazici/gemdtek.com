@extends('layouts.app')

@section('title', __('pages.errors.500.title') . ' — '.setting('site.name', 'GEMDTEK'))
@section('no_index', true)

@section('content')
<section class="bg-navy-900 text-cream">
    <div class="container-tight py-32 md:py-40 text-center">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">500</p>
        <h1 class="text-5xl md:text-7xl font-display font-bold mb-6 text-brass-400">
            {{ __('pages.errors.500.lead') }}
        </h1>
        <p class="text-lg text-cream/80 max-w-xl mx-auto mb-10">
            {{ __('pages.errors.500.body') }}
        </p>
        <a href="{{ route('home') }}" class="btn-accent">{{ __('pages.errors.500.cta') }}</a>
    </div>
</section>
@endsection
