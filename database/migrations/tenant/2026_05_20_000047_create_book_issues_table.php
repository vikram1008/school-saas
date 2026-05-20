<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->string('issue_number')->unique(); // e.g. ISS-2024-00001
            $table->foreignId('book_id')->constrained('library_books')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('library_members')->cascadeOnDelete();
            $table->unsignedBigInteger('issued_by'); // user_id of librarian/admin
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->unsignedBigInteger('returned_to')->nullable(); // user_id who accepted return
            $table->string('status')->default('issued'); // issued, returned, overdue, lost
            $table->decimal('fine_amount', 8, 2)->default(0); // accumulated fine
            $table->decimal('fine_paid', 8, 2)->default(0);
            $table->integer('fine_per_day')->default(1); // ₹ per day overdue
            $table->text('notes')->nullable();
            $table->text('condition_on_issue')->nullable(); // good, fair, poor
            $table->text('condition_on_return')->nullable();
            $table->timestamps();

            $table->index(['status']);
            $table->index(['due_date', 'status']); // for overdue queries
            $table->index(['member_id', 'status']); // per-member active issues
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
