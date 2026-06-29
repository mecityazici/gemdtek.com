@extends('layouts.app')

@section('title', __('pages.contact.eyebrow') . ' — GEMDTEK')
@section('meta_description', __('pages.contact.subline'))

@php
    $inputClass = 'block w-full rounded-md border-graphite/20 shadow-sm focus:border-petrol focus:ring-petrol/30 font-sans';

    // Admin → Site Ayarları'ndan okunur; ayar boşsa mevcut (lang/varsayılan) değere düşer.
    $contactEmail = setting('contact.email', 'info@gemdtek.com');
    $contactCampus = setting('contact.campus', __('pages.contact.info.campus_value'));
    $contactResponseNote = setting('contact.response_note', __('pages.contact.info.response_note'));
    $linkedin = setting('social.linkedin', 'https://linkedin.com/company/gemdtek');
    $instagram = setting('social.instagram', 'https://instagram.com/gemdtek');
    $twitter = setting('social.twitter', 'https://x.com/gemdtek');
@endphp

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.contact.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ __('pages.contact.headline_lead') }} <span class="text-brass-400">{{ __('pages.contact.headline_accent') }}</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">{{ __('pages.contact.subline') }}</p>
    </div>
</section>

<section class="container-tight py-16 grid md:grid-cols-2 gap-12">

    {{-- Sol: iletişim bilgileri --}}
    <div>
        <div class="space-y-8">
            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-2">{{ __('pages.contact.info.email') }}</p>
                <a href="mailto:{{ $contactEmail }}" class="font-display text-2xl text-navy-800 hover:text-petrol transition-colors">
                    {{ $contactEmail }}
                </a>
            </div>

            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-2">{{ __('pages.contact.info.social') }}</p>
                <div class="flex items-center gap-3 text-petrol">
                    @if ($linkedin)
                    <a href="{{ $linkedin }}" target="_blank" rel="noopener" class="hover:text-navy-800" aria-label="LinkedIn">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                    </a>
                    @endif
                    @if ($instagram)
                    <a href="{{ $instagram }}" target="_blank" rel="noopener" class="hover:text-navy-800" aria-label="Instagram">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                    </a>
                    @endif
                    @if ($twitter)
                    <a href="{{ $twitter }}" target="_blank" rel="noopener" class="hover:text-navy-800" aria-label="X">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    </a>
                    @endif
                </div>
            </div>

            <div>
                <p class="font-mono text-xs uppercase tracking-widest text-brass-600 mb-2">{{ __('pages.contact.info.campus') }}</p>
                <p class="text-graphite/80 leading-relaxed">{{ $contactCampus }}</p>
            </div>

            <p class="text-sm text-graphite/60 italic">{{ $contactResponseNote }}</p>
        </div>
    </div>

    {{-- Sağ: form --}}
    <div>
        <div class="bg-white rounded-xl p-8 shadow-sm border border-graphite/5">
            <h2 class="font-display text-2xl font-bold text-navy-800 mb-6">{{ __('pages.contact.form.title') }}</h2>

            @if (session('contact_sent'))
                <div class="bg-emerald-50 border-l-4 border-emerald-500 p-5 rounded-lg mb-6">
                    <p class="font-display font-bold text-emerald-900 mb-1">{{ __('pages.contact.form.sent_title') }}</p>
                    <p class="text-emerald-900/80 text-sm">{{ __('pages.contact.form.sent_body') }}</p>
                </div>
            @endif

            <form action="{{ route('contact.submit') }}" method="POST" class="space-y-5">
                @csrf

                {{-- honeypot --}}
                <div class="hidden" aria-hidden="true">
                    <label>Web site (boş bırak): <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
                </div>

                <div>
                    <label for="c-name" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.contact.form.name') }} <span class="text-brass-600">*</span></label>
                    <input type="text" id="c-name" name="name" value="{{ old('name') }}" required maxlength="120" class="{{ $inputClass }}">
                    @error('name') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="c-email" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.contact.form.email') }} <span class="text-brass-600">*</span></label>
                    <input type="email" id="c-email" name="email" value="{{ old('email') }}" required maxlength="160" class="{{ $inputClass }}">
                    @error('email') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="c-subject" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.contact.form.subject') }} <span class="text-brass-600">*</span></label>
                    <input type="text" id="c-subject" name="subject" value="{{ old('subject') }}" required maxlength="160" class="{{ $inputClass }}">
                    @error('subject') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="c-message" class="block text-sm font-medium text-graphite mb-1">{{ __('pages.contact.form.message') }} <span class="text-brass-600">*</span></label>
                    <textarea id="c-message" name="message" rows="5" required maxlength="4000" class="{{ $inputClass }}">{{ old('message') }}</textarea>
                    @error('message') <p class="mt-1 text-xs text-red-600 font-medium">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn-accent w-full md:w-auto">
                    {{ __('pages.contact.form.submit') }}
                </button>
            </form>
        </div>
    </div>
</section>

@endsection
