@extends('layouts.app')

@section('title', __('pages.newsletter.feedback.'.$state.'.title') . ' — '.setting('site.name', 'GEMDTEK'))
@section('meta_description', __('pages.newsletter.feedback.'.$state.'.body'))

@section('content')

<section class="container-tight py-24 max-w-2xl">
    @php
        $tone = match($state) {
            'confirmed' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-500', 'text' => 'text-emerald-900'],
            'unsubscribed' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-500', 'text' => 'text-amber-900'],
            default => ['bg' => 'bg-red-50', 'border' => 'border-red-500', 'text' => 'text-red-900'],
        };
    @endphp

    <div class="{{ $tone['bg'] }} border-l-4 {{ $tone['border'] }} p-8 rounded-xl">
        <h1 class="font-display text-2xl md:text-3xl font-bold {{ $tone['text'] }} mb-3">
            {{ __('pages.newsletter.feedback.'.$state.'.title') }}
        </h1>
        <p class="{{ $tone['text'] }}/85 leading-relaxed">
            {{ __('pages.newsletter.feedback.'.$state.'.body') }}
        </p>
    </div>

    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('home') }}" class="btn-accent">{{ __('site.cta.home') }}</a>
        @if ($state === 'unsubscribed')
            <a href="{{ route('newsletter.show') }}" class="btn-ghost">{{ __('pages.newsletter.feedback.resubscribe') }}</a>
        @endif
    </div>
</section>

@endsection
