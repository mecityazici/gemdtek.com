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

        // super_admin: tüm permissions — Shield bypass + explicit grant
        $superAdmin->syncPermissions(Permission::all());

        $email = env('ADMIN_EMAIL', 'admin@gemdtek.com');
        $existing = User::where('email', $email)->first();

        if ($existing) {
            // User var → şifreye DOKUNMA. Sadece role'ü ve verified flag'i garantile.
            // (Production'da seeder yeniden çalıştırıldığında admin'in panelden değiştirdiği
            //  şifre korunsun. Şifre sıfırlamak için Filament panel veya tinker kullanılır.)
            if (! $existing->hasRole('super_admin')) {
                $existing->assignRole($superAdmin);
            }
            if (! $existing->email_verified_at) {
                $existing->forceFill(['email_verified_at' => now()])->save();
            }

            $this->command->info('Admin user var, dokunulmadı.');
            $this->command->line("  Email:    {$email}");
            $this->command->line('  Password: (mevcut şifre korundu)');

            return;
        }

        // İlk kurulum: yeni admin yarat
        $password = env('ADMIN_PASSWORD');
        if (empty($password)) {
            $this->command->error('İlk kurulum için .env\'de ADMIN_PASSWORD ZORUNLU. Lütfen doldur ve seed\'i tekrar çalıştır.');

            return;
        }

        $user = User::create([
            'name' => env('ADMIN_NAME', 'GEMDTEK Admin'),
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($superAdmin);

        $this->command->info('Admin user oluşturuldu.');
        $this->command->line("  Email:    {$email}");
        $this->command->line("  Password: {$password}  (panelden hemen değiştir!)");
    }
}
