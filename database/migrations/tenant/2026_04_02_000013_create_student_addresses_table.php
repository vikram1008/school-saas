<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_profile_id')
                  ->unique()
                  ->constrained('student_profiles')
                  ->cascadeOnDelete();

            // Permanent Address
            $table->string('perm_house_no')->nullable();
            $table->string('perm_street')->nullable();
            $table->string('perm_village_city')->nullable();
            $table->string('perm_tehsil')->nullable();
            $table->string('perm_district')->nullable();
            $table->string('perm_state')->nullable();
            $table->string('perm_pincode', 6)->nullable();

            // Correspondence Address
            $table->boolean('same_as_permanent')->default(true);
            $table->string('corr_house_no')->nullable();
            $table->string('corr_street')->nullable();
            $table->string('corr_village_city')->nullable();
            $table->string('corr_tehsil')->nullable();
            $table->string('corr_district')->nullable();
            $table->string('corr_state')->nullable();
            $table->string('corr_pincode', 6)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_addresses');
    }
};