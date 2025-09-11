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
            $table->dropColumn('actual_situation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_compliances', function (Blueprint $table) {
            $table->text('actual_situation')->nullable()->after('document_type_id');
        });
    }
};
