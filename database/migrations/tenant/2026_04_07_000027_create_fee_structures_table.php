<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('academic_year_id')
                  ->constrained('academic_years')
                  ->cascadeOnDelete();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();
            $table->foreignId('fee_head_id')
                  ->constrained('fee_heads')
                  ->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->unsignedInteger('due_day')->default(10); // day of month
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            // One fee head per class per year
            $table->unique(
                ['academic_year_id', 'class_id', 'fee_head_id'],
                'unique_fee_structure'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_structures');
    }
};