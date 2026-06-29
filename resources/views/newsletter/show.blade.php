@extends('layouts.app')

@section('title', __('pages.newsletter.eyebrow') . ' — '.setting('site.name', 'GEMDTEK'))
@section('meta_description', __('pages.newsletter.subline'))

@php
    $inputClass = 'block w-full rounded-md border-graphite/20 shadow-sm focus:border-petrol focus:ring-petrol/30 font-sans';
@endphp

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.newsletter.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ __('pages.newsletter.headline_lead') }} <span class="text-brass-400">{{ __('pages.newsletter.headline_accent') }}</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">{{ __('pages.newsletter.subline') }}</p>
    </div>
</section>

<section class="container-tight py-16 grid md:grid-cols-2 gap-12 items-start">

    <div class="space-y-6">
        <h2 class="font-display text-2xl font-bold text-navy-800">{{ __('pages.newsletter.benefits_title') }}</h2>
        <ul class="space-y-4 text-graphite/85 leading-relaxed">
            <li class="flex gap-3">
                <span class="text-brass-500 font-mono mt-1">01</span>
                <span>{{ __('pages.newsletter.benefits.events') }}</span>
            </li>
            <li class="flex gap-3">
                <span class="text-brass-500 font-mono mt-1">02</span>
                <span>{{ __('pages.newsletter.benefits.projects') }}</span>
            </li>
            <li class="flex gap-3">
                <span class="text-brass-500 font-mono mt-1">03</span>
                <span>{{ __('pages.newsletter.benefits.industry') }}</span>
            </li>
            <li class="flex gap-3">
                <span class="text-brass-500 font-mono mt-1">04</span>
                <span>{{ __('pages.newsletter.benefits.alumni') }}</span>
            </li>
        </ul>

        <p class="text-sm text-graphite/60 italic">{{ __('pages.newsletter.frequency_note') }}</p>
    </div>

    <div>
        <div class="bg-white rounded-xl p-8 shadow-sm border border-graphite/5">
            <h2 class="font-display text-2xl font-bold text-navy-800 mb-6">{{ __('pages.newsletter.form.title') }}</h2>

            @if (session('newsletter_status') === 'pending')
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-5 rounded-lg mb-6">
                    <p class="font-display font-bold text-emerald-900 mb-1">{{ __('pages.newsletter.form.pending_title') }}</p>
                    <p class="text-emerald-900/80 text-sm">{{ __('pages.newsletter.form.pending_body') }}</p>
                </div>
            @endif

            @if (session('newsletter_status') === 'already_confirmed')
                <div class="bg-amber-50 border-l-4 border-amber-500 p-5 rounded-lg mb-6">
                    <p class="font-display font-bold text-amber-900 mb-1">{{ __('pages.newsletter.form.already_title') }}</p>
                    <p class="text-amber-900/80 text-sm">{{ __('pages.newsletter.form.already_body') }}</p>
                </div>
            @endif

            <form action="{{ route('newsletter.subscribe') }}" method="POST" class="space-y-5">
                @csrf
                <input type="hidden" name="source" value="bulten-page">

                <div class="hidden" aria-hidden="true">
                    <label>Web site (boş bırak): <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div>
                    <label for="n-name" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.newsletter.form.name') }}</label>
                    <input type="text" id="n-name" name="name" value="{{ old('name') }}" maxlength="120" class="{{ $inputClass }}">
                    @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="n-email" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.newsletter.form.email') }} <span class="text-brass-600">*</span></label>
                    <input type="email" id="n-email" name="email" value="{{ old('email') }}" required maxlength="160" class="{{ $inputClass }}">
                    @error('email') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <p class="text-xs text-graphite/60">
                    {!! __('pages.newsletter.form.consent', ['url' => route('legal.privacy')]) !!}
                </p>

                <button type="submit" class="btn-accent w-full md:w-auto">
                    {{ __('pages.newsletter.form.submit') }}
                </button>
            </form>
        </div>
    </div>
</section>

@endsection
