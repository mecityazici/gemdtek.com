@extends('layouts.app')

@section('title', __('pages.events.rsvp.feedback.'.$state.'.title') . ' — GEMDTEK')
@section('meta_description', __('pages.events.rsvp.feedback.'.$state.'.body'))

@section('content')

<section class="container-tight py-24 max-w-2xl">
    @php
        $tone = match($state) {
            'confirmed' => ['bg' => 'bg-emerald-50', 'border' => 'border-emerald-500', 'text' => 'text-emerald-900'],
            'cancelled' => ['bg' => 'bg-amber-50', 'border' => 'border-amber-500', 'text' => 'text-amber-900'],
            default => ['bg' => 'bg-red-50', 'border' => 'border-red-500', 'text' => 'text-red-900'],
        };
    @endphp

    <div class="{{ $tone['bg'] }} border-l-4 {{ $tone['border'] }} p-8 rounded-xl">
        <h1 class="font-display text-2xl md:text-3xl font-bold {{ $tone['text'] }} mb-3">
            {{ __('pages.events.rsvp.feedback.'.$state.'.title') }}
        </h1>
        <p class="{{ $tone['text'] }}/85 leading-relaxed">
            {{ __('pages.events.rsvp.feedback.'.$state.'.body') }}
        </p>

        @if (isset($registration) && $state === 'confirmed' && $registration->event)
            <div class="mt-5 space-y-1 {{ $tone['text'] }}/85 text-sm">
                <p><strong>{{ $registration->event->getTranslation('title', app()->getLocale()) }}</strong></p>
                @if ($registration->event->event_date)
                    <p>{{ $registration->event->event_date->isoFormat('D MMM YYYY HH:mm') }}</p>
                @endif
                @if ($registration->event->location)
                    <p>{{ $registration->event->location }}</p>
                @endif
            </div>
        @endif
    </div>

    <div class="mt-10 flex flex-wrap gap-3">
        <a href="{{ route('events.index') }}" class="btn-accent">{{ __('pages.events.all_events') }}</a>
        @if (isset($registration) && $registration->event)
            <a href="{{ route('events.show', $registration->event) }}" class="btn-ghost">{{ __('site.cta.details') }}</a>
            @if ($state === 'confirmed')
                <a href="{{ route('events.ics', $registration->event) }}" class="btn-ghost">{{ __('pages.events.add_to_calendar') }}</a>
            @endif
        @endif
    </div>
</section>

@endsection
