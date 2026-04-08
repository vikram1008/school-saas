<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_family_details', function (Blueprint $table) {
            $table->string('father_name_hi')->nullable()->after('father_name');
            $table->string('father_occupation_hi')->nullable()->after('father_occupation');
            $table->string('mother_name_hi')->nullable()->after('mother_name');
            $table->string('mother_occupation_hi')->nullable()->after('mother_occupation');
            $table->string('guardian_name_hi')->nullable()->after('guardian_name');
            $table->string('guardian_relationship_hi')->nullable()->after('guardian_relationship');
            $table->string('guardian_occupation_hi')->nullable()->after('guardian_occupation');
        });
    }

    public function down(): void
    {
        Schema::table('student_family_details', function (Blueprint $table) {
            $table->dropColumn([
                'father_name_hi',
                'father_occupation_hi',
                'mother_name_hi',
                'mother_occupation_hi',
                'guardian_name_hi',
                'guardian_relationship_hi',
                'guardian_occupation_hi',
            ]);
        });
    }
};