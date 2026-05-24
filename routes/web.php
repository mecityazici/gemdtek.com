<?php

use App\Http\Controllers\ApplicationFormController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\EventRegistrationController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SponsorLeadController;
use App\Http\Middleware\SetLocaleFromSession;
use App\Models\Alumni;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Form;
use App\Models\NewsPost;
use App\Models\Project;
use App\Models\SiteMetric;
use App\Models\Sponsor;
use App\Models\TeamMember;
use App\Models\TimelineEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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

Route::get('/etkinlikler/rss', function () {
    $upcoming = Event::active()->upcoming()->limit(10)->get();
    $past = Event::active()->past()->limit(10)->get();
    $events = $upcoming->concat($past);

    return response()
        ->view('feeds.events', ['events' => $events])
        ->header('Content-Type', 'application/rss+xml; charset=utf-8');
})->name('events.rss');

Route::get('/etkinlikler/takvim', function (Request $request) {
    $monthInput = $request->string('month')->toString();
    try {
        $cursor = $monthInput
            ? Carbon::createFromFormat('Y-m', $monthInput)->startOfMonth()
            : Carbon::now()->startOfMonth();
    } catch (Throwable) {
        $cursor = Carbon::now()->startOfMonth();
    }

    $rangeStart = $cursor->copy()->startOfWeek();
    $rangeEnd = $cursor->copy()->endOfMonth()->endOfWeek();

    $events = Event::active()
        ->whereBetween('event_date', [$rangeStart, $rangeEnd])
        ->orderBy('event_date')
        ->get()
        ->groupBy(fn (Event $e) => $e->event_date->toDateString());

    return view('events.calendar', [
        'cursor' => $cursor,
        'rangeStart' => $rangeStart,
        'rangeEnd' => $rangeEnd,
        'eventsByDay' => $events,
        'prevMonth' => $cursor->copy()->subMonth()->format('Y-m'),
        'nextMonth' => $cursor->copy()->addMonth()->format('Y-m'),
    ]);
})->name('events.calendar');

Route::get('/etkinlikler/kayit/onay/{token}', [EventRegistrationController::class, 'confirm'])
    ->where('token', '[A-Za-z0-9]{48}')
    ->name('events.registrations.confirm');
Route::get('/etkinlikler/kayit/iptal/{token}', [EventRegistrationController::class, 'cancel'])
    ->where('token', '[A-Za-z0-9]{48}')
    ->name('events.registrations.cancel');

Route::get('/etkinlikler/{event:slug}', function (Event $event) {
    abort_unless($event->is_active, 404);
    $event->loadCount(['registrations as confirmed_registrations_count' => function ($q) {
        $q->where('status', EventRegistration::STATUS_CONFIRMED);
    }]);

    return view('events.show', ['event' => $event]);
})->name('events.show');

Route::get('/etkinlikler/{event:slug}/ics', [EventRegistrationController::class, 'ics'])
    ->name('events.ics');
Route::post('/etkinlikler/{event:slug}/kayit', [EventRegistrationController::class, 'register'])
    ->middleware('throttle:5,1')
    ->name('events.registrations.store');

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

Route::get('/haberler/rss', function () {
    $posts = NewsPost::published()->limit(20)->get();

    return response()
        ->view('feeds.news', ['posts' => $posts])
        ->header('Content-Type', 'application/rss+xml; charset=utf-8');
})->name('news.rss');

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
Route::view('/offline', 'offline')->name('offline');

Route::get('/iletisim', [ContactController::class, 'show'])->name('contact');
Route::post('/iletisim', [ContactController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');

Route::get('/sponsor-ol', [SponsorLeadController::class, 'show'])->name('sponsor.show');
Route::post('/sponsor-ol', [SponsorLeadController::class, 'submit'])
    ->middleware('throttle:5,1')
    ->name('sponsor.submit');

Route::get('/arama', [SearchController::class, 'index'])->name('search');

Route::get('/bulten', [NewsletterController::class, 'show'])->name('newsletter.show');
Route::post('/bulten', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:5,1')
    ->name('newsletter.subscribe');
Route::get('/bulten/onayla/{token}', [NewsletterController::class, 'confirm'])
    ->where('token', '[A-Za-z0-9]{48}')
    ->name('newsletter.confirm');
Route::get('/bulten/cikis/{token}', [NewsletterController::class, 'unsubscribe'])
    ->where('token', '[A-Za-z0-9]{48}')
    ->name('newsletter.unsubscribe');

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
    $urls->push(['loc' => route('newsletter.show'), 'changefreq' => 'monthly', 'priority' => '0.5']);
    $urls->push(['loc' => route('events.calendar'), 'changefreq' => 'weekly', 'priority' => '0.6']);
    $urls->push(['loc' => route('news.rss'),      'changefreq' => 'daily',   'priority' => '0.4']);
    $urls->push(['loc' => route('events.rss'),    'changefreq' => 'weekly',  'priority' => '0.4']);

    return response()
        ->view('sitemap', ['urls' => $urls])
        ->header('Content-Type', 'application/xml; charset=utf-8');
})->name('sitemap');
