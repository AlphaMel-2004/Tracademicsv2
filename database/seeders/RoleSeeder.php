<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'MIS',
                'description' => 'Management Information Systems - Full system administration access',
                'permissions' => [
                    'manage_users',
                    'manage_departments',
                    'manage_programs',
                    'manage_semesters',
                    'manage_document_types',
                    'view_all_reports',
                    'access_all_data'
                ]
            ],
            [
                'name' => 'VPAA',
                'description' => 'Vice President for Academic Affairs - Monitor compliance across all departments',
                'permissions' => [
                    'view_all_compliance',
                    'generate_university_reports',
                    'approve_submissions',
                    'access_historical_data'
                ]
            ],
            [
                'name' => 'Dean',
                'description' => 'Dean - Monitor compliance for specific department',
                'permissions' => [
                    'view_department_compliance',
                    'generate_department_reports',
                    'approve_department_submissions',
                    'manage_program_heads'
                ]
            ],
            [
                'name' => 'Program Head',
                'description' => 'Program Head - Assign subjects and monitor program compliance',
                'permissions' => [
                    'assign_subjects',
                    'view_program_compliance',
                    'generate_program_reports',
                    'review_faculty_submissions'
                ]
            ],
            [
                'name' => 'Faculty',
                'description' => 'Faculty Member - Submit compliance documents',
                'permissions' => [
                    'submit_documents',
                    'view_personal_compliance',
                    'track_submissions'
                ]
            ]
        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role['name'],
                'description' => $role['description'],
                'permissions' => json_encode($role['permissions'])
            ]);
        }
    }
}
