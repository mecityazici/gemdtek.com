@extends('layouts.app')

@section('title', $event->title . ' — GEMDTEK')
@section('meta_description', $event->summary)
@section('og_type', 'event')
@if ($event->cover_url)
    @section('og_image', $event->cover_url)
@endif

@section('content')

<section class="relative bg-navy-900 text-cream">
    @if ($event->cover_url)
        <div class="absolute inset-0">
            <img src="{{ $event->cover_url }}" alt="" class="w-full h-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-t from-navy-950 via-navy-900/70 to-transparent"></div>
        </div>
    @endif
    <div class="relative container-tight py-20 md:py-28">
        <a href="{{ route('events.index') }}" class="inline-flex items-center gap-2 text-brass-300 hover:text-brass-200 text-sm mb-6 font-mono">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('pages.events.all_events') }}
        </a>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="bg-navy-700/60 text-cream px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider">
                {{ $event->category_label }}
            </span>
            @if ($event->is_upcoming)
                <span class="bg-brass-500/30 text-brass-200 border border-brass-400/40 px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider">
                    Yaklaşıyor
                </span>
            @endif
        </div>
        <h1 class="text-3xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ $event->title }}
        </h1>
        @if ($event->summary)
            <p class="text-lg md:text-xl text-cream/80 max-w-3xl">{{ $event->summary }}</p>
        @endif
    </div>
</section>

<section class="bg-petrol text-cream">
    <div class="container-tight py-10 grid md:grid-cols-3 gap-6">
        <div>
            <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-2">{{ __('pages.events.date_label') }}</p>
            <p class="font-display text-xl font-bold">{{ $event->event_date->isoFormat('D MMMM YYYY') }}</p>
            <p class="text-cream/70 text-sm">{{ $event->event_date->isoFormat('HH:mm') }}</p>
        </div>
        @if ($event->location)
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-2">{{ __('pages.events.location_label') }}</p>
                <p class="font-display text-xl font-bold">{{ $event->location }}</p>
            </div>
        @endif
        <div class="flex flex-wrap items-center gap-3 md:justify-end">
            @if ($event->registration_enabled)
                <a href="#rsvp" class="btn-accent">
                    {{ __('pages.events.rsvp.cta') }}
                </a>
            @elseif ($event->registration_url)
                <a href="{{ $event->registration_url }}" target="_blank" rel="noopener" class="btn-accent">
                    {{ __('site.cta.register') }}
                </a>
            @endif
            @if ($event->is_upcoming)
                <a href="{{ route('events.ics', $event) }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-md border border-cream/30 text-cream hover:bg-cream/10 transition-colors text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    {{ __('pages.events.add_to_calendar') }}
                </a>
            @endif
        </div>
    </div>
</section>

@if ($event->description)
    <section class="container-tight py-20 max-w-3xl">
        <div class="prose prose-lg max-w-none prose-headings:font-display prose-headings:text-navy-800 prose-a:text-petrol">
            {!! $event->description !!}
        </div>
    </section>
@endif

