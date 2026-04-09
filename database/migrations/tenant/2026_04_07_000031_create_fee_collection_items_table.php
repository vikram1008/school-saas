<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_collection_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_collection_id')
                  ->constrained('fee_collections')
                  ->cascadeOnDelete();
            $table->foreignId('fee_demand_id')
                  ->constrained('fee_demands')
                  ->cascadeOnDelete();
            $table->decimal('amount_paid', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_collection_items');
    }
};