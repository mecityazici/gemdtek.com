<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>GEMDTEK — Etkinlikler</title>
        <link>{{ route('events.index') }}</link>
        <atom:link href="{{ route('events.rss') }}" rel="self" type="application/rss+xml"/>
        <description>GEMDTEK zirveleri, atölyeler, kariyer günleri ve sektör etkinlikleri.</description>
        <language>{{ app()->getLocale() === 'en' ? 'en-us' : 'tr-tr' }}</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        @foreach ($events as $event)
            <item>
                <title>{{ $event->title }}</title>
                <link>{{ route('events.show', $event) }}</link>
                <guid isPermaLink="true">{{ route('events.show', $event) }}</guid>
                <pubDate>{{ $event->event_date->toRssString() }}</pubDate>
                <category>{{ $event->category_label }}</category>
                @if ($event->summary)
                    <description><![CDATA[{{ $event->summary }}]]></description>
                @endif
                @if ($event->description)
                    <content:encoded><![CDATA[{!! $event->description !!}]]></content:encoded>
                @endif
            </item>
        @endforeach
    </channel>
</rss>
