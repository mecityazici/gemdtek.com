@extends('layouts.app')

@section('title', __('pages.forms.eyebrow') . ' — GEMDTEK')

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.forms.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ __('pages.forms.headline_lead') }} <span class="text-brass-400">{{ __('pages.forms.headline_accent') }}</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">
            {{ __('pages.forms.subline') }}
        </p>
    </div>
</section>

<section class="container-tight py-20">
    @if ($forms->isEmpty())
        <div class="max-w-2xl mx-auto text-center bg-cream rounded-xl p-12 border border-graphite/10">
            <p class="font-display text-2xl text-navy-800 font-bold mb-3">{{ __('pages.forms.empty_title') }}</p>
            <p class="text-graphite/70">{{ __('pages.forms.empty_body') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($forms as $form)
                <a href="{{ route('forms.show', $form) }}"
                   class="group bg-white rounded-xl p-6 shadow-sm border border-graphite/5 hover:shadow-lg hover:border-brass-300 transition-all">
                    <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-2">{{ __('pages.forms.open_badge') }}</p>
                    <h2 class="font-display font-bold text-2xl text-navy-800 mb-3 group-hover:text-petrol transition-colors">
                        {{ $form->title }}
                    </h2>
                    @if ($form->description)
                        <p class="text-graphite/70 leading-relaxed text-sm">{{ $form->description }}</p>
                    @endif
                    <div class="mt-4 flex items-center justify-between text-sm">
                        @if ($form->ends_at)
                            <span class="font-mono text-xs text-graphite/60">
                                {{ __('pages.forms.deadline') }}: {{ $form->ends_at->format('d M Y H:i') }}
                            </span>
                        @else
                            <span></span>
                        @endif
                        <span class="font-medium text-brass-600 inline-flex items-center gap-1">
                            {{ __('pages.forms.apply') }}
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

@endsection
