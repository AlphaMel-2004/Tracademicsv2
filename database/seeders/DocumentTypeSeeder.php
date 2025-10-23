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
                'description' => 'Faculty information sheet containing personal and professional details for the academic semester',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 15
            ],
            [
                'name' => 'TOR/Diploma',
                'description' => 'Official Transcript of Records or Diploma as proof of educational qualification',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 30
            ],
            [
                'name' => 'Certificates of Trainings Attended (past 5 years)',
                'description' => 'Certificates of completed trainings, seminars, and professional development programs from the past 5 years',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 30
            ],
            [
                'name' => 'Faculty Load',
                'description' => 'Official faculty teaching load and subject assignment for the semester',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 15
            ],
            
            // Subject-specific requirements (submit for each assigned subject)
            [
                'name' => 'Syllabus',
                'description' => 'Course syllabus including learning objectives, topics, and assessment methods (specify the subject)',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 15
            ],
            [
                'name' => 'Prelim Test Questions',
                'description' => 'Preliminary examination questions with answer key and rubrics',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 45
            ],
            [
                'name' => 'Prelim Class Record',
                'description' => 'Preliminary class record showing attendance and grade computations',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 50
            ],
            [
                'name' => 'Midterm Test Questions',
                'description' => 'Midterm examination questions with comprehensive answer key',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 75
            ],
            [
                'name' => 'Midterm Table of Specifications',
                'description' => 'Detailed table of specifications for midterm examinations',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 75
            ],
            [
                'name' => 'Midterm Class Record',
                'description' => 'Midterm class record with updated attendance and grade calculations',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 80
            ],
            [
                'name' => 'Prefinal Test Questions',
                'description' => 'Pre-final examination questions and corresponding answer key',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 105
            ],
            [
                'name' => 'Prefinal Class Record',
                'description' => 'Pre-final class record with complete attendance and grade updates',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 110
            ],
            [
                'name' => 'Final Test Questions',
                'description' => 'Comprehensive final examination questions with detailed answer key',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 135
            ],
            [
                'name' => 'Final Table of Specifications',
                'description' => 'Complete table of specifications for final examinations',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 135
            ],
            [
                'name' => 'Final Class Record',
                'description' => 'Final class record with complete semester attendance and final grades',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 140
            ],
            [
                'name' => 'Final Grading Sheet',
                'description' => 'Official final grading sheet for student grade submission',
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
