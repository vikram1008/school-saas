<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();

            // Identity
            $table->string('school_name')->default('');
            $table->string('school_name_hi')->nullable();
            $table->string('logo')->nullable();           // storage path
            $table->string('favicon')->nullable();

            // Contact
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_alt')->nullable();
            $table->string('website')->nullable();

            // Address
            $table->string('address_line1')->nullable();
            $table->string('address_line1_hi')->nullable();
            $table->string('address_line2')->nullable();
            $table->string('address_line2_hi')->nullable();
            $table->string('city')->nullable();
            $table->string('city_hi')->nullable();
            $table->string('state')->nullable();
            $table->string('state_hi')->nullable();
            $table->string('pincode')->nullable();
            $table->string('country')->default('India');

            // Academic
            $table->string('board_affiliation')->nullable(); // CBSE, RBSE, etc.
            $table->string('school_code')->nullable();
            $table->string('affiliation_number')->nullable();
            $table->string('udise_code')->nullable();

            // Branding
            $table->string('primary_color')->default('#696cff');
            $table->string('tagline')->nullable();
            $table->string('tagline_hi')->nullable();

            // Receipt / Document
            $table->text('receipt_footer_note')->nullable();
            $table->text('receipt_footer_note_hi')->nullable();
            $table->string('principal_name')->nullable();
            $table->string('principal_name_hi')->nullable();
            $table->string('principal_signature')->nullable(); // storage path

            $table->timestamps();
        });

        // Seed default row
        DB::table('school_settings')->insert([
            'school_name' => 'My School',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};