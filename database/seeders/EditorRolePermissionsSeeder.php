<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EditorRolePermissionsSeeder extends Seeder
{
    /**
     * Editor: içerik üreten ekip. Haber/etkinlik/timeline/alumni'de tam yetki,
     * kurumsal liste ve başvuru verilerinde sadece görüntüleme,
     * sistem ayarlarına (SiteMetric, Activity, Role) erişim yok.
     */
    public function run(): void
    {
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);

        // Resource slug eşleştirmesi: Shield permission'ları like '%{slug}' formatında biter
        $fullCrud = [
            'news::post',
            'event',
            'event::registration',
            'timeline::event',
            'alumni',
            'newsletter::campaign',
        ];

        $viewOnly = [
            'project',
            'project::spec',
            'project::member',
            'sponsor',
            'team::member',
            'form',
            'form::field',
            'form::submission',
            'sponsor::lead',
        ];

        $viewAndUpdate = [
            'newsletter::subscriber',
        ];

        $names = collect();

        foreach ($fullCrud as $slug) {
            // Tüm action'lar (view, view_any, create, update, delete vb.)
            $names = $names->merge(
                Permission::query()
                    ->where('name', 'like', '%_'.$slug)
                    ->pluck('name')
            );
        }

        foreach ($viewOnly as $slug) {
            $names->push('view_'.$slug);
            $names->push('view_any_'.$slug);
        }

        foreach ($viewAndUpdate as $slug) {
            $names->push('view_'.$slug);
            $names->push('view_any_'.$slug);
            $names->push('update_'.$slug);
            $names->push('delete_'.$slug);
        }

        // Widget'lar — dashboard hepsi görünsün
        $names = $names->merge(
            Permission::query()
                ->where('name', 'like', 'widget_%')
                ->pluck('name')
        );

        // Sadece var olan permission'lar
        $valid = Permission::whereIn('name', $names->unique())->pluck('name')->all();

        $editor->syncPermissions($valid);

        $this->command->line('Editor rolüne '.count($valid).' permission atandı.');

        // Test editor kullanıcısı
        $user = User::firstOrCreate(
            ['email' => 'editor@gemdtek.com'],
            [
                'name' => 'Editor Test',
                'password' => Hash::make('Editor!2026'),
                'email_verified_at' => now(),
            ],
        );

        if (! $user->hasRole('editor')) {
            $user->assignRole('editor');
        }

        $this->command->line('Test editör: editor@gemdtek.com / Editor!2026');
    }
}
