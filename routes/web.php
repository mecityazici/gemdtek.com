<?php

use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SponsorLeadController;
use App\Http\Middleware\SetLocaleFromSession;
use App\Models\Alumni;
use App\Models\Event;
use App\Models\Form;
use App\Models\NewsPost;
use App\Models\Project;
use App\Models\SiteMetric;
use App\Models\Sponsor;
use App\Models\TeamMember;
use App\Models\TimelineEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $nextEvent = Event::active()->upcoming()->first();

    return view('home', [
        'metrics' => SiteMetric::active()->get(),
        'sponsors' => Sponsor::active()->get(),
        'nextEvent' => $nextEvent,
        'nextEventDate' => $nextEvent?->event_date?->toIso8601String(),
        'nextEventTitle' => $nextEvent?->title ?? 'Yaklaşan etkinlik henüz duyurulmadı',
    ]);
})->name('home');

Route::get('/hakkimizda', function () {
    return view('about', [
        'team' => TeamMember::active()->get(),
        'timeline' => TimelineEvent::orderBy('year')->orderBy('order')->get(),
    ]);
})->name('about');

Route::get('/ar-ge', function () {
    return view('projects.index', [
        'projects' => Project::active()->orderBy('order')->get(),
    ]);
})->name('projects.index');

Route::get('/ar-ge/{project:slug}', function (Project $project) {
    abort_unless($project->is_active, 404);
    $project->load(['specs', 'members']);

    return view('projects.show', [
        'project' => $project,
        'specsByCategory' => $project->specs->groupBy('category'),
        'captain' => $project->members->firstWhere('is_captain', true),
        'crew' => $project->members->where('is_captain', false)->values(),
    ]);
})->name('projects.show');

Route::get('/basvuru', [ApplicationFormController::class, 'index'])->name('forms.index');
Route::get('/basvuru/{form:slug}', [ApplicationFormController::class, 'show'])->name('forms.show');
Route::post('/basvuru/{form:slug}', [ApplicationFormController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('forms.submit');

Route::get('/etkinlikler', function (Request $request) {
    $cat = $request->string('cat')->toString();
    $query = Event::active();
    if ($cat && array_key_exists($cat, Event::CATEGORIES)) {
        $query->where('category', $cat);
    }

    return view('events.index', [
        'upcoming' => (clone $query)->upcoming()->get(),
        'past' => (clone $query)->past()->limit(12)->get(),
        'activeCat' => $cat,
    ]);
})->name('events.index');

Route::get('/etkinlikler/{event:slug}', function (Event $event) {
    abort_unless($event->is_active, 404);

    return view('events.show', ['event' => $event]);
})->name('events.show');

Route::get('/haberler', function (Request $request) {
    $cat = $request->string('cat')->toString();
    $query = NewsPost::published();
    if ($cat && array_key_exists($cat, NewsPost::CATEGORIES)) {
        $query->where('category', $cat);
    }

    return view('news.index', [
        'posts' => $query->paginate(9)->withQueryString(),
        'activeCat' => $cat,
    ]);
})->name('news.index');

Route::get('/haberler/{post:slug}', function (NewsPost $post) {
    abort_unless($post->is_published, 404);

    return view('news.show', ['post' => $post]);
})->name('news.show');

Route::get('/lang/{locale}', function (string $locale, Request $request) {
    if (in_array($locale, SetLocaleFromSession::SUPPORTED, true)) {
        $request->session()->put('locale', $locale);
    }

    return redirect($request->headers->get('referer') ?: route('home'));
})->name('lang.switch');

Route::view('/kvkk', 'legal.privacy')->name('legal.privacy');

Route::get('/iletisim', [ContactController::class, 'show'])->name('contact');
Route::post('/iletisim', [ContactController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');

Route::get('/sponsor-ol', [SponsorLeadController::class, 'show'])->name('sponsor.show');
Route::post('/sponsor-ol', [SponsorLeadController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('sponsor.submit');

Route::get('/arama', [SearchController::class, 'index'])->name('search');

Route::get('/mezunlar', function (Request $request) {
    $sector = $request->string('sector')->toString();
    $year = (int) $request->integer('year');

    $query = Alumni::public()->orderBy('order')->orderByDesc('graduation_year');
    if ($sector && array_key_exists($sector, Alumni::SECTORS)) {
        $query->where('sector', $sector);
    }
    if ($year >= 1980 && $year <= 2100) {
        $query->where('graduation_year', $year);
    }

    return view('alumni.index', [
        'alumni' => $query->paginate(24)->withQueryString(),
        'activeSec' => $sector,
        'activeYear' => $year ?: null,
        'years' => Alumni::public()->whereNotNull('graduation_year')->distinct()->orderByDesc('graduation_year')->pluck('graduation_year'),
    ]);
})->name('alumni.index');

Route::get('/sitemap.xml', function () {
    $urls = collect();

    $urls->push(['loc' => route('home'),             'changefreq' => 'daily',  'priority' => '1.0']);
    $urls->push(['loc' => route('about'),            'changefreq' => 'monthly', 'priority' => '0.8']);
    $urls->push(['loc' => route('projects.index'),   'changefreq' => 'weekly', 'priority' => '0.8']);
    $urls->push(['loc' => route('events.index'),     'changefreq' => 'weekly', 'priority' => '0.7']);
    $urls->push(['loc' => route('news.index'),       'changefreq' => 'daily',  'priority' => '0.7']);
    $urls->push(['loc' => route('forms.index'),      'changefreq' => 'weekly', 'priority' => '0.6']);
    $urls->push(['loc' => route('legal.privacy'),    'changefreq' => 'yearly', 'priority' => '0.3']);

    Project::active()->each(function ($p) use ($urls) {
        $urls->push([
            'loc' => route('projects.show', $p),
            'lastmod' => $p->updated_at?->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.7',
        ]);
    });

    Event::active()->each(function ($e) use ($urls) {
        $urls->push([
            'loc' => route('events.show', $e),
            'lastmod' => $e->updated_at?->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.6',
        ]);
    });

    NewsPost::published()->each(function ($n) use ($urls) {
        $urls->push([
            'loc' => route('news.show', $n),
            'lastmod' => $n->updated_at?->toAtomString(),
            'changefreq' => 'monthly',
            'priority' => '0.6',
        ]);
    });

    Form::open()->each(function ($f) use ($urls) {
        $urls->push([
            'loc' => route('forms.show', $f),
            'lastmod' => $f->updated_at?->toAtomString(),
            'changefreq' => 'weekly',
            'priority' => '0.5',
        ]);
    });

    $urls->push(['loc' => route('alumni.index'),  'changefreq' => 'weekly', 'priority' => '0.5']);
    $urls->push(['loc' => route('contact'),       'changefreq' => 'yearly', 'priority' => '0.4']);
    $urls->push(['loc' => route('sponsor.show'),  'changefreq' => 'monthly', 'priority' => '0.7']);

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml; charset=utf-8');
})->name('sitemap');
