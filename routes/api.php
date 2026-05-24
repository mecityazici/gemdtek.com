<?php

use App\Http\Controllers\Api\AlumniController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SponsorController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('throttle:60,1')->group(function () {
    Route::get('/events', [EventController::class, 'index'])->name('api.events.index');
    Route::get('/events/{event:slug}', [EventController::class, 'show'])->name('api.events.show');

    Route::get('/news', [NewsController::class, 'index'])->name('api.news.index');
    Route::get('/news/{post:slug}', [NewsController::class, 'show'])->name('api.news.show');

    Route::get('/projects', [ProjectController::class, 'index'])->name('api.projects.index');
    Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('api.projects.show');

    Route::get('/alumni', [AlumniController::class, 'index'])->name('api.alumni.index');

    Route::get('/sponsors', [SponsorController::class, 'index'])->name('api.sponsors.index');

    Route::get('/', function () {
        return response()->json([
            'api' => 'GEMDTEK Public API',
            'version' => 'v1',
            'endpoints' => [
                'GET /api/v1/events?upcoming=1|past=1&category=...&per_page=25&lang=tr|en',
                'GET /api/v1/events/{slug}',
                'GET /api/v1/news?category=...&per_page=25&lang=tr|en',
                'GET /api/v1/news/{slug}',
                'GET /api/v1/projects?status=active|completed|upcoming&lang=tr|en',
                'GET /api/v1/projects/{slug}',
                'GET /api/v1/alumni?sector=tersane&year=2020&lang=tr|en',
                'GET /api/v1/sponsors',
            ],
            'rate_limit' => '60 req/min',
        ]);
    })->name('api.root');
});
