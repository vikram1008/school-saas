<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grade_scales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->string('grade');             // A1, A2, B1...
            $table->decimal('min_percentage', 5, 2);
            $table->decimal('max_percentage', 5, 2);
            $table->decimal('grade_point', 4, 2)->default(0);
            $table->string('description')->nullable();       // Outstanding
            $table->string('description_hi')->nullable();    // उत्कृष्ट
            $table->string('color')->nullable();             // success, warning, danger
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grade_scales');
    }
};