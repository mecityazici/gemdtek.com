@extends('layouts.app')

@section('title', __('pages.events.calendar.title') . ' — GEMDTEK')
@section('meta_description', __('pages.events.calendar.meta'))

@section('content')

<section class="bg-navy-900 text-cream">
    <div class="container-tight py-16 md:py-20">
        <p class="font-mono text-xs uppercase tracking-widest text-brass-300 mb-3">{{ __('pages.events.calendar.eyebrow') }}</p>
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <h1 class="text-3xl md:text-5xl font-display font-bold leading-tight">
                    {{ $cursor->isoFormat('MMMM YYYY') }}
                </h1>
                <p class="text-cream/70 mt-2 text-sm">{{ __('pages.events.calendar.subline') }}</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('events.calendar', ['month' => $prevMonth]) }}"
                   class="inline-flex items-center gap-1 px-3 py-2 rounded-md border border-cream/20 hover:bg-cream/10 transition-colors text-sm"
                   aria-label="{{ __('pages.events.calendar.prev') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $prevMonth)->isoFormat('MMM') }}
                </a>
                <a href="{{ route('events.calendar') }}"
                   class="px-3 py-2 rounded-md bg-brass-500 hover:bg-brass-400 text-white text-sm font-medium transition-colors">
                    {{ __('pages.events.calendar.today') }}
                </a>
                <a href="{{ route('events.calendar', ['month' => $nextMonth]) }}"
                   class="inline-flex items-center gap-1 px-3 py-2 rounded-md border border-cream/20 hover:bg-cream/10 transition-colors text-sm"
                   aria-label="{{ __('pages.events.calendar.next') }}">
                    {{ \Illuminate\Support\Carbon::createFromFormat('Y-m', $nextMonth)->isoFormat('MMM') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 mt-6 text-sm">
            <a href="{{ route('events.index') }}" class="text-cream/70 hover:text-cream underline-offset-4 hover:underline">
                {{ __('pages.events.calendar.list_view') }}
            </a>
            <span class="text-cream/30">·</span>
            <a href="{{ route('events.rss') }}" class="text-cream/70 hover:text-cream underline-offset-4 hover:underline">
                RSS
            </a>
        </div>
    </div>
</section>

<section class="container-tight py-12">
    @php
        $weekdays = [
            __('pages.events.calendar.weekdays.mon'),
            __('pages.events.calendar.weekdays.tue'),
            __('pages.events.calendar.weekdays.wed'),
            __('pages.events.calendar.weekdays.thu'),
            __('pages.events.calendar.weekdays.fri'),
            __('pages.events.calendar.weekdays.sat'),
            __('pages.events.calendar.weekdays.sun'),
        ];
        $today = \Illuminate\Support\Carbon::today();
        $currentMonth = $cursor->month;
    @endphp

    <div class="grid grid-cols-7 gap-px bg-graphite/10 rounded-lg overflow-hidden border border-graphite/10">
        @foreach ($weekdays as $weekday)
            <div class="bg-navy-50 px-3 py-2 text-xs font-mono uppercase tracking-wider text-navy-800 text-center">
                {{ $weekday }}
            </div>
        @endforeach

        @php $cell = $rangeStart->copy(); @endphp
        @while ($cell->lte($rangeEnd))
            @php
                $isInMonth = $cell->month === $currentMonth;
                $isToday = $cell->isSameDay($today);
                $dayEvents = $eventsByDay[$cell->toDateString()] ?? collect();
            @endphp

            <div class="bg-white min-h-[7rem] p-2 text-sm relative {{ $isInMonth ? '' : 'opacity-40' }} {{ $isToday ? 'ring-2 ring-brass-500 ring-inset' : '' }}">
                <div class="flex items-center justify-between mb-1.5">
                    <span class="font-mono text-xs {{ $isToday ? 'text-brass-600 font-bold' : 'text-graphite/60' }}">
                        {{ $cell->day }}
                    </span>
                    @if ($dayEvents->count() > 0)
                        <span class="text-[10px] font-mono bg-petrol text-cream rounded-full px-1.5 py-0.5">
                            {{ $dayEvents->count() }}
                        </span>
                    @endif
                </div>

                @foreach ($dayEvents->take(2) as $event)
                    <a href="{{ route('events.show', $event) }}"
                       class="block mb-1 px-1.5 py-1 rounded text-[11px] leading-tight bg-navy-50 hover:bg-brass-50 border-l-2 border-brass-500 text-navy-800 truncate"
                       title="{{ $event->title }}">
                        <span class="font-mono text-[10px] text-graphite/60 mr-1">{{ $event->event_date->format('H:i') }}</span>
                        <span class="font-medium">{{ $event->title }}</span>
                    </a>
                @endforeach
                @if ($dayEvents->count() > 2)
                    <p class="text-[10px] text-graphite/50 mt-1">
                        +{{ $dayEvents->count() - 2 }} {{ __('pages.events.calendar.more') }}
                    </p>
                @endif
            </div>

            @php $cell->addDay(); @endphp
        @endwhile
    </div>
</section>

@endsection
