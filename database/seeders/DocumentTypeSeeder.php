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
                'name' => 'Training Certificates',
                'description' => 'Certificates of completed trainings, seminars, and professional development programs',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 30
            ],
            [
                'name' => 'Faculty Load Assignment',
                'description' => 'Official faculty teaching load and subject assignment for the semester',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 15
            ],
            [
                'name' => 'Performance Evaluation Form',
                'description' => 'Faculty performance evaluation and self-assessment form',
                'submission_type' => 'semester',
                'is_required' => true,
                'due_days' => 20
            ],
            
            // Subject-specific requirements (submit for each assigned subject)
            [
                'name' => 'Course Syllabus',
                'description' => 'Detailed course syllabus including learning objectives, topics, and assessment methods',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 15
            ],
            [
                'name' => 'Course Outline',
                'description' => 'Comprehensive course outline with weekly lesson plans and activities',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 15
            ],
            [
                'name' => 'Lesson Plans',
                'description' => 'Detailed lesson plans for all scheduled class sessions',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 20
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
                'name' => 'Prelim Table of Specifications',
                'description' => 'Table of specifications for preliminary examinations',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 45
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
                'name' => 'Pre-final Test Questions',
                'description' => 'Pre-final examination questions and corresponding answer key',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 105
            ],
            [
                'name' => 'Pre-final Table of Specifications',
                'description' => 'Table of specifications for pre-final examinations',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 105
            ],
            [
                'name' => 'Pre-final Class Record',
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
            ],
            
            // Additional important documents based on actual faculty submissions
            [
                'name' => 'Attendance Record',
                'description' => 'Complete student attendance record for the entire semester',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 145
            ],
            [
                'name' => 'Student Portfolio Assessment',
                'description' => 'Assessment of student portfolios and project work',
                'submission_type' => 'subject',
                'is_required' => false,
                'due_days' => 130
            ],
            [
                'name' => 'Course Evaluation Report',
                'description' => 'End-of-semester course evaluation and improvement recommendations',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 150
            ]
        ];
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
                'description' => 'Official final grading sheet for student grade submission',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 145
            ],
            
            // Additional important documents based on actual faculty submissions
            [
                'name' => 'Attendance Record',
                'description' => 'Complete student attendance record for the entire semester',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 145
            ],
            [
                'name' => 'Student Portfolio Assessment',
                'description' => 'Assessment of student portfolios and project work',
                'submission_type' => 'subject',
                'is_required' => false,
                'due_days' => 130
            ],
            [
                'name' => 'Course Evaluation Report',
                'description' => 'End-of-semester course evaluation and improvement recommendations',
                'submission_type' => 'subject',
                'is_required' => true,
                'due_days' => 150
            ]
        ];

        foreach ($documentTypes as $documentType) {
            DocumentType::create($documentType);
        }
    }
}
