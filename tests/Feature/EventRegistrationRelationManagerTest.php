<?php

namespace Tests\Feature;

use App\Filament\Resources\EventResource;
use App\Filament\Resources\EventResource\RelationManagers\RegistrationsRelationManager;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\User;
use Tests\TestCase;

class EventRegistrationRelationManagerTest extends TestCase
{
    public function test_event_resource_registers_relation_manager(): void
    {
        $this->assertContains(
            RegistrationsRelationManager::class,
            EventResource::getRelations(),
        );
    }

    public function test_event_has_many_registrations_relationship(): void
    {
        $event = Event::firstWhere('slug', 'cfd-atolyesi');
        $this->assertNotNull($event);

        $registration = EventRegistration::create([
            'event_id' => $event->id,
            'name' => 'Inline Test',
            'email' => 'inline@example.com',
            'status' => EventRegistration::STATUS_PENDING,
        ]);

        $event->refresh();
        $this->assertTrue($event->registrations->contains($registration));
        $this->assertSame($event->id, $registration->event->id);
    }

    public function test_event_edit_page_loads_for_super_admin(): void
    {
        $admin = User::where('email', 'admin@gemdtek.com')->first();
        $this->assertNotNull($admin);

        $event = Event::firstWhere('slug', 'cfd-atolyesi');

        $this->actingAs($admin)
            ->get(EventResource::getUrl('edit', ['record' => $event]))
            ->assertOk();
    }
}
