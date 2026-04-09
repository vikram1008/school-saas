<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_heads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_hi')->nullable();
            $table->enum('type', ['preset', 'custom'])->default('custom');
            $table->enum('frequency', [
                'monthly', 'quarterly', 'half_yearly', 'yearly', 'one_time'
            ])->default('monthly');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_optional')->default(false);
            $table->text('description')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // Seed preset fee heads
        $now = now();
        DB::table('fee_heads')->insert([
            ['name' => 'Tuition Fee',     'name_hi' => 'शिक्षण शुल्क',    'type' => 'preset', 'frequency' => 'monthly',     'sort_order' => 1,  'is_active' => 1, 'is_optional' => 0, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Exam Fee',        'name_hi' => 'परीक्षा शुल्क',   'type' => 'preset', 'frequency' => 'one_time',    'sort_order' => 2,  'is_active' => 1, 'is_optional' => 0, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Sports Fee',      'name_hi' => 'खेल शुल्क',       'type' => 'preset', 'frequency' => 'yearly',      'sort_order' => 3,  'is_active' => 1, 'is_optional' => 1, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Library Fee',     'name_hi' => 'पुस्तकालय शुल्क', 'type' => 'preset', 'frequency' => 'yearly',      'sort_order' => 4,  'is_active' => 1, 'is_optional' => 1, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Transport Fee',   'name_hi' => 'परिवहन शुल्क',    'type' => 'preset', 'frequency' => 'monthly',     'sort_order' => 5,  'is_active' => 1, 'is_optional' => 1, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Computer Fee',    'name_hi' => 'कंप्यूटर शुल्क',  'type' => 'preset', 'frequency' => 'monthly',     'sort_order' => 6,  'is_active' => 1, 'is_optional' => 1, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Development Fee', 'name_hi' => 'विकास शुल्क',     'type' => 'preset', 'frequency' => 'yearly',      'sort_order' => 7,  'is_active' => 1, 'is_optional' => 0, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Admission Fee',   'name_hi' => 'प्रवेश शुल्क',    'type' => 'preset', 'frequency' => 'one_time',    'sort_order' => 8,  'is_active' => 1, 'is_optional' => 0, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'Late Fine',       'name_hi' => 'विलंब शुल्क',     'type' => 'preset', 'frequency' => 'one_time',    'sort_order' => 9,  'is_active' => 1, 'is_optional' => 1, 'description' => null, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_heads');
    }
};