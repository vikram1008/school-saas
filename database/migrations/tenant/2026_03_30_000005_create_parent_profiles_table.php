<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('parent_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Personal Info
            $table->string('first_name');
            $table->string('last_name');
            $table->enum('relation', [
                'father',
                'mother',
                'guardian',
                'other',
            ])->default('father');
            $table->string('phone')->nullable();
            $table->string('alternate_phone')->nullable();
            $table->string('occupation')->nullable();
            $table->string('photo')->nullable();

            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();

            // ID Proof
            $table->string('id_proof_type')->nullable();
            $table->string('id_proof_number')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_profiles');
    }
};