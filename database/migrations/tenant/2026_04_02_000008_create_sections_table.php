<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')
                  ->constrained('classes')
                  ->cascadeOnDelete();

            $table->string('name');                              // "A", "B", "C"
            $table->unsignedBigInteger('class_teacher_id')->nullable(); // FK to users
            $table->unsignedInteger('capacity')->nullable();     // max students
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['class_id', 'name']); // no duplicate section names per class
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sections');
    }
};