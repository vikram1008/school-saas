<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('library_books', function (Blueprint $table) {
            $table->id();
            $table->string('accession_number')->unique(); // unique library catalogue number
            $table->string('title');
            $table->string('title_hi')->nullable();
            $table->string('author')->nullable();
            $table->string('publisher')->nullable();
            $table->string('isbn')->nullable();
            $table->string('category')->default('general'); // fiction, non-fiction, reference, textbook, magazine, general
            $table->year('publication_year')->nullable();
            $table->string('edition')->nullable();
            $table->string('language')->default('english');
            $table->integer('total_copies')->default(1);
            $table->integer('available_copies')->default(1);
            $table->string('rack_location')->nullable(); // shelf/rack identifier
            $table->decimal('price', 8, 2)->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_reference_only')->default(false); // can't be issued
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('library_books');
    }
};
