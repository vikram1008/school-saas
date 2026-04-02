<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('plan');
            $table->unsignedInteger('per_student_rate')->default(10)->after('logo'); // ₹10 or ₹20
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn('per_student_rate');
            $table->enum('plan', ['basic', 'pro', 'enterprise'])->default('basic')->after('logo');
        });
    }
};