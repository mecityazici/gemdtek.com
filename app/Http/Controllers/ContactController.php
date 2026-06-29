<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageReceived;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact.show');
    }

    public function submit(Request $request)
    {
        if ($request->filled('website')) {
            abort(422, 'Spam tespit edildi.');
        }

        $data = $request->validate([
            'website' => 'prohibited',
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:160',
            'subject' => 'required|string|max:160',
            'message' => 'required|string|max:4000',
        ]);

        $to = setting('notifications.email', env('FORM_NOTIFICATION_EMAIL', config('mail.from.address')));

        Mail::to($to)
            ->queue(new ContactMessageReceived(
                name: $data['name'],
                email: $data['email'],
                messageSubject: $data['subject'],
                body: $data['message'],
                ip: (string) $request->ip(),
            ));

        return redirect()
            ->route('contact')
            ->with('contact_sent', true);
    }
}
