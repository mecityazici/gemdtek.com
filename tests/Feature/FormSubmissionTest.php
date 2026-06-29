<?php

namespace Tests\Feature;

use App\Filament\Resources\FormResource\Pages\EditForm;
use App\Filament\Resources\FormResource\RelationManagers\SubmissionsRelationManager;
use App\Mail\FormSubmissionReceived;
use App\Models\Form;
use App\Models\FormSubmission;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class FormSubmissionTest extends TestCase
{
    public function test_valid_submission_creates_record_and_queues_mail(): void
    {
        Mail::fake();

        $payload = [
            'ad_soyad' => 'Test Aday',
            'email' => 'aday@example.com',
            'telefon' => '+90 555 111 22 33',
            'bolum' => 'Gemi İnşaatı',
            'sinif' => '3. sınıf',
            'ilgi_alanlari' => ['Mekanik tasarım', 'Otonom yazılım'],
            'motivasyon' => 'Test motivation text.',
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
        $this->expectException(HttpException::class);

        $this->post('/basvuru/uyelik', [
            'website' => 'http://spam.example',
            'ad_soyad' => 'Spammy',
            'email' => 'spam@example.com',
            'bolum' => 'X',
            'sinif' => '1. sınıf',
        ]);
    }

    public function test_closed_form_returns_423_on_submit(): void
    {
        $form = Form::where('slug', 'uyelik')->first();
        $form->update(['is_active' => false]);

        $this->withoutExceptionHandling();
        $this->expectException(HttpException::class);

        $this->post('/basvuru/uyelik', [
            'ad_soyad' => 'X', 'email' => 'x@x.com', 'bolum' => 'X', 'sinif' => '1. sınıf',
        ]);
    }

    public function test_uploaded_file_is_stored_privately_and_downloadable_by_admin_only(): void
    {
        Mail::fake();

        // local diski izole bir test klasörüne yönlendir (gerçek storage/app'i kirletme).
        $root = storage_path('framework/testing/disks/form-attachments');
        File::deleteDirectory($root);
        config(['filesystems.disks.local.root' => $root]);
        Storage::forgetDisk('local');

        try {
            $response = $this->post('/basvuru/ar-ge-basvuru', [
                'ad_soyad' => 'Test Aday',
                'email' => 'aday@example.com',
                'bolum' => 'Gemi İnşaatı',
                'alanlar' => ['Mekanik tasarım'],
                'seviye' => 'Orta (bağımsız çalışabilir)',
                'cv' => UploadedFile::fake()->create('benim-cv.pdf', 120, 'application/pdf'),
            ]);

            $response->assertRedirect('/basvuru/ar-ge-basvuru')
                ->assertSessionHas('submitted', true);

            $submission = FormSubmission::latest()->first();
            $this->assertNotNull($submission);
            // data[] sadece orijinal dosya adını metin olarak tutar
            $this->assertSame('benim-cv.pdf', $submission->data['cv']);

            // asıl dosya private 'local' diskte media olarak durur
            $media = $submission->attachmentFor('cv');
            $this->assertNotNull($media, 'Yüklenen dosya attachments koleksiyonunda bulunmalı');
            $this->assertSame('local', $media->disk, 'Ekler herkese açık disk yerine private local diskte olmalı');
            $this->assertSame('benim-cv.pdf', $media->file_name);
            $this->assertSame('cv', $media->getCustomProperty('field_name'));

            // misafir erişemez
            $this->get(route('forms.attachment', $media))->assertForbidden();

            // panel yetkisi olmayan kullanıcı erişemez
            $this->actingAs(User::factory()->create())
                ->get(route('forms.attachment', $media))
                ->assertForbidden();

            // admin inline olarak görüntüleyebilir
            $admin = User::where('email', 'admin@gemdtek.com')->first();
            $download = $this->actingAs($admin)->get(route('forms.attachment', $media));
            $download->assertOk();
            $this->assertStringContainsString('inline', (string) $download->headers->get('content-disposition'));
            $this->assertStringContainsString('benim-cv.pdf', (string) $download->headers->get('content-disposition'));
        } finally {
            File::deleteDirectory($root);
        }
    }

    public function test_admin_submissions_table_renders_file_attachment_link(): void
    {
        $root = storage_path('framework/testing/disks/form-attachments-ui');
        File::deleteDirectory($root);
        config(['filesystems.disks.local.root' => $root]);
        Storage::forgetDisk('local');

        try {
            $form = Form::where('slug', 'ar-ge-basvuru')->first();
            $submission = FormSubmission::create([
                'form_id' => $form->id,
                'data' => ['ad_soyad' => 'Aday', 'email' => 'a@b.com', 'cv' => 'ozgecmis.pdf'],
            ]);
            $file = UploadedFile::fake()->create('ozgecmis.pdf', 50, 'application/pdf');
            $submission->addMedia($file->getRealPath())
                ->usingFileName('ozgecmis.pdf')
                ->withCustomProperties(['field_name' => 'cv'])
                ->toMediaCollection('attachments');

            $admin = User::where('email', 'admin@gemdtek.com')->first();
            Filament::setCurrentPanel(Filament::getPanel('admin'));

            Livewire::actingAs($admin)
                ->test(SubmissionsRelationManager::class, [
                    'ownerRecord' => $form,
                    'pageClass' => EditForm::class,
                ])
                ->assertSuccessful()
                // file kolonu indirme linkini içermeli
                ->assertSee('ozgecmis.pdf')
                ->assertSee(route('forms.attachment', $submission->attachmentFor('cv')))
                // görüntüleme modalı (infolist) hatasız açılmalı
                ->mountTableAction('view', $submission)
                ->assertSuccessful();
        } finally {
            File::deleteDirectory($root);
        }
    }

    public function test_invalid_select_option_fails_validation(): void
    {
        $response = $this->from('/basvuru/uyelik')->post('/basvuru/uyelik', [
            'ad_soyad' => 'Test',
            'email' => 'test@example.com',
            'bolum' => 'Test',
            'sinif' => 'Geçersiz seçenek', // not in options
        ]);

        $response->assertRedirect('/basvuru/uyelik')
            ->assertSessionHasErrors('sinif');
    }
}
