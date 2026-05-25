<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Generate Shield permissions (idempotent — skips existing rows)
        Artisan::call('shield:generate', [
            '--all' => true,
            '--option' => 'permissions',
            '--panel' => 'admin',
            '--minimal' => true,
        ]);

        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'team_captain', 'guard_name' => 'web']);

        // super_admin: tüm permissions — Shield bypass'ı + explicit grant
        // (Spatie Permission Gate::before, izin atanmamışsa false döner;
        //  Shield bypass'ı ondan sonra çalışmadığı için tüm izinleri açıkça verelim)
        $superAdmin->syncPermissions(Permission::all());

        $email = env('ADMIN_EMAIL', 'admin@gemdtek.com');
        $password = env('ADMIN_PASSWORD', 'ChangeMe!2026');

        // updateOrCreate: mevcut user'sa şifreyi de günceller; firstOrCreate sadece
        // ilk seferinde yarattığı için .env'deki ADMIN_PASSWORD değişiminin
        // hash'e yansımıyordu — bu sürüm her seed'de password'u .env ile senkron tutar.
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'GEMDTEK Admin'),
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ],
        );

        if (! $user->hasRole('super_admin')) {
            $user->assignRole($superAdmin);
        }

        $this->command->info('Admin user ready.');
        $this->command->line("  Email:    {$email}");
        $this->command->line("  Password: {$password}  (ilk girişte değiştir!)");
    }
}
