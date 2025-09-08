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
        Schema::create('faculty_semester_compliances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('document_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('semester_id')->constrained()->onDelete('cascade');
            $table->text('actual_situation')->nullable();
            $table->string('evidence_link')->nullable();
            $table->enum('self_evaluation_status', ['Complied', 'Not Complied'])->default('Not Complied');
            $table->timestamps();
            
            // Ensure one record per user, document type, and semester
            $table->unique(['user_id', 'document_type_id', 'semester_id'], 'faculty_compliance_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('faculty_semester_compliances');
    }
};
