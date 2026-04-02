<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->enum('billing_cycle', [
                'monthly', 'quarterly', 'half_yearly', 'yearly'
            ])->default('monthly')->after('per_student_rate');

            $table->enum('subscription_status', [
                'active', 'grace_warning', 'grace_readonly', 'suspended'
            ])->default('active')->after('billing_cycle');

            $table->date('provisioned_at')->nullable()->after('subscription_status');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn(['billing_cycle', 'subscription_status', 'provisioned_at']);
        });
    }
};