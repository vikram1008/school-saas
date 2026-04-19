<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_hi')->nullable();
            $table->longText('content');
            $table->longText('content_hi')->nullable();
            $table->enum('visible_to', [
                'all', 'parents', 'staff', 'students'
            ])->default('all');
            $table->unsignedBigInteger('published_by');
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notices');
    }
};