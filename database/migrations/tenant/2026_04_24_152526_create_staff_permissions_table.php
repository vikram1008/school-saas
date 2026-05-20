<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Module-level permissions — each boolean grants access to a module
            $table->boolean('can_mark_student_attendance')->default(true);
            $table->boolean('can_mark_staff_attendance')->default(false);
            $table->boolean('can_view_attendance_reports')->default(false);
            $table->boolean('can_enter_marks')->default(true);
            $table->boolean('can_view_exams')->default(true);
            $table->boolean('can_view_report_cards')->default(false);
            $table->boolean('can_manage_timetable')->default(false);
            $table->boolean('can_view_timetable')->default(true);
            $table->boolean('can_post_notices')->default(false);
            $table->boolean('can_view_notices')->default(true);
            $table->boolean('can_collect_fees')->default(false);
            $table->boolean('can_view_fee_reports')->default(false);
            $table->boolean('can_view_students')->default(true);
            $table->boolean('can_view_staff')->default(false);
            $table->boolean('can_view_parents')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_permissions');
    }
};
