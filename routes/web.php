<?php

use App\Models\Project;
use App\Models\Sponsor;
use App\Models\TeamMember;
use App\Models\TimelineEvent;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home', [
        'sponsors' => Sponsor::active()->get(),
        'nextEventDate' => '2026-10-25T10:00:00+03:00',
        'nextEventTitle' => 'GEMDTEK Denizcilik Kariyer Zirvesi 2026',
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
