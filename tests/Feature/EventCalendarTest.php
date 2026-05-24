<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EventCalendarTest extends TestCase
{
    public function test_calendar_renders_current_month_by_default(): void
    {
        $this->get('/etkinlikler/takvim')
            ->assertOk()
            ->assertSee(Carbon::now()->isoFormat('MMMM YYYY'));
    }

    public function test_month_query_param_navigates_to_specific_month(): void
    {
        $response = $this->get('/etkinlikler/takvim?month=2026-10');

        $response->assertOk()
            ->assertSee(Carbon::createFromFormat('Y-m', '2026-10')->isoFormat('MMMM YYYY'));
    }

    public function test_invalid_month_input_falls_back_to_current_month(): void
    {
        $this->get('/etkinlikler/takvim?month=garbage')
            ->assertOk()
            ->assertSee(Carbon::now()->isoFormat('MMMM YYYY'));
    }

    public function test_seeded_event_appears_in_its_month_cell(): void
    {
        $event = Event::active()->first();
        $this->assertNotNull($event);

        $monthParam = $event->event_date->format('Y-m');

        $this->get('/etkinlikler/takvim?month='.$monthParam)
            ->assertOk()
            ->assertSee($event->title)
            ->assertSee($event->event_date->format('H:i'));
    }

    public function test_calendar_has_navigation_to_prev_and_next_months(): void
    {
        $cursor = Carbon::createFromFormat('Y-m', '2026-06');

        $response = $this->get('/etkinlikler/takvim?month=2026-06');
        $response->assertOk()
            ->assertSee('month='.$cursor->copy()->subMonth()->format('Y-m'), escape: false)
            ->assertSee('month='.$cursor->copy()->addMonth()->format('Y-m'), escape: false);
    }

    public function test_events_index_links_to_calendar_view(): void
    {
        $this->get('/etkinlikler')
            ->assertOk()
            ->assertSee(route('events.calendar'), escape: false);
    }
}
