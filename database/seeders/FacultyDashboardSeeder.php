<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\ComplianceSubmission;
use App\Models\DocumentType;
use App\Models\Semester;
use App\Models\FacultyAssignment;
use App\Models\Subject;

class FacultyDashboardSeeder extends Seeder
{
    public function run(): void
    {
        $faculty = User::whereHas('role', function($q) {
            $q->where('name', 'Faculty');
        })->first();

        if (!$faculty) {
            return;
        }

        $currentSemester = Semester::where('is_active', true)->first();
        if (!$currentSemester) {
            return;
        }

        // Create sample compliance submissions
        $docTypes = DocumentType::take(5)->get();
        $statuses = ['pending', 'approved', 'under_review', 'rejected'];

        foreach ($docTypes as $index => $docType) {
            ComplianceSubmission::firstOrCreate([
                'user_id' => $faculty->id,
                'document_type_id' => $docType->id,
                'semester_id' => $currentSemester->id,
            ], [
                'status' => $statuses[$index % count($statuses)],
                'submitted_at' => now()->subDays(rand(1, 15)),
                'created_at' => now()->subDays(rand(1, 15)),
                'updated_at' => now()->subDays(rand(1, 5))
            ]);
        }

        // Create sample subject assignments if subjects exist
        $subjects = Subject::take(3)->get();
        foreach ($subjects as $subject) {
            FacultyAssignment::firstOrCreate([
                'user_id' => $faculty->id,
                'subject_id' => $subject->id,
                'semester_id' => $currentSemester->id,
                'program_id' => $subject->program_id,
            ]);
        }

        echo "Faculty dashboard sample data created!\n";
    }
}
