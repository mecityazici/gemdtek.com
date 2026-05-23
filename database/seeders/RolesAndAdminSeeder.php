<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RolesAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'team_captain', 'guard_name' => 'web']);

        $email = env('ADMIN_EMAIL', 'admin@gemdtek.com');
        $password = env('ADMIN_PASSWORD', 'ChangeMe!2026');

        $user = User::firstOrCreate(
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
