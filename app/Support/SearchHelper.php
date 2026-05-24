<?php

namespace App\Support;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class SearchHelper
{
    public const EXCERPT_RADIUS = 80;

    public static function highlight(?string $text, string $query): HtmlString
    {
        $safe = e((string) $text);
        if ($query === '' || $safe === '') {
            return new HtmlString($safe);
        }

        $pattern = '/('.preg_quote($query, '/').')/iu';
        $highlighted = preg_replace($pattern, '<mark class="bg-brass-100 text-navy-900 rounded px-0.5">$1</mark>', $safe);

        return new HtmlString($highlighted ?? $safe);
    }

    /**
     * Build a short excerpt around the first occurrence of $query.
     * Falls back to the first $radius*2 characters when no match.
     */
    public static function excerpt(?string $text, string $query, int $radius = self::EXCERPT_RADIUS): string
    {
        $text = trim(strip_tags((string) $text));
        if ($text === '') {
            return '';
        }

        if ($query === '') {
            return Str::limit($text, $radius * 2);
        }

        $position = mb_stripos($text, $query);
        if ($position === false) {
            return Str::limit($text, $radius * 2);
        }

        $start = max(0, $position - $radius);
        $length = mb_strlen($query) + ($radius * 2);
        $snippet = mb_substr($text, $start, $length);

        $prefix = $start > 0 ? '… ' : '';
        $suffix = ($start + $length) < mb_strlen($text) ? ' …' : '';

        return $prefix.trim($snippet).$suffix;
    }
}
