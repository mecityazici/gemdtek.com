<?php

namespace Tests\Feature;

use App\Mail\ContactMessageReceived;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactFlowTest extends TestCase
{
    public function test_valid_contact_message_queues_mail(): void
    {
        Mail::fake();

        $response = $this->post('/iletisim', [
            'name'    => 'Endüstri Temsilcisi',
            'email'   => 'temsilci@example.com',
            'subject' => 'Sponsorluk hakkında',
            'message' => 'Merhaba, sponsorluk paketleriniz hakkında bilgi almak isterim.',
        ]);

        $response->assertRedirect('/iletisim')
            ->assertSessionHas('contact_sent', true);

        Mail::assertQueued(ContactMessageReceived::class, function ($mail) {
            return $mail->name === 'Endüstri Temsilcisi'
                && $mail->email === 'temsilci@example.com'
                && $mail->messageSubject === 'Sponsorluk hakkında';
        });
    }

    public function test_contact_form_validation_fails_on_missing_fields(): void
    {
        $response = $this->from('/iletisim')->post('/iletisim', [
            'name' => 'Only Name',
        ]);

        $response->assertRedirect('/iletisim')
            ->assertSessionHasErrors(['email', 'subject', 'message']);
    }

    public function test_contact_honeypot_blocks_spam(): void
    {
        $this->withoutExceptionHandling();
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $this->post('/iletisim', [
            'website' => 'spam-payload',
            'name'    => 'X',
            'email'   => 'x@x.com',
            'subject' => 'X',
            'message' => 'X',
        ]);
    }
}
