<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            // Bilingual
            $table->string('first_name_hi')->nullable()->after('first_name');
            $table->string('last_name_hi')->nullable()->after('last_name');
            $table->string('designation_hi')->nullable()->after('designation');
            $table->string('department_hi')->nullable()->after('department');

            // Staff type
            $table->enum('staff_type', [
                'teaching',
                'non_teaching',
                'administrative',
            ])->default('teaching')->after('user_id');

            // Identity
            $table->string('aadhaar_number', 12)->nullable()->after('id_proof_number');
            $table->string('pan_number', 10)->nullable()->after('aadhaar_number');

            // Contact
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('email')->nullable()->after('whatsapp');

            // Qualification
            $table->string('qualification')->nullable()->after('email');
            $table->string('qualification_hi')->nullable()->after('qualification');
            $table->unsignedInteger('experience_years')->default(0)->after('qualification_hi');

            // Emergency Contact
            $table->string('emergency_contact_name')->nullable()->after('experience_years');
            $table->string('emergency_contact_phone')->nullable()->after('emergency_contact_name');

            // Drop old address columns (will use staff_addresses table)
            $table->dropColumn(['address', 'city', 'state', 'pincode']);
        });
    }

    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'first_name_hi', 'last_name_hi',
                'designation_hi', 'department_hi',
                'staff_type', 'aadhaar_number', 'pan_number',
                'whatsapp', 'email', 'qualification',
                'qualification_hi', 'experience_years',
                'emergency_contact_name', 'emergency_contact_phone',
            ]);
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
        });
    }
};