<?php

namespace Tests\Feature;

use App\Mail\EventRegistrationConfirmation;
use App\Mail\EventRegistrationConfirmed;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use App\Notifications\NewEventRegistrationNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    private function openEvent(array $overrides = []): Event
    {
        $event = Event::firstWhere('slug', 'cfd-atolyesi');

        if ($overrides) {
            $event->forceFill($overrides)->save();
        }

        return $event->fresh();
    }

    public function test_rsvp_form_appears_only_when_registration_enabled(): void
    {
        $this->get('/etkinlikler/cfd-atolyesi')
            ->assertOk()
            ->assertSee('Yerini şimdiden ayır');

        $this->get('/etkinlikler/denizcilik-kariyer-zirvesi-2026')
            ->assertOk()
            ->assertDontSee('Yerini şimdiden ayır');
    }

    public function test_register_creates_pending_registration_and_queues_confirmation(): void
    {
        Mail::fake();
        $event = $this->openEvent();

        $this->post('/etkinlikler/'.$event->slug.'/kayit', [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'affiliation' => 'ogrenci',
        ])->assertRedirect();

        $registration = EventRegistration::where('email', 'ada@example.com')->first();
        $this->assertNotNull($registration);
        $this->assertSame(EventRegistration::STATUS_PENDING, $registration->status);
        $this->assertSame($event->id, $registration->event_id);
        $this->assertNotNull($registration->confirm_token);
        $this->assertNotNull($registration->cancel_token);

        Mail::assertQueued(EventRegistrationConfirmation::class, fn ($m) => $m->registration->is($registration));
    }

    public function test_capacity_pushes_overflow_to_waitlist(): void
    {
        Mail::fake();
        $event = $this->openEvent(['capacity' => 2]);

        EventRegistration::create([
            'event_id' => $event->id,
            'name' => 'A', 'email' => 'a@example.com',
            'status' => EventRegistration::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);
        EventRegistration::create([
            'event_id' => $event->id,
            'name' => 'B', 'email' => 'b@example.com',
            'status' => EventRegistration::STATUS_PENDING,
        ]);

        $this->post('/etkinlikler/'.$event->slug.'/kayit', [
            'name' => 'Late Bird',
            'email' => 'late@example.com',
        ])->assertRedirect();

        $reg = EventRegistration::where('email', 'late@example.com')->first();
        $this->assertSame(EventRegistration::STATUS_WAITLIST, $reg->status);
        Mail::assertNotQueued(EventRegistrationConfirmation::class);
    }

    public function test_confirmation_link_confirms_and_queues_invitation_with_ics(): void
    {
        Mail::fake();
        Notification::fake();
        $event = $this->openEvent();

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'name' => 'Grace', 'email' => 'grace@example.com',
            'status' => EventRegistration::STATUS_PENDING,
        ]);

        $this->get('/etkinlikler/kayit/onay/'.$registration->confirm_token)
            ->assertOk();

        $registration->refresh();
        $this->assertSame(EventRegistration::STATUS_CONFIRMED, $registration->status);
        $this->assertNotNull($registration->confirmed_at);
        $this->assertNull($registration->confirm_token);

        Mail::assertQueued(EventRegistrationConfirmed::class);

        $admins = User::role(['super_admin', 'editor'])->get();
        Notification::assertSentTo($admins, NewEventRegistrationNotification::class);
    }

    public function test_cancel_link_marks_registration_cancelled(): void
    {
        $event = $this->openEvent();

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'name' => 'Linus', 'email' => 'linus@example.com',
            'status' => EventRegistration::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        $this->get('/etkinlikler/kayit/iptal/'.$registration->cancel_token)
            ->assertOk();

        $registration->refresh();
        $this->assertSame(EventRegistration::STATUS_CANCELLED, $registration->status);
        $this->assertNotNull($registration->cancelled_at);
    }

    public function test_invalid_token_shows_feedback(): void
    {
        $invalidToken = str_repeat('x', 48);
        $this->get('/etkinlikler/kayit/onay/'.$invalidToken)
            ->assertOk()
            ->assertSeeText('Geçersiz');
    }

    public function test_ics_endpoint_returns_calendar_with_event_data(): void
    {
        $event = $this->openEvent();

        $response = $this->get('/etkinlikler/'.$event->slug.'/ics');

        $response->assertOk();
        $this->assertStringContainsString('text/calendar', $response->headers->get('Content-Type'));
        $this->assertStringContainsString('BEGIN:VCALENDAR', $response->getContent());
        $this->assertStringContainsString('BEGIN:VEVENT', $response->getContent());
        $this->assertStringContainsString('CFD', $response->getContent());
        $this->assertStringContainsString('Online', $response->getContent());
    }

    public function test_honeypot_blocks_registration(): void
    {
        Mail::fake();
        $event = $this->openEvent();

        $this->post('/etkinlikler/'.$event->slug.'/kayit', [
            'name' => 'Spam', 'email' => 'spam@example.com',
            'website' => 'http://spam',
        ])->assertStatus(422);

        $this->assertDatabaseMissing('event_registrations', ['email' => 'spam@example.com']);
        Mail::assertNothingQueued();
    }

    public function test_closed_registration_redirects_back_with_status(): void
    {
        Mail::fake();
        $event = $this->openEvent(['registration_enabled' => false]);

        $this->post('/etkinlikler/'.$event->slug.'/kayit', [
            'name' => 'Sad', 'email' => 'closed@example.com',
        ])->assertRedirect();

        $this->assertDatabaseMissing('event_registrations', ['email' => 'closed@example.com']);
        Mail::assertNothingQueued();
    }

    public function test_duplicate_confirmed_email_returns_already_confirmed_state(): void
    {
        Mail::fake();
        $event = $this->openEvent();

        EventRegistration::create([
            'event_id' => $event->id,
            'name' => 'Existing', 'email' => 'existing@example.com',
            'status' => EventRegistration::STATUS_CONFIRMED,
            'confirmed_at' => now(),
        ]);

        $this->post('/etkinlikler/'.$event->slug.'/kayit', [
            'name' => 'Existing', 'email' => 'existing@example.com',
        ])
            ->assertRedirect()
            ->assertSessionHas('event_registration_status', 'already_confirmed');

        Mail::assertNothingQueued();
    }
}
