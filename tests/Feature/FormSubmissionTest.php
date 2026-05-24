<?php

namespace Tests\Feature;

use App\Mail\FormSubmissionReceived;
use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    public function test_valid_submission_creates_record_and_queues_mail(): void
    {
        Mail::fake();

        $payload = [
            'ad_soyad'       => 'Test Aday',
            'email'          => 'aday@example.com',
            'telefon'        => '+90 555 111 22 33',
            'bolum'          => 'Gemi İnşaatı',
            'sinif'          => '3. sınıf',
            'ilgi_alanlari'  => ['Mekanik tasarım', 'Otonom yazılım'],
            'motivasyon'     => 'Test motivation text.',
        ];

        $response = $this->post('/basvuru/uyelik', $payload);

        $response->assertRedirect('/basvuru/uyelik')
            ->assertSessionHas('submitted', true);

        $submission = FormSubmission::latest()->first();
        $this->assertNotNull($submission);
        $this->assertSame('aday@example.com', $submission->data['email']);
        $this->assertSame(['Mekanik tasarım', 'Otonom yazılım'], $submission->data['ilgi_alanlari']);

        Mail::assertQueued(FormSubmissionReceived::class);
    }

    public function test_missing_required_field_fails_validation(): void
    {
        $response = $this->from('/basvuru/uyelik')->post('/basvuru/uyelik', [
            // missing ad_soyad and email
            'bolum' => 'Gemi İnşaatı',
            'sinif' => '3. sınıf',
        ]);

        $response->assertRedirect('/basvuru/uyelik')
            ->assertSessionHasErrors(['ad_soyad', 'email']);
    }

    public function test_honeypot_field_rejects_submission(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->post('/basvuru/uyelik', [
            'website'   => 'http://spam.example',
            'ad_soyad'  => 'Spammy',
            'email'     => 'spam@example.com',
            'bolum'     => 'X',
            'sinif'     => '1. sınıf',
        ]);
    }

    public function test_closed_form_returns_423_on_submit(): void
    {
        $form = Form::where('slug', 'uyelik')->first();
        $form->update(['is_active' => false]);

        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->post('/basvuru/uyelik', [
            'ad_soyad' => 'X', 'email' => 'x@x.com', 'bolum' => 'X', 'sinif' => '1. sınıf',
        ]);
    }

    public function test_invalid_select_option_fails_validation(): void
    {
        $response = $this->from('/basvuru/uyelik')->post('/basvuru/uyelik', [
            'ad_soyad' => 'Test',
            'email'    => 'test@example.com',
            'bolum'    => 'Test',
            'sinif'    => 'Geçersiz seçenek', // not in options
        ]);

        $response->assertRedirect('/basvuru/uyelik')
            ->assertSessionHasErrors('sinif');
    }
}
