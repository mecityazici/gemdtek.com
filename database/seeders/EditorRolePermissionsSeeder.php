<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EditorRolePermissionsSeeder extends Seeder
{
    /**
     * Editor rolünün permission haritası.
     * Editor: içerik üreten ekip. Haber/etkinlik/timeline/alumni/newsletter
     * kampanyada tam yetki; kurumsal liste ve başvuru verilerinde sadece
     * görüntüleme; sistem ayarlarına (SiteMetric, Activity, Role, User,
     * SiteSettings) erişim yok.
     *
     * NOT: Bu seeder hiç user yaratmaz. Production'da editor kullanıcıları
     * super_admin tarafından admin panelden eklenir (Sistem → Kullanıcılar).
     * Local geliştirme için test editor: DemoContentSeeder içinde tanımlı.
     */
    public function run(): void
    {
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);

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

        $valid = Permission::whereIn('name', $names->unique())->pluck('name')->all();

        $editor->syncPermissions($valid);

        $this->command->line('Editor rolüne '.count($valid).' permission atandı.');
    }
}
