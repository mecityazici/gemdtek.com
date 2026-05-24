<?php

namespace Tests\Feature;

use App\Mail\SponsorLeadReceived;
use App\Models\SponsorLead;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SponsorLeadFlowTest extends TestCase
{
    public function test_valid_sponsor_lead_creates_record_and_mail(): void
    {
        Mail::fake();

        $response = $this->post('/sponsor-ol', [
            'company_name' => 'Test Tersane A.Ş.',
            'contact_name' => 'Ali Yetkili',
            'contact_email' => 'ali@tersane.example',
            'contact_role' => 'CFO',
            'interest_tier' => 'gold',
            'message' => 'Altın paket detayları için bizimle görüşelim.',
        ]);

        $response->assertRedirect('/sponsor-ol')
            ->assertSessionHas('sponsor_lead_sent', true);

        $lead = SponsorLead::latest()->first();
        $this->assertSame('Test Tersane A.Ş.', $lead->company_name);
        $this->assertSame('gold', $lead->interest_tier);

        Mail::assertQueued(SponsorLeadReceived::class, function ($mail) use ($lead) {
            return $mail->lead->id === $lead->id;
        });
    }

    public function test_sponsor_lead_validation_fails_without_company(): void
    {
        $response = $this->from('/sponsor-ol')->post('/sponsor-ol', [
            'contact_name' => 'Test',
            'contact_email' => 'test@example.com',
        ]);

        $response->assertRedirect('/sponsor-ol')
            ->assertSessionHasErrors('company_name');
    }

    public function test_invalid_tier_value_rejected(): void
    {
        $response = $this->from('/sponsor-ol')->post('/sponsor-ol', [
            'company_name' => 'X',
            'contact_name' => 'X',
            'contact_email' => 'x@x.com',
            'interest_tier' => 'diamond', // not in TIERS enum
        ]);

        $response->assertRedirect('/sponsor-ol')
            ->assertSessionHasErrors('interest_tier');
    }
}
