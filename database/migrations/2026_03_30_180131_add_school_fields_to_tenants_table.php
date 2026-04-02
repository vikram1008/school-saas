<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('school_name')->after('id');
            $table->string('email')->unique()->after('school_name');
            $table->string('phone')->nullable()->after('email');
            $table->text('address')->nullable()->after('phone');
            $table->string('logo')->nullable()->after('address');
            $table->enum('plan', ['basic', 'pro', 'enterprise'])
                  ->default('basic')->after('logo');
            $table->boolean('is_active')->default(true)->after('plan');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'school_name', 'email', 'phone',
                'address', 'logo', 'plan', 'is_active'
            ]);
        });
    }
};