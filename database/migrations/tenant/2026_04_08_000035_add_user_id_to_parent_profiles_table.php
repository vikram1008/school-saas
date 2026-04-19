<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            // user_id already exists — skip it

            // Primary contact info
            $table->string('mobile')->nullable()->after('phone');
            $table->string('email')->nullable()->after('mobile');

            // Hindi bilingual
            $table->string('first_name_hi')->nullable()->after('first_name');
            $table->string('last_name_hi')->nullable()->after('last_name');
            $table->string('occupation_hi')->nullable()->after('occupation');

            // Status
            $table->boolean('is_active')->default(true)->after('id_proof_number');
        });
    }

    public function down(): void
    {
        Schema::table('parent_profiles', function (Blueprint $table) {
            $table->dropColumn([
                'mobile',
                'email',
                'first_name_hi',
                'last_name_hi',
                'occupation_hi',
                'is_active',
            ]);
        });
    }
};