<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // 1. SaaS HQ Roles create karein
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $supportRole = Role::firstOrCreate(['name' => 'Support Staff', 'guard_name' => 'web']);

        // 2. Main Founder (Aapka) Account
        $admin = User::firstOrCreate(
            ['email' => 'vikram@saas.com'], // Aapka direct email
            [
                'name' => 'Vikram Kumar',
                'password' => Hash::make('password123'),
            ]
        );

        // 3. Assign Role
        $admin->assignRole($superAdminRole);
        
        $this->command->info('HQ Super Admin account successfully created!');
    }
}