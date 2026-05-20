<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_permissions', function (Blueprint $table) {
            $table->boolean('can_manage_library')->default(false)->after('can_view_parents');
        });

        // Set can_manage_library = true for existing librarian users
        DB::table('staff_permissions')
            ->whereIn('user_id', function ($query) {
                $query->select('id')->from('users')->where('role', 'librarian');
            })
            ->update(['can_manage_library' => true]);
    }

    public function down(): void
    {
        Schema::table('staff_permissions', function (Blueprint $table) {
            $table->dropColumn('can_manage_library');
        });
    }
};
