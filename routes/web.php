<?php

use App\Http\Controllers\ApplicationFormController;
use App\Models\Event;
use App\Models\NewsPost;
use App\Models\Project;
use App\Models\Sponsor;
use App\Models\TeamMember;
use App\Models\TimelineEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $nextEvent = Event::active()->upcoming()->first();

    return view('home', [
        'sponsors'       => Sponsor::active()->get(),
        'nextEvent'      => $nextEvent,
        'nextEventDate'  => $nextEvent?->event_date?->toIso8601String(),
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

Route::get('/basvuru',                 [ApplicationFormController::class, 'index'])->name('forms.index');
Route::get('/basvuru/{form:slug}',     [ApplicationFormController::class, 'show'])->name('forms.show');
Route::post('/basvuru/{form:slug}',    [ApplicationFormController::class, 'submit'])
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
        'past'     => (clone $query)->past()->limit(12)->get(),
        'activeCat'  => $cat,
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
        'posts'     => $query->paginate(9)->withQueryString(),
        'activeCat' => $cat,
    ]);
})->name('news.index');

Route::get('/haberler/{post:slug}', function (NewsPost $post) {
    abort_unless($post->is_published, 404);
    return view('news.show', ['post' => $post]);
})->name('news.show');

Route::get('/lang/{locale}', function (string $locale, Request $request) {
    if (in_array($locale, \App\Http\Middleware\SetLocaleFromSession::SUPPORTED, true)) {
        $request->session()->put('locale', $locale);
    }
    return redirect($request->headers->get('referer') ?: route('home'));
})->name('lang.switch');
