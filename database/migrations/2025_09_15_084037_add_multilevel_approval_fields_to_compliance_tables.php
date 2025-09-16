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
        // Add multi-level approval fields to faculty_semester_compliances
        Schema::table('faculty_semester_compliances', function (Blueprint $table) {
            // Program Head approval fields
            $table->enum('program_head_approval_status', ['pending', 'approved', 'needs_revision'])->default('pending')->after('approval_status');
            $table->unsignedBigInteger('program_head_approved_by')->nullable()->after('program_head_approval_status');
            $table->timestamp('program_head_approved_at')->nullable()->after('program_head_approved_by');
            $table->text('program_head_comments')->nullable()->after('program_head_approved_at');
            
            // Dean approval fields
            $table->enum('dean_approval_status', ['pending', 'approved', 'needs_revision'])->default('pending')->after('program_head_comments');
            $table->unsignedBigInteger('dean_approved_by')->nullable()->after('dean_approval_status');
            $table->timestamp('dean_approved_at')->nullable()->after('dean_approved_by');
            $table->text('dean_comments')->nullable()->after('dean_approved_at');
            
            // Foreign key constraints
            $table->foreign('program_head_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dean_approved_by')->references('id')->on('users')->onDelete('set null');
        });
        
        // Add multi-level approval fields to subject_compliances
        Schema::table('subject_compliances', function (Blueprint $table) {
            // Program Head approval fields
            $table->enum('program_head_approval_status', ['pending', 'approved', 'needs_revision'])->default('pending')->after('approval_status');
            $table->unsignedBigInteger('program_head_approved_by')->nullable()->after('program_head_approval_status');
            $table->timestamp('program_head_approved_at')->nullable()->after('program_head_approved_by');
            $table->text('program_head_comments')->nullable()->after('program_head_approved_at');
            
            // Dean approval fields
            $table->enum('dean_approval_status', ['pending', 'approved', 'needs_revision'])->default('pending')->after('program_head_comments');
            $table->unsignedBigInteger('dean_approved_by')->nullable()->after('dean_approval_status');
            $table->timestamp('dean_approved_at')->nullable()->after('dean_approved_by');
            $table->text('dean_comments')->nullable()->after('dean_approved_at');
            
            // Foreign key constraints
            $table->foreign('program_head_approved_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('dean_approved_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('faculty_semester_compliances', function (Blueprint $table) {
            $table->dropForeign(['program_head_approved_by']);
            $table->dropForeign(['dean_approved_by']);
            $table->dropColumn([
                'program_head_approval_status',
                'program_head_approved_by', 
                'program_head_approved_at',
                'program_head_comments',
                'dean_approval_status',
                'dean_approved_by',
                'dean_approved_at',
                'dean_comments'
            ]);
        });
        
        Schema::table('subject_compliances', function (Blueprint $table) {
            $table->dropForeign(['program_head_approved_by']);
            $table->dropForeign(['dean_approved_by']);
            $table->dropColumn([
                'program_head_approval_status',
                'program_head_approved_by',
                'program_head_approved_at', 
                'program_head_comments',
                'dean_approval_status',
                'dean_approved_by',
                'dean_approved_at',
                'dean_comments'
            ]);
        });
    }
};
