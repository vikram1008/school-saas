<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Consolidation migration: move all school branding / identity fields from the
 * per-tenant `school_settings` table into the central `tenants` table.
 *
 * After this migration the `Tenant` model is the single source of truth for
 * every school's identity AND billing info. The `school_settings` tenant table
 * is no longer needed and is removed from the tenant migration list.
 *
 * Fields already on `tenants`: school_name, school_name_hi, email, phone,
 *   address, address_hi, logo, per_student_rate, billing_cycle,
 *   subscription_status, provisioned_at, is_active
 *
 * Fields being added here (previously only in school_settings):
 *   favicon, phone_alt, website,
 *   address_line1/2 (+_hi), city (+_hi), state (+_hi), pincode, country,
 *   board_affiliation, school_code, affiliation_number, udise_code,
 *   primary_color, tagline (+_hi),
 *   receipt_footer_note (+_hi), principal_name (+_hi), principal_signature
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            // --- Branding / Media ---
            $table->string('favicon')->nullable()->after('logo');

            // --- Contact ---
            $table->string('phone_alt')->nullable()->after('phone');
            $table->string('website')->nullable()->after('phone_alt');

            // --- Structured address (additive; 'address' column kept for compatibility) ---
            $table->string('address_line1')->nullable()->after('address');
            $table->string('address_line1_hi')->nullable()->after('address_line1');
            $table->string('address_line2')->nullable()->after('address_line1_hi');
            $table->string('address_line2_hi')->nullable()->after('address_line2');
            $table->string('city')->nullable()->after('address_line2_hi');
            $table->string('city_hi')->nullable()->after('city');
            $table->string('state')->nullable()->after('city_hi');
            $table->string('state_hi')->nullable()->after('state');
            $table->string('pincode', 10)->nullable()->after('state_hi');
            $table->string('country')->default('India')->after('pincode');

            // --- Academic / Regulatory ---
            $table->string('board_affiliation')->nullable()->after('country');
            $table->string('school_code')->nullable()->after('board_affiliation');
            $table->string('affiliation_number')->nullable()->after('school_code');
            $table->string('udise_code', 20)->nullable()->after('affiliation_number');

            // --- Branding ---
            $table->string('primary_color', 7)->default('#696cff')->after('udise_code');
            $table->string('tagline')->nullable()->after('primary_color');
            $table->string('tagline_hi')->nullable()->after('tagline');

            // --- Documents / Receipts ---
            $table->text('receipt_footer_note')->nullable()->after('tagline_hi');
            $table->text('receipt_footer_note_hi')->nullable()->after('receipt_footer_note');
            $table->string('principal_name')->nullable()->after('receipt_footer_note_hi');
            $table->string('principal_name_hi')->nullable()->after('principal_name');
            $table->string('principal_signature')->nullable()->after('principal_name_hi');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'favicon', 'phone_alt', 'website',
                'address_line1', 'address_line1_hi', 'address_line2', 'address_line2_hi',
                'city', 'city_hi', 'state', 'state_hi', 'pincode', 'country',
                'board_affiliation', 'school_code', 'affiliation_number', 'udise_code',
                'primary_color', 'tagline', 'tagline_hi',
                'receipt_footer_note', 'receipt_footer_note_hi',
                'principal_name', 'principal_name_hi', 'principal_signature',
            ]);
        });
    }
};
