<?php

namespace App\Http\Controllers;

use App\Mail\NewsletterConfirmation;
use App\Models\NewsletterSubscriber;
use App\Notifications\NewNewsletterSubscriberNotification;
use App\Support\AdminNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function show()
    {
        return view('newsletter.show');
    }

    public function subscribe(Request $request)
    {
        if ($request->filled('website')) {
            abort(422, 'Spam tespit edildi.');
        }

        $data = $request->validate([
            'website' => 'prohibited',
            'email' => 'required|email:rfc|max:160',
            'name' => 'nullable|string|max:120',
            'source' => 'nullable|string|max:80',
        ]);

        $locale = app()->getLocale();
        $existing = NewsletterSubscriber::where('email', $data['email'])->first();

        if ($existing && $existing->status === NewsletterSubscriber::STATUS_CONFIRMED) {
            return back()->with('newsletter_status', 'already_confirmed');
        }

        $subscriber = $existing ?: new NewsletterSubscriber;
        $subscriber->fill([
            'email' => $data['email'],
            'name' => $data['name'] ?? $subscriber->name,
            'locale' => $locale,
            'status' => NewsletterSubscriber::STATUS_PENDING,
            'source' => $data['source'] ?? $subscriber->source ?? 'site',
            'confirmed_at' => null,
            'unsubscribed_at' => null,
        ]);

        if ($existing) {
            $subscriber->confirm_token = Str::random(48);
        }

        $subscriber->save();

        Mail::to($subscriber->email)->queue(new NewsletterConfirmation($subscriber));

        return back()->with('newsletter_status', 'pending');
    }

    public function confirm(string $token)
    {
        $subscriber = NewsletterSubscriber::where('confirm_token', $token)->first();

        if (! $subscriber) {
            return view('newsletter.feedback', ['state' => 'invalid_token']);
        }

        if ($subscriber->status !== NewsletterSubscriber::STATUS_CONFIRMED) {
            $subscriber->confirm();
            AdminNotifier::send(new NewNewsletterSubscriberNotification($subscriber));
        }

        return view('newsletter.feedback', ['state' => 'confirmed', 'subscriber' => $subscriber]);
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->first();

        if (! $subscriber) {
            return view('newsletter.feedback', ['state' => 'invalid_token']);
        }

        if ($subscriber->status !== NewsletterSubscriber::STATUS_UNSUBSCRIBED) {
            $subscriber->unsubscribe();
        }

        return view('newsletter.feedback', ['state' => 'unsubscribed', 'subscriber' => $subscriber]);
    }
}
