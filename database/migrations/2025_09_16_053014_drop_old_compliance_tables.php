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
        // Drop old compliance tables - these have been replaced by the new compliance_submissions system
        Schema::dropIfExists('faculty_semester_compliances');
        Schema::dropIfExists('subject_compliances');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This down method is intentionally empty as we don't want to recreate the old tables
        // The old table structures are complex and have been fully replaced by the new system
        // If needed, the old tables can be recreated by running the original migrations
    }
};
