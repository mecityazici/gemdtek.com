<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use App\Models\Event;
use App\Models\NewsPost;
use App\Models\Project;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const MIN_LENGTH = 2;

    private const LIMIT_OVERVIEW = 6;

    private const LIMIT_FOCUSED = 25;

    public const TYPES = ['projects', 'news', 'events', 'alumni'];

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $type = (string) $request->input('type', '');
        if (! in_array($type, self::TYPES, true)) {
            $type = '';
        }

        $results = array_fill_keys(self::TYPES, collect());
        $totals = array_fill_keys(self::TYPES, 0);
        $total = 0;

        if (mb_strlen($q) >= self::MIN_LENGTH) {
            $like = '%'.$q.'%';
            $limit = $type === '' ? self::LIMIT_OVERVIEW : self::LIMIT_FOCUSED;

            $totals['projects'] = Project::active()
                ->where(fn ($w) => $w->where('name', 'like', $like)
                    ->orWhere('summary', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('problem_statement', 'like', $like))
                ->count();

            $totals['news'] = NewsPost::published()
                ->where(fn ($w) => $w->where('title', 'like', $like)
                    ->orWhere('excerpt', 'like', $like)
                    ->orWhere('content', 'like', $like))
                ->count();

            $totals['events'] = Event::active()
                ->where(fn ($w) => $w->where('title', 'like', $like)
                    ->orWhere('summary', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('location', 'like', $like))
                ->count();

            $totals['alumni'] = Alumni::public()
                ->where(fn ($w) => $w->where('name', 'like', $like)
                    ->orWhere('position', 'like', $like)
                    ->orWhere('company', 'like', $like))
                ->count();

            if ($type === '' || $type === 'projects') {
                $results['projects'] = Project::active()
                    ->where(fn ($w) => $w->where('name', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('problem_statement', 'like', $like))
                    ->limit($limit)
                    ->get();
            }

            if ($type === '' || $type === 'news') {
                $results['news'] = NewsPost::published()
                    ->where(fn ($w) => $w->where('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('content', 'like', $like))
                    ->limit($limit)
                    ->get();
            }

            if ($type === '' || $type === 'events') {
                $results['events'] = Event::active()
                    ->where(fn ($w) => $w->where('title', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('location', 'like', $like))
                    ->orderByDesc('event_date')
                    ->limit($limit)
                    ->get();
            }

            if ($type === '' || $type === 'alumni') {
                $results['alumni'] = Alumni::public()
                    ->where(fn ($w) => $w->where('name', 'like', $like)
                        ->orWhere('position', 'like', $like)
                        ->orWhere('company', 'like', $like))
                    ->limit($limit)
                    ->get();
            }

            $total = array_sum($totals);
        }

        return view('search.index', [
            'q' => $q,
            'type' => $type,
            'results' => $results,
            'totals' => $totals,
            'total' => $total,
            'tooShort' => mb_strlen($q) > 0 && mb_strlen($q) < self::MIN_LENGTH,
            'minLength' => self::MIN_LENGTH,
        ]);
    }
}
