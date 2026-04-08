<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_profile_id')
                  ->unique()
                  ->constrained('staff_profiles')
                  ->cascadeOnDelete();

            // Permanent Address
            $table->string('perm_house_no')->nullable();
            $table->string('perm_house_no_hi')->nullable();
            $table->string('perm_street')->nullable();
            $table->string('perm_street_hi')->nullable();
            $table->string('perm_village_city')->nullable();
            $table->string('perm_village_city_hi')->nullable();
            $table->string('perm_tehsil')->nullable();
            $table->string('perm_tehsil_hi')->nullable();
            $table->string('perm_district')->nullable();
            $table->string('perm_district_hi')->nullable();
            $table->string('perm_state')->nullable();
            $table->string('perm_state_hi')->nullable();
            $table->string('perm_pincode', 6)->nullable();

            // Current Address
            $table->boolean('same_as_permanent')->default(true);
            $table->string('curr_house_no')->nullable();
            $table->string('curr_street')->nullable();
            $table->string('curr_village_city')->nullable();
            $table->string('curr_tehsil')->nullable();
            $table->string('curr_district')->nullable();
            $table->string('curr_state')->nullable();
            $table->string('curr_pincode', 6)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_addresses');
    }
};