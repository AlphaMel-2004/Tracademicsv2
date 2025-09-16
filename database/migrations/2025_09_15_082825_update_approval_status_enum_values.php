<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update faculty_semester_compliances table
        Schema::table('faculty_semester_compliances', function (Blueprint $table) {
            // First, update any existing 'rejected' values to 'needs_revision'
            DB::statement("UPDATE faculty_semester_compliances SET approval_status = 'needs_revision' WHERE approval_status = 'rejected'");
            
            // Drop the existing enum column and recreate it with new values
            $table->dropColumn('approval_status');
        });
        
        Schema::table('faculty_semester_compliances', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'needs_revision'])->default('pending')->after('self_evaluation_status');
        });
        
        // Update subject_compliances table
        Schema::table('subject_compliances', function (Blueprint $table) {
            // First, update any existing 'rejected' values to 'needs_revision'
            DB::statement("UPDATE subject_compliances SET approval_status = 'needs_revision' WHERE approval_status = 'rejected'");
            
            // Drop the existing enum column and recreate it with new values
            $table->dropColumn('approval_status');
        });
        
        Schema::table('subject_compliances', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'needs_revision'])->default('pending')->after('self_evaluation_status');
        });
        
        // Update compliance_submissions table
        Schema::table('compliance_submissions', function (Blueprint $table) {
            // First, update any existing 'rejected' values to 'needs_revision'  
            DB::statement("UPDATE compliance_submissions SET status = 'needs_revision' WHERE status = 'rejected'");
            
            // Drop the existing enum column and recreate it with new values
            $table->dropColumn('status');
        });
        
        Schema::table('compliance_submissions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'submitted', 'under_review', 'approved', 'needs_revision'])->default('pending')->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert faculty_semester_compliances table
        Schema::table('faculty_semester_compliances', function (Blueprint $table) {
            // First, update any existing 'needs_revision' values back to 'rejected'
            DB::statement("UPDATE faculty_semester_compliances SET approval_status = 'rejected' WHERE approval_status = 'needs_revision'");
            
            $table->dropColumn('approval_status');
        });
        
        Schema::table('faculty_semester_compliances', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('self_evaluation_status');
        });
        
        // Revert subject_compliances table
        Schema::table('subject_compliances', function (Blueprint $table) {
            DB::statement("UPDATE subject_compliances SET approval_status = 'rejected' WHERE approval_status = 'needs_revision'");
            
            $table->dropColumn('approval_status');
        });
        
        Schema::table('subject_compliances', function (Blueprint $table) {
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('self_evaluation_status');
        });
        
        // Revert compliance_submissions table  
        Schema::table('compliance_submissions', function (Blueprint $table) {
            DB::statement("UPDATE compliance_submissions SET status = 'rejected' WHERE status = 'needs_revision'");
            
            $table->dropColumn('status');
        });
        
        Schema::table('compliance_submissions', function (Blueprint $table) {
            $table->enum('status', ['pending', 'submitted', 'under_review', 'approved', 'rejected'])->default('pending')->after('id');
        });
    }
};
