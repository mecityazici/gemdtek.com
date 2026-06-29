<?php

namespace App\Http\Controllers;

use App\Mail\SponsorLeadReceived;
use App\Models\SponsorLead;
use App\Notifications\NewSponsorLeadNotification;
use App\Support\AdminNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SponsorLeadController extends Controller
{
    public function show()
    {
        return view('sponsor.show', [
            'tiers' => SponsorLead::TIERS,
            'kitUrl' => asset('docs/sponsorship-kit.pdf'),
        ]);
    }

    public function submit(Request $request)
    {
        if ($request->filled('website')) {
            abort(422, 'Spam tespit edildi.');
        }

        $data = $request->validate([
            'website' => 'prohibited',
            'company_name' => 'required|string|max:160',
            'contact_name' => 'required|string|max:120',
            'contact_email' => 'required|email:rfc|max:160',
            'contact_role' => 'nullable|string|max:120',
            'interest_tier' => 'nullable|in:'.implode(',', array_keys(SponsorLead::TIERS)),
            'message' => 'nullable|string|max:2000',
        ]);

        $lead = SponsorLead::create($data + [
            'ip_address' => $request->ip(),
            'source' => 'site',
        ]);

        $to = setting('notifications.email', env('FORM_NOTIFICATION_EMAIL', config('mail.from.address')));
        if ($to) {
            Mail::to($to)->queue(new SponsorLeadReceived($lead));
        }

        AdminNotifier::send(new NewSponsorLeadNotification($lead));

        return redirect()
            ->route('sponsor.show')
            ->with('sponsor_lead_sent', true);
    }
}
