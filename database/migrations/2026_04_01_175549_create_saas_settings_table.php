<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saas_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->enum('type', ['integer', 'string', 'boolean'])->default('string');
            $table->string('group')->default('general'); // billing, access, notifications
            $table->string('label');                     // Human readable label for UI
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        $now = now();
        DB::table('saas_settings')->insert([
            [
                'key'         => 'grace_warning_days',
                'value'       => '7',
                'type'        => 'integer',
                'group'       => 'billing',
                'label'       => 'Grace Warning Period (Days)',
                'description' => 'Days after due date before entering Read-Only mode.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'grace_readonly_days',
                'value'       => '30',
                'type'        => 'integer',
                'group'       => 'billing',
                'label'       => 'Read-Only Period (Days)',
                'description' => 'Days after due date before full suspension.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'suspension_days',
                'value'       => '31',
                'type'        => 'integer',
                'group'       => 'billing',
                'label'       => 'Suspension Threshold (Days)',
                'description' => 'Days after due date to trigger full account suspension.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'default_billing_cycle',
                'value'       => 'monthly',
                'type'        => 'string',
                'group'       => 'billing',
                'label'       => 'Default Billing Cycle',
                'description' => 'Default cycle assigned to new schools on provisioning.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'saas_name',
                'value'       => 'School SaaS',
                'type'        => 'string',
                'group'       => 'general',
                'label'       => 'Platform Name',
                'description' => 'Displayed across the platform and emails.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
            [
                'key'         => 'support_email',
                'value'       => 'support@saas.com',
                'type'        => 'string',
                'group'       => 'general',
                'label'       => 'Support Email',
                'description' => 'Shown to suspended schools on the access denied page.',
                'created_at'  => $now,
                'updated_at'  => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('saas_settings');
    }
};