@extends('layouts.app')

@section('title', __('pages.news.eyebrow') . ' — GEMDTEK')

@php
    $categories = \App\Models\NewsPost::CATEGORIES;
@endphp

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">{{ __('pages.news.eyebrow') }}</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            {{ __('pages.news.headline_lead') }} <span class="text-brass-400">{{ __('pages.news.headline_accent') }}</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">
            {{ __('pages.news.subline') }}
        </p>
    </div>
</section>

<section class="container-tight py-10">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('news.index') }}"
           class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $activeCat === '' ? 'bg-navy-800 text-cream' : 'bg-cream text-navy-800 hover:bg-navy-100' }}">
            {{ __('site.cta.all') }}
        </a>
        @foreach ($categories as $key => $label)
            <a href="{{ route('news.index', ['cat' => $key]) }}"
               class="px-4 py-2 rounded-full text-sm font-medium transition-colors {{ $activeCat === $key ? 'bg-navy-800 text-cream' : 'bg-cream text-navy-800 hover:bg-navy-100' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>
</section>

<section class="container-tight pb-20">
    @if ($posts->isEmpty())
        <div class="max-w-2xl mx-auto text-center bg-cream rounded-xl p-12 border border-graphite/10">
            <p class="font-display text-xl text-navy-800 font-bold">{{ __('pages.news.empty') }}</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($posts as $post)
                <a href="{{ route('news.show', $post) }}"
                   class="group bg-white rounded-xl overflow-hidden shadow-sm border border-graphite/5 hover:shadow-lg transition-all">
                    <div class="aspect-[16/9] bg-navy-100 relative overflow-hidden">
                        @if ($post->cover_url)
                            <img src="{{ $post->cover_url }}" alt="" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-navy-800 to-petrol flex items-center justify-center">
                                <span class="font-display text-3xl text-brass-400/40 font-bold">{{ Str::upper(substr($post->title, 0, 1)) }}</span>
                            </div>
                        @endif
                        <span class="absolute top-3 left-3 bg-navy-900/80 text-cream px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider">
                            {{ $post->category_label }}
                        </span>
                    </div>
                    <div class="p-5">
                        @if ($post->published_at)
                            <p class="font-mono text-xs text-brass-600 mb-2">{{ $post->published_at->isoFormat('D MMMM YYYY') }}</p>
                        @endif
                        <h3 class="font-display font-bold text-lg text-navy-800 group-hover:text-petrol transition-colors mb-2">
                            {{ $post->title }}
                        </h3>
                        @if ($post->excerpt)
                            <p class="text-graphite/70 text-sm leading-relaxed line-clamp-3">{{ $post->excerpt }}</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>
    @endif
</section>

@endsection
