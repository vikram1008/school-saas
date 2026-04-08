<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_addresses', function (Blueprint $table) {
            $table->string('perm_house_no_hi')->nullable()->after('perm_house_no');
            $table->string('perm_street_hi')->nullable()->after('perm_street');
            $table->string('perm_village_city_hi')->nullable()->after('perm_village_city');
            $table->string('perm_tehsil_hi')->nullable()->after('perm_tehsil');
            $table->string('perm_district_hi')->nullable()->after('perm_district');
            $table->string('perm_state_hi')->nullable()->after('perm_state');
        });
    }

    public function down(): void
    {
        Schema::table('student_addresses', function (Blueprint $table) {
            $table->dropColumn([
                'perm_house_no_hi',
                'perm_street_hi',
                'perm_village_city_hi',
                'perm_tehsil_hi',
                'perm_district_hi',
                'perm_state_hi',
            ]);
        });
    }
};