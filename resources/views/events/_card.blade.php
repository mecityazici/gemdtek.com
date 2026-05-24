<a href="{{ route('events.show', $event) }}"
   class="group bg-white rounded-xl overflow-hidden shadow-sm border border-graphite/5 hover:shadow-lg transition-all">
    <div class="aspect-[16/9] bg-navy-100 relative overflow-hidden">
        @if ($event->cover_url)
            <img src="{{ $event->cover_thumb_url }}" srcset="{{ $event->cover_thumb_url }} 400w, {{ $event->cover_web_url }} 1280w" sizes="(max-width: 768px) 100vw, 33vw" alt="" loading="lazy" decoding="async" width="400" height="225" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
        @else
            <div class="w-full h-full bg-gradient-to-br from-petrol to-navy-800 flex items-center justify-center">
                <svg class="w-12 h-12 text-brass-400/40" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            </div>
        @endif
        <span class="absolute top-3 left-3 bg-navy-900/80 text-cream px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider">
            {{ $event->category_label }}
        </span>
        @if ($event->is_upcoming)
            <span class="absolute top-3 right-3 bg-brass-500 text-white px-3 py-1 rounded-full text-xs font-mono uppercase tracking-wider">
                {{ __('pages.events.badge_upcoming') }}
            </span>
        @endif
    </div>
    <div class="p-5">
        <p class="font-mono text-xs text-brass-600 mb-2">
            {{ $event->event_date->isoFormat('D MMMM YYYY · HH:mm') }}
        </p>
        <h3 class="font-display font-bold text-lg text-navy-800 group-hover:text-petrol transition-colors mb-2">
            {{ $event->title }}
        </h3>
        @if ($event->summary)
            <p class="text-graphite/70 text-sm leading-relaxed line-clamp-2">{{ $event->summary }}</p>
        @endif
        @if ($event->location)
            <p class="mt-3 text-xs text-graphite/60 flex items-center gap-1">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $event->location }}
            </p>
        @endif
    </div>
</a>
