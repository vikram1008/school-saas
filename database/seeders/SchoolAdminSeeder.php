<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Read credentials stored in tenant's data column
        $name     = tenant('admin_name')     ?? 'School Admin';
        $email    = tenant('admin_email')    ?? 'admin@school.com';
        $password = tenant('admin_password') ?? 'Admin@1234';

        $userId = DB::connection('tenant')->table('users')->insertGetId([
            'name'       => $name,
            'email'      => $email,
            'password'   => Hash::make($password),
            'role'       => 'school_admin',
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::connection('tenant')->table('staff_profiles')->insert([
            'user_id'         => $userId,
            'employee_code'   => 'EMP-001',
            'first_name'      => explode(' ', $name)[0],
            'last_name'       => explode(' ', $name, 2)[1] ?? 'Admin',
            'designation'     => 'School Administrator',
            'department'      => 'Administration',
            'employment_type' => 'full_time',
            'status'          => 'active',
            'joining_date'    => now()->toDateString(),
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }
}