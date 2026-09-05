<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_permissions', function (Blueprint $table) {
            $table->boolean('can_approve_student_leave')->default(false)->after('can_manage_library');
        });
    }

    public function down(): void
    {
        Schema::table('staff_permissions', function (Blueprint $table) {
            $table->dropColumn('can_approve_student_leave');
        });
    }
};
