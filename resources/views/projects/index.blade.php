@extends('layouts.app')

@section('title', 'Ar-Ge & Projeler — GEMDTEK')

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-20 md:py-28">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-4">Ar-Ge & Projeler</p>
        <h1 class="text-4xl md:text-5xl font-display font-bold mb-6 max-w-3xl leading-tight">
            Mühendislik <span class="text-brass-400">vitrinimiz.</span>
        </h1>
        <p class="text-lg text-cream/80 max-w-3xl">
            Aktif Ar-Ge takımlarımız, alternatif yakıtlı tasarımlarımız ve otonom denizcilik projelerimiz —
            sektör paydaşlarımızla birlikte sürdürdüğümüz mühendislik çalışmaları.
        </p>
    </div>
</section>

<section class="container-tight py-20">
    @if ($projects->isEmpty())
        <p class="text-center text-graphite/60 italic">Aktif proje yayında değil.</p>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($projects as $project)
                <a href="{{ route('projects.show', $project) }}"
                   class="group bg-white rounded-xl overflow-hidden shadow-sm border border-graphite/5 hover:shadow-xl hover:-translate-y-1 transition-all">
                    <div class="aspect-[4/3] bg-navy-100 relative overflow-hidden">
                        @if ($project->hero_url)
                            <img src="{{ $project->hero_url }}" alt="{{ $project->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-navy-800 to-petrol flex items-center justify-center">
                                <span class="font-display text-5xl text-brass-400/40 font-bold">GEMDTEK</span>
                            </div>
                        @endif
                        <span @class([
                            'absolute top-3 right-3 px-3 py-1 rounded-full text-xs font-medium font-mono uppercase tracking-wider',
                            'bg-emerald-500 text-white' => $project->status === 'active',
                            'bg-graphite text-cream'    => $project->status === 'completed',
                            'bg-brass-500 text-white'   => $project->status === 'upcoming',
                        ])>{{ $project->status_label }}</span>
                    </div>
                    <div class="p-6">
                        @if ($project->year)
                            <p class="font-mono text-xs text-brass-600 mb-2">{{ $project->year }}</p>
                        @endif
                        <h2 class="font-display font-bold text-xl text-navy-800 mb-2 group-hover:text-petrol transition-colors">
                            {{ $project->name }}
                        </h2>
                        <p class="text-graphite/70 text-sm leading-relaxed">{{ $project->summary }}</p>
                        <p class="mt-4 text-sm font-medium text-brass-600 inline-flex items-center gap-1">
                            İncele
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
</section>

@endsection
