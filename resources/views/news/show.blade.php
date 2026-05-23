@extends('layouts.app')

@section('title', $post->title . ' — GEMDTEK')
@section('meta_description', $post->excerpt)

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-16 md:py-20">
        <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-brass-300 hover:text-brass-200 text-sm mb-6 font-mono">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            {{ __('pages.news.all_news') }}
        </a>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <span class="bg-navy-700/60 text-cream px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider">
                {{ $post->category_label }}
            </span>
            @if ($post->published_at)
                <span class="font-mono text-xs text-cream/60">{{ $post->published_at->isoFormat('D MMMM YYYY') }}</span>
            @endif
        </div>
        <h1 class="text-3xl md:text-5xl font-display font-bold mb-6 max-w-4xl leading-tight">{{ $post->title }}</h1>
        @if ($post->excerpt)
            <p class="text-lg text-cream/80 max-w-3xl">{{ $post->excerpt }}</p>
        @endif
    </div>
</section>

@if ($post->cover_url)
    <div class="container-tight -mt-8 mb-8">
        <img src="{{ $post->cover_url }}" alt="" class="rounded-xl w-full aspect-[16/9] object-cover shadow-lg">
    </div>
@endif

@if ($post->content)
    <article class="container-tight pb-20 max-w-3xl">
        <div class="prose prose-lg max-w-none prose-headings:font-display prose-headings:text-navy-800 prose-a:text-petrol prose-img:rounded-lg">
            {!! $post->content !!}
        </div>
    </article>
@endif

@endsection
