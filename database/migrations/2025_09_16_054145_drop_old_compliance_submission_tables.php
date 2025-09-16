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
        // Drop the old compliance system tables that have been replaced
        // by faculty_semester_compliances and subject_compliances
        Schema::dropIfExists('compliance_documents');
        Schema::dropIfExists('compliance_links'); 
        Schema::dropIfExists('compliance_submissions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This down method is intentionally empty as we don't want to recreate the old tables
        // The old compliance_submissions system has been fully replaced by the new structured system
        // If needed, the old tables can be recreated by running the original migrations
    }
};
