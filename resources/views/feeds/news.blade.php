<?php echo '<?xml version="1.0" encoding="UTF-8"?>'."\n"; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/">
    <channel>
        <title>GEMDTEK — Haberler</title>
        <link>{{ route('news.index') }}</link>
        <atom:link href="{{ route('news.rss') }}" rel="self" type="application/rss+xml"/>
        <description>Gemi İnşaatı ve Deniz Teknolojileri Kulübü duyuru, blog ve basın yansımaları.</description>
        <language>{{ app()->getLocale() === 'en' ? 'en-us' : 'tr-tr' }}</language>
        <lastBuildDate>{{ now()->toRssString() }}</lastBuildDate>
        @foreach ($posts as $post)
            <item>
                <title>{{ $post->title }}</title>
                <link>{{ route('news.show', $post) }}</link>
                <guid isPermaLink="true">{{ route('news.show', $post) }}</guid>
                <pubDate>{{ ($post->published_at ?? $post->created_at)->toRssString() }}</pubDate>
                <category>{{ $post->category_label }}</category>
                @if ($post->excerpt)
                    <description><![CDATA[{{ $post->excerpt }}]]></description>
                @endif
                @if ($post->content)
                    <content:encoded><![CDATA[{!! $post->content !!}]]></content:encoded>
                @endif
            </item>
        @endforeach
    </channel>
</rss>
