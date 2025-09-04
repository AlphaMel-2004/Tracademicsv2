<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('password')->constrained()->onDelete('cascade');
            $table->foreignId('current_semester_id')->after('role_id')->nullable()->constrained('semesters')->onDelete('set null');
            $table->enum('faculty_type', ['regular', 'part_time', 'visiting'])->after('current_semester_id')->nullable();
            $table->foreignId('department_id')->after('faculty_type')->nullable()->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['current_semester_id']);
            $table->dropForeign(['department_id']);
            $table->dropColumn(['role_id', 'current_semester_id', 'faculty_type', 'department_id']);
        });
    }
};
