<?php

namespace Tests\Feature;

use App\Filament\Imports\AlumniImporter;
use App\Filament\Imports\SponsorImporter;
use App\Models\Alumni;
use App\Models\Sponsor;
use App\Models\User;
use Filament\Actions\Imports\Models\Import;
use Tests\TestCase;

class ImporterTest extends TestCase
{
    private function createImport(string $importerClass): Import
    {
        return Import::create([
            'user_id' => User::firstOrCreate(
                ['email' => 'importer-test@gemdtek.com'],
                ['name' => 'Importer Test', 'password' => bcrypt('test1234')]
            )->getKey(),
            'file_name' => 'test.csv',
            'file_path' => 'imports/test.csv',
            'importer' => $importerClass,
            'total_rows' => 1,
        ]);
    }

    private function makeImporter(string $class, array $data): SponsorImporter|AlumniImporter
    {
        $import = $this->createImport($class);
        $importer = new $class($import, [], []);

        $reflection = new \ReflectionProperty($importer, 'data');
        $reflection->setValue($importer, $data);

        return $importer;
    }

    public function test_sponsor_importer_creates_record_with_translations(): void
    {
        $importer = $this->makeImporter(SponsorImporter::class, [
            'name' => 'Test Tersane',
            'name_en' => 'Test Shipyard',
            'url' => 'https://example.com',
            'tier' => 'gold',
            'order' => 5,
            'is_active' => true,
        ]);

        $sponsor = $importer->resolveRecord();
        $sponsor->save();

        $this->assertInstanceOf(Sponsor::class, $sponsor);
        $this->assertSame('Test Tersane', $sponsor->getTranslation('name', 'tr'));
        $this->assertSame('Test Shipyard', $sponsor->getTranslation('name', 'en'));
        $this->assertSame('gold', $sponsor->tier);
        $this->assertSame(5, $sponsor->order);
        $this->assertTrue($sponsor->is_active);
    }

    public function test_sponsor_importer_falls_back_to_tr_when_en_missing(): void
    {
        $importer = $this->makeImporter(SponsorImporter::class, [
            'name' => 'Sadece TR Sponsor',
            'tier' => 'silver',
        ]);

        $sponsor = $importer->resolveRecord();
        $sponsor->save();

        $this->assertSame('Sadece TR Sponsor', $sponsor->getTranslation('name', 'tr'));
        $this->assertArrayNotHasKey('en', $sponsor->getTranslations('name'));
    }

    public function test_alumni_importer_creates_record_with_translatable_position(): void
    {
        $importer = $this->makeImporter(AlumniImporter::class, [
            'name' => 'Ahmet Yılmaz',
            'position' => 'Tasarım Mühendisi',
            'position_en' => 'Design Engineer',
            'graduation_year' => 2018,
            'sector' => 'tasarim-ofisi',
            'company' => 'Delta Marine',
            'city' => 'İstanbul',
            'linkedin_url' => 'https://linkedin.com/in/test',
            'order' => 1,
            'is_public' => true,
        ]);

        $alumni = $importer->resolveRecord();
        $alumni->save();

        $this->assertInstanceOf(Alumni::class, $alumni);
        $this->assertSame('Ahmet Yılmaz', $alumni->name);
        $this->assertSame('Tasarım Mühendisi', $alumni->getTranslation('position', 'tr'));
        $this->assertSame('Design Engineer', $alumni->getTranslation('position', 'en'));
        $this->assertSame(2018, $alumni->graduation_year);
        $this->assertSame('tasarim-ofisi', $alumni->sector);
        $this->assertSame('Delta Marine', $alumni->company);
        $this->assertTrue($alumni->is_public);
    }

    public function test_alumni_importer_handles_optional_fields(): void
    {
        $importer = $this->makeImporter(AlumniImporter::class, [
            'name' => 'Minimal Mezun',
            'position' => 'Mühendis',
            'sector' => 'tersane',
        ]);

        $alumni = $importer->resolveRecord();
        $alumni->save();

        $this->assertSame('Minimal Mezun', $alumni->name);
        $this->assertSame('Mühendis', $alumni->getTranslation('position', 'tr'));
        $this->assertNull($alumni->graduation_year);
        $this->assertNull($alumni->company);
        $this->assertNull($alumni->linkedin_url);
        $this->assertSame(0, $alumni->order);
        $this->assertTrue($alumni->is_public);
    }

    public function test_csv_templates_are_published(): void
    {
        $sponsorTemplate = public_path('templates/sponsors-template.csv');
        $alumniTemplate = public_path('templates/alumni-template.csv');

        $this->assertFileExists($sponsorTemplate);
        $this->assertFileExists($alumniTemplate);

        $sponsorHeader = fgetcsv(fopen($sponsorTemplate, 'r'));
        $this->assertSame(['name', 'name_en', 'url', 'tier', 'order', 'is_active'], $sponsorHeader);

        $alumniHeader = fgetcsv(fopen($alumniTemplate, 'r'));
        $this->assertSame(
            ['name', 'position', 'position_en', 'graduation_year', 'sector', 'company', 'city', 'linkedin_url', 'order', 'is_public'],
            $alumniHeader
        );
    }
}