@if ($event->registration_enabled)
    @php
        $inputClass = 'block w-full rounded-md border-graphite/20 shadow-sm focus:border-petrol focus:ring-petrol/30 font-sans';
        $remaining = $event->remainingSeats();
        $isOpen = $event->isRegistrationOpen();
        $isFull = $event->isFull();
    @endphp
    <section id="rsvp" class="bg-cream/60 border-y border-graphite/10 py-16">
        <div class="container-tight grid md:grid-cols-[2fr_3fr] gap-10">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">{{ __('pages.events.rsvp.eyebrow') }}</p>
                <h2 class="font-display text-3xl font-bold text-navy-800 mb-4">{{ __('pages.events.rsvp.title') }}</h2>
                <p class="text-graphite/80 leading-relaxed mb-6">{{ __('pages.events.rsvp.subline') }}</p>

                <dl class="space-y-3 text-sm">
                    @if ($event->capacity)
                        <div class="flex items-baseline justify-between gap-4 pb-3 border-b border-graphite/10">
                            <dt class="text-graphite/60">{{ __('pages.events.rsvp.capacity_label') }}</dt>
                            <dd class="font-display text-lg text-navy-800">{{ $event->capacity }}</dd>
                        </div>
                        <div class="flex items-baseline justify-between gap-4 pb-3 border-b border-graphite/10">
                            <dt class="text-graphite/60">{{ __('pages.events.rsvp.remaining_label') }}</dt>
                            <dd class="font-display text-lg text-{{ $isFull ? 'red' : 'emerald' }}-700">
                                {{ $isFull ? __('pages.events.rsvp.full') : $remaining }}
                            </dd>
                        </div>
                    @endif
                    @if ($event->registration_deadline)
                        <div class="flex items-baseline justify-between gap-4">
                            <dt class="text-graphite/60">{{ __('pages.events.rsvp.deadline_label') }}</dt>
                            <dd class="text-graphite text-sm">{{ $event->registration_deadline->isoFormat('D MMM YYYY HH:mm') }}</dd>
                        </div>
                    @endif
                </dl>
            </div>

            <div>
                <div class="bg-white rounded-xl p-8 shadow-sm border border-graphite/5">
                    @if (! $isOpen)
                        <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-lg">
                            <p class="font-display font-bold text-amber-900 mb-1">{{ __('pages.events.rsvp.closed_title') }}</p>
                            <p class="text-amber-900/80 text-sm">{{ __('pages.events.rsvp.closed_body') }}</p>
                        </div>
                    @else
                        @if (session('event_registration_status') === 'pending')
                            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-5 rounded-lg mb-6">
                                <p class="font-display font-bold text-emerald-900 mb-1">{{ __('pages.events.rsvp.pending_title') }}</p>
                                <p class="text-emerald-900/80 text-sm">{{ __('pages.events.rsvp.pending_body') }}</p>
                            </div>
                        @endif
                        @if (session('event_registration_status') === 'waitlist')
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-5 rounded-lg mb-6">
                                <p class="font-display font-bold text-blue-900 mb-1">{{ __('pages.events.rsvp.waitlist_title') }}</p>
                                <p class="text-blue-900/80 text-sm">{{ __('pages.events.rsvp.waitlist_body') }}</p>
                            </div>
                        @endif
                        @if (session('event_registration_status') === 'already_confirmed')
                            <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-lg mb-6">
                                <p class="font-display font-bold text-amber-900 mb-1">{{ __('pages.events.rsvp.already_title') }}</p>
                                <p class="text-amber-900/80 text-sm">{{ __('pages.events.rsvp.already_body') }}</p>
                            </div>
                        @endif

                        <form action="{{ route('events.registrations.store', $event) }}" method="POST" class="space-y-5">
                            @csrf
                            <div class="hidden" aria-hidden="true">
                                <label>Web site (boş bırak): <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                            </div>

                            <div class="grid md:grid-cols-2 gap-5">
                                <div>
                                    <label for="r-name" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.events.rsvp.fields.name') }} <span class="text-brass-600">*</span></label>
                                    <input type="text" id="r-name" name="name" value="{{ old('name') }}" required maxlength="120" class="{{ $inputClass }}">
                                    @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label for="r-email" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.events.rsvp.fields.email') }} <span class="text-brass-600">*</span></label>
                                    <input type="email" id="r-email" name="email" value="{{ old('email') }}" required maxlength="160" class="{{ $inputClass }}">
                                    @error('email') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                                </div>
                            </div>

                            <div class="grid md:grid-cols-2 gap-5">
                                <div>
                                    <label for="r-phone" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.events.rsvp.fields.phone') }}</label>
                                    <input type="tel" id="r-phone" name="phone" value="{{ old('phone') }}" maxlength="40" class="{{ $inputClass }}">
                                </div>
                                <div>
                                    <label for="r-affiliation" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.events.rsvp.fields.affiliation') }}</label>
                                    <select id="r-affiliation" name="affiliation" class="{{ $inputClass }}">
                                        <option value="">—</option>
                                        @foreach (\App\Models\EventRegistration::AFFILIATIONS as $value => $label)
                                            <option value="{{ $value }}" @selected(old('affiliation') === $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="r-notes" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.events.rsvp.fields.notes') }}</label>
                                <textarea id="r-notes" name="notes" rows="3" maxlength="1000" class="{{ $inputClass }}">{{ old('notes') }}</textarea>
                            </div>

                            <p class="text-xs text-graphite/60">
                                {!! __('pages.events.rsvp.consent', ['url' => route('legal.privacy')]) !!}
                            </p>

                            <button type="submit" class="btn-accent w-full md:w-auto">
                                {{ $isFull ? __('pages.events.rsvp.waitlist_submit') : __('pages.events.rsvp.submit') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif

@endsection
