<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/*
 * ProjectSpec.key/value ve ProjectMember.role tek-dilli (TR) yapılıyor.
 * Bu alanlar Filament Repeater ile düzenleniyordu; Spatie Translatable plugin'i
 * yalnızca ana modelin çevrilebilir alanlarını round-trip ettiği için nested
 * repeater child'ları aktif-olmayan locale'i bozuyordu. Form/FormField'la aynı
 * "admin içeriği tek dil" yaklaşımına geçiyoruz.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1) json → text (önce tip; aksi halde düz metin json kolona yazılamaz)
        Schema::table('project_specs', function (Blueprint $table) {
            $table->text('key')->change();
            $table->text('value')->change();
        });
        Schema::table('project_members', function (Blueprint $table) {
            $table->text('role')->change();
        });

        // 2) mevcut {"tr":..,"en":..} JSON'ını TR değerine düzleştir
        foreach (DB::table('project_specs')->get(['id', 'key', 'value']) as $row) {
            DB::table('project_specs')->where('id', $row->id)->update([
                'key' => $this->flatten($row->key),
                'value' => $this->flatten($row->value),
            ]);
        }
        foreach (DB::table('project_members')->get(['id', 'role']) as $row) {
            DB::table('project_members')->where('id', $row->id)->update([
                'role' => $this->flatten($row->role),
            ]);
        }
    }

    public function down(): void
    {
        foreach (DB::table('project_specs')->get(['id', 'key', 'value']) as $row) {
            DB::table('project_specs')->where('id', $row->id)->update([
                'key' => json_encode(['tr' => (string) $row->key], JSON_UNESCAPED_UNICODE),
                'value' => json_encode(['tr' => (string) $row->value], JSON_UNESCAPED_UNICODE),
            ]);
        }
        foreach (DB::table('project_members')->get(['id', 'role']) as $row) {
            DB::table('project_members')->where('id', $row->id)->update([
                'role' => json_encode(['tr' => (string) $row->role], JSON_UNESCAPED_UNICODE),
            ]);
        }

        Schema::table('project_specs', function (Blueprint $table) {
            $table->json('key')->change();
            $table->json('value')->change();
        });
        Schema::table('project_members', function (Blueprint $table) {
            $table->json('role')->change();
        });
    }

    private function flatten(?string $raw): string
    {
        if ($raw === null || $raw === '') {
            return '';
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return (string) ($decoded['tr'] ?? (reset($decoded) ?: ''));
        }

        return $raw; // zaten düz metin
    }
};
