@extends('layouts.app')

@section('title', __('pages.sponsor.eyebrow') . ' — '.setting('site.name', 'GEMDTEK'))
@section('meta_description', __('pages.sponsor.subline'))

@php
    $inputClass = 'block w-full rounded-md border-graphite/20 shadow-sm focus:border-petrol focus:ring-petrol/30 font-sans';
    $tiers = [
        'platinum' => 'navy-800',
        'gold'     => 'brass-500',
        'silver'   => 'graphite',
        'bronze'   => 'petrol',
    ];
@endphp

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.sponsor.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ __('pages.sponsor.headline_lead') }} <span class="text-brass-400">{{ __('pages.sponsor.headline_accent') }}</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">{{ __('pages.sponsor.subline') }}</p>
    </div>
</section>

{{-- KIT DOWNLOAD --}}
<section class="bg-brass-500 text-white">
    <div class="container-tight py-12 md:flex md:items-center md:justify-between gap-8">
        <div class="mb-6 md:mb-0">
            <p class="font-mono text-xs uppercase tracking-widest text-white/70 mb-2">{{ __('pages.sponsor.kit_eyebrow') }}</p>
            <h2 class="font-display text-2xl md:text-3xl font-bold mb-2">{{ __('pages.sponsor.kit_title') }}</h2>
            <p class="text-white/90 max-w-2xl">{{ __('pages.sponsor.kit_subline') }}</p>
        </div>
        <a href="{{ $kitUrl }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-3 bg-white text-brass-700 hover:bg-white/90 font-medium px-6 py-3 rounded-md shrink-0 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
            {{ __('pages.sponsor.kit_button') }}
        </a>
    </div>
</section>

{{-- TIERS --}}
<section class="container-tight py-20">
    <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3 text-center">{{ __('pages.sponsor.tiers.eyebrow') }}</p>
    <h2 class="text-3xl md:text-4xl font-display font-bold text-navy-800 mb-12 text-center">{{ __('pages.sponsor.tiers.title') }}</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach (['platinum', 'gold', 'silver', 'bronze'] as $tier)
            <div class="bg-white rounded-xl border border-graphite/10 p-6 shadow-sm hover:shadow-lg transition-shadow flex flex-col">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-display text-xl font-bold text-navy-800">{{ __('pages.sponsor.tiers.' . $tier . '.name') }}</h3>
                    <span @class([
                        'w-3 h-3 rounded-full',
                        'bg-navy-800'   => $tier === 'platinum',
                        'bg-brass-500'  => $tier === 'gold',
                        'bg-graphite'   => $tier === 'silver',
                        'bg-petrol'     => $tier === 'bronze',
                    ])></span>
                </div>
                <p class="font-mono text-2xl font-bold text-brass-600 mb-4">{{ __('pages.sponsor.tiers.' . $tier . '.price') }}</p>
                <p class="text-graphite/80 text-sm leading-relaxed flex-1">
                    {{ __('pages.sponsor.tiers.' . $tier . '.perks') }}
                </p>
            </div>
        @endforeach
    </div>
</section>

{{-- LEAD FORM --}}
<section class="bg-cream py-20">
    <div class="container-tight max-w-3xl">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-3">{{ __('pages.sponsor.form.eyebrow') }}</p>
        <h2 class="text-3xl md:text-4xl font-display font-bold text-navy-800 mb-3">{{ __('pages.sponsor.form.title') }}</h2>
        <p class="text-graphite/70 mb-8">{{ __('pages.sponsor.form.subline') }}</p>

        @if (session('sponsor_lead_sent'))
            <div class="bg-emerald-50 border-l-4 border-emerald-500 p-5 rounded-lg mb-8">
                <p class="font-display font-bold text-emerald-900 mb-1">{{ __('pages.sponsor.form.sent_title') }}</p>
                <p class="text-emerald-900/80 text-sm">{{ __('pages.sponsor.form.sent_body') }}</p>
            </div>
        @endif

        <form action="{{ route('sponsor.submit') }}" method="POST" class="space-y-5 bg-white p-8 rounded-xl shadow-sm border border-graphite/5">
            @csrf

            <div class="hidden" aria-hidden="true">
                <label>Web site (boş bırak): <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="s-company" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.sponsor.form.company') }} <span class="text-brass-600">*</span></label>
                    <input type="text" id="s-company" name="company_name" value="{{ old('company_name') }}" required maxlength="160" class="{{ $inputClass }}">
                    @error('company_name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="s-tier" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.sponsor.form.tier') }}</label>
                    <select id="s-tier" name="interest_tier" class="{{ $inputClass }}">
                        <option value="">{{ __('pages.sponsor.form.tier_default') }}</option>
                        @foreach ($tiers as $key => $label)
                            <option value="{{ $key }}" @selected(old('interest_tier') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('interest_tier') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="s-name" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.sponsor.form.name') }} <span class="text-brass-600">*</span></label>
                    <input type="text" id="s-name" name="contact_name" value="{{ old('contact_name') }}" required maxlength="120" class="{{ $inputClass }}">
                    @error('contact_name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="s-role" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.sponsor.form.role') }}</label>
                    <input type="text" id="s-role" name="contact_role" value="{{ old('contact_role') }}" maxlength="120" class="{{ $inputClass }}">
                </div>
            </div>

            <div>
                <label for="s-email" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.sponsor.form.email') }} <span class="text-brass-600">*</span></label>
                <input type="email" id="s-email" name="contact_email" value="{{ old('contact_email') }}" required maxlength="160" class="{{ $inputClass }}">
                @error('contact_email') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="s-message" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.sponsor.form.message') }}</label>
                <textarea id="s-message" name="message" rows="4" maxlength="2000" class="{{ $inputClass }}">{{ old('message') }}</textarea>
            </div>

            <button type="submit" class="btn-accent w-full md:w-auto">
                {{ __('pages.sponsor.form.submit') }}
            </button>
        </form>
    </div>
</section>

@endsection
