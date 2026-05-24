<?php

namespace App\Http\Controllers;

use App\Mail\EventRegistrationConfirmation;
use App\Mail\EventRegistrationConfirmed;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Notifications\NewEventRegistrationNotification;
use App\Support\AdminNotifier;
use App\Support\IcsGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EventRegistrationController extends Controller
{
    public function register(Request $request, Event $event)
    {
        abort_unless($event->is_active, 404);

        if (! $event->isRegistrationOpen()) {
            return back()->with('event_registration_status', 'closed');
        }

        if ($request->filled('website')) {
            abort(422, 'Spam tespit edildi.');
        }

        $data = $request->validate([
            'website' => 'prohibited',
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:160',
            'phone' => 'nullable|string|max:40',
            'affiliation' => 'nullable|in:'.implode(',', array_keys(EventRegistration::AFFILIATIONS)),
            'notes' => 'nullable|string|max:1000',
        ]);

        $existing = EventRegistration::where('event_id', $event->id)
            ->where('email', $data['email'])
            ->first();

        if ($existing && $existing->status === EventRegistration::STATUS_CONFIRMED) {
            return back()->with('event_registration_status', 'already_confirmed');
        }

        $status = $event->isFull() && ! $existing
            ? EventRegistration::STATUS_WAITLIST
            : EventRegistration::STATUS_PENDING;

        $registration = $existing ?: new EventRegistration;
        $registration->fill([
            'event_id' => $event->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'affiliation' => $data['affiliation'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => $status,
            'source' => $request->input('source', 'event-page'),
            'ip_address' => $request->ip(),
            'confirmed_at' => null,
            'cancelled_at' => null,
        ]);

        if ($existing) {
            $registration->confirm_token = Str::random(48);
        }

        $registration->save();

        if ($status === EventRegistration::STATUS_PENDING) {
            Mail::to($registration->email)->queue(new EventRegistrationConfirmation($registration));
        }

        return back()->with('event_registration_status', $status === EventRegistration::STATUS_WAITLIST ? 'waitlist' : 'pending');
    }

    public function confirm(string $token)
    {
        $registration = EventRegistration::where('confirm_token', $token)->first();

        if (! $registration) {
            return view('events.registration-feedback', ['state' => 'invalid_token']);
        }

        if ($registration->status !== EventRegistration::STATUS_CONFIRMED) {
            $registration->confirm();
            Mail::to($registration->email)->queue(new EventRegistrationConfirmed($registration));
            AdminNotifier::send(new NewEventRegistrationNotification($registration));
        }

        return view('events.registration-feedback', [
            'state' => 'confirmed',
            'registration' => $registration,
        ]);
    }

    public function cancel(string $token)
    {
        $registration = EventRegistration::where('cancel_token', $token)->first();

        if (! $registration) {
            return view('events.registration-feedback', ['state' => 'invalid_token']);
        }

        if ($registration->status !== EventRegistration::STATUS_CANCELLED) {
            $registration->cancel();
        }

        return view('events.registration-feedback', [
            'state' => 'cancelled',
            'registration' => $registration,
        ]);
    }

    public function ics(Event $event)
    {
        abort_unless($event->is_active, 404);

        $body = IcsGenerator::forEvent($event);

        return response($body, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="'.$event->slug.'.ics"',
        ]);
    }
}
