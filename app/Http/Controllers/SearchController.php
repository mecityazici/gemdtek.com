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

    private const LIMIT_PER_GROUP = 6;

    public function index(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        $results = [
            'projects' => collect(),
            'news' => collect(),
            'events' => collect(),
            'alumni' => collect(),
        ];
        $total = 0;

        if (mb_strlen($q) >= self::MIN_LENGTH) {
            $like = '%'.$q.'%';

            $results['projects'] = Project::active()
                ->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('problem_statement', 'like', $like);
                })
                ->limit(self::LIMIT_PER_GROUP)
                ->get();

            $results['news'] = NewsPost::published()
                ->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like)
                        ->orWhere('content', 'like', $like);
                })
                ->limit(self::LIMIT_PER_GROUP)
                ->get();

            $results['events'] = Event::active()
                ->where(function ($w) use ($like) {
                    $w->where('title', 'like', $like)
                        ->orWhere('summary', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('location', 'like', $like);
                })
                ->orderByDesc('event_date')
                ->limit(self::LIMIT_PER_GROUP)
                ->get();

            $results['alumni'] = Alumni::public()
                ->where(function ($w) use ($like) {
                    $w->where('name', 'like', $like)
                        ->orWhere('position', 'like', $like)
                        ->orWhere('company', 'like', $like);
                })
                ->limit(self::LIMIT_PER_GROUP)
                ->get();

            $total = $results['projects']->count()
                + $results['news']->count()
                + $results['events']->count()
                + $results['alumni']->count();
        }

        return view('search.index', [
            'q' => $q,
            'results' => $results,
            'total' => $total,
            'tooShort' => mb_strlen($q) > 0 && mb_strlen($q) < self::MIN_LENGTH,
            'minLength' => self::MIN_LENGTH,
        ]);
    }
}
