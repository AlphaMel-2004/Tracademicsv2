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
        Schema::table('subject_compliances', function (Blueprint $table) {
            // Add semester_id column and foreign key constraint
            $table->foreignId('semester_id')->after('document_type_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_compliances', function (Blueprint $table) {
            // Drop the foreign key and column
            $table->dropForeign(['semester_id']);
            $table->dropColumn('semester_id');
        });
    }
};
