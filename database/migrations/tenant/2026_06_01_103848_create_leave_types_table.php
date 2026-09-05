<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_hi')->nullable();
            $table->unsignedTinyInteger('max_days_per_year')->default(30);
            $table->boolean('requires_document')->default(false);
            $table->boolean('applicable_to_students')->default(true);
            $table->boolean('applicable_to_staff')->default(true);
            $table->boolean('is_active')->default(true);
            $table->unsignedTinyInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed default leave types
        DB::table('leave_types')->insert([
            ['name' => 'Sick Leave', 'name_hi' => 'बीमारी की छुट्टी', 'max_days_per_year' => 12, 'requires_document' => false, 'applicable_to_students' => true, 'applicable_to_staff' => true, 'is_active' => true, 'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Casual Leave', 'name_hi' => 'आकस्मिक अवकाश', 'max_days_per_year' => 10, 'requires_document' => false, 'applicable_to_students' => true, 'applicable_to_staff' => true, 'is_active' => true, 'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Medical Leave', 'name_hi' => 'चिकित्सा अवकाश', 'max_days_per_year' => 30, 'requires_document' => true, 'applicable_to_students' => true, 'applicable_to_staff' => true, 'is_active' => true, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Family Emergency', 'name_hi' => 'पारिवारिक आपातकाल', 'max_days_per_year' => 5, 'requires_document' => false, 'applicable_to_students' => true, 'applicable_to_staff' => true, 'is_active' => true, 'sort_order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Earned Leave', 'name_hi' => 'अर्जित अवकाश', 'max_days_per_year' => 30, 'requires_document' => false, 'applicable_to_students' => false, 'applicable_to_staff' => true, 'is_active' => true, 'sort_order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maternity Leave', 'name_hi' => 'मातृत्व अवकाश', 'max_days_per_year' => 180, 'requires_document' => true, 'applicable_to_students' => false, 'applicable_to_staff' => true, 'is_active' => true, 'sort_order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Study Leave', 'name_hi' => 'अध्ययन अवकाश', 'max_days_per_year' => 15, 'requires_document' => false, 'applicable_to_students' => true, 'applicable_to_staff' => false, 'is_active' => true, 'sort_order' => 7, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
