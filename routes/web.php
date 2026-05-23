<?php

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
