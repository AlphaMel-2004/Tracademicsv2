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
        // Recreate faculty_semester_compliances table with all updates
        Schema::create('faculty_semester_compliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->string('evidence_link')->nullable();
            $table->enum('self_evaluation_status', ['Complied', 'Not Complied'])->default('Not Complied');
            
            // Multi-level approval fields
            $table->enum('approval_status', ['draft', 'submitted', 'pending', 'approved', 'needs_revision'])->default('draft');
            $table->enum('program_head_approval_status', ['pending', 'approved', 'needs_revision'])->default('pending');
            $table->foreignId('program_head_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('program_head_approved_at')->nullable();
            $table->enum('dean_approval_status', ['pending', 'approved', 'needs_revision'])->default('pending');
            $table->foreignId('dean_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('dean_approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('comments')->nullable();
            
            $table->timestamps();
            
            // Ensure one record per user, document type, and semester
            $table->unique(['user_id', 'document_type_id', 'semester_id'], 'faculty_compliance_unique');
        });

        // Recreate subject_compliances table with all updates
        Schema::create('subject_compliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->string('evidence_link')->nullable();
            $table->enum('self_evaluation_status', ['Complied', 'Not Complied'])->default('Not Complied');
            
            // Multi-level approval fields
            $table->enum('approval_status', ['draft', 'submitted', 'pending', 'approved', 'needs_revision'])->default('draft');
            $table->enum('program_head_approval_status', ['pending', 'approved', 'needs_revision'])->default('pending');
            $table->foreignId('program_head_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('program_head_approved_at')->nullable();
            $table->enum('dean_approval_status', ['pending', 'approved', 'needs_revision'])->default('pending');
            $table->foreignId('dean_approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('dean_approved_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('comments')->nullable();
            
            $table->timestamps();
            
            // Ensure one record per user, subject, and document type
            $table->unique(['user_id', 'subject_id', 'document_type_id', 'semester_id'], 'subject_compliance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_compliances');
        Schema::dropIfExists('faculty_semester_compliances');
    }
};
