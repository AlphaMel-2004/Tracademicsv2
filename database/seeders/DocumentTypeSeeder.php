<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DocumentType;

class DocumentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $documentTypes = [
            // Semester-wide requirements (submit once per semester)
            [
                'name' => 'Information Sheet',
                'description' => 'Faculty information sheet for the semester',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 15
            ],
            [
                'name' => 'TOR/Diploma',
                'description' => 'Transcript of Records or Diploma',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 30
            ],
            [
                'name' => 'Certificates of Trainings',
                'description' => 'Training certificates and professional development documents',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 30
            ],
            [
                'name' => 'Faculty Load',
                'description' => 'Faculty teaching load for the semester',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 15
            ],
            
            // Subject-specific requirements (submit for each subject)
            [
                'name' => 'Syllabus',
                'description' => 'Subject syllabus',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 15
            ],
            [
                'name' => 'Course Outline',
                'description' => 'Detailed course outline',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 15
            ],
            [
                'name' => 'Prelim Test Questions',
                'description' => 'Preliminary examination questions',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 45
            ],
            [
                'name' => 'Prelim Class Record',
                'description' => 'Preliminary class record',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 50
            ],
            [
                'name' => 'Midterm Test Questions',
                'description' => 'Midterm examination questions',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 75
            ],
            [
                'name' => 'Midterm Table of Specifications',
                'description' => 'Midterm table of specifications',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 75
            ],
            [
                'name' => 'Midterm Class Record',
                'description' => 'Midterm class record',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 80
            ],
            [
                'name' => 'Pre-final Test Questions',
                'description' => 'Pre-final examination questions',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 105
            ],
            [
                'name' => 'Pre-final Class Record',
                'description' => 'Pre-final class record',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 110
            ],
            [
                'name' => 'Final Test Questions',
                'description' => 'Final examination questions',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 135
            ],
            [
                'name' => 'Final Table of Specifications',
                'description' => 'Final table of specifications',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 135
            ],
            [
                'name' => 'Final Class Record',
                'description' => 'Final class record',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 140
            ],
            [
                'name' => 'Final Grading Sheet',
                'description' => 'Final grading sheet',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 145
            ]
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::create($documentType);
        }
    }
}
