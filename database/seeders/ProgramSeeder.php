<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Program;
use App\Models\Department;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get departments
        $ase = Department::where('code', 'ASE')->first();
        $sbism = Department::where('code', 'SBISM')->first();
        $nursing = Department::where('code', 'NURSING')->first();
        $alliedHealth = Department::where('code', 'ALLIED_HEALTH')->first();
        $graduateStudies = Department::where('code', 'GRADUATE_STUDIES')->first();

        $programs = [
            // ASE Programs
            [
                'name' => 'Bachelor of Science in Psychology',
                'code' => 'BSPsych',
                'department_id' => $ase->id,
                'description' => 'Psychology Program'
            ],
            [
                'name' => 'Bachelor of Arts in Theology',
                'code' => 'ABTheo',
                'department_id' => $ase->id,
                'description' => 'Theology Program'
            ],
            [
                'name' => 'Bachelor of Elementary Education',
                'code' => 'BEED',
                'department_id' => $ase->id,
                'description' => 'Elementary Education Program'
            ],
            // SBISM Programs
            [
                'name' => 'Bachelor of Science in Business Administration',
                'code' => 'BSBA',
                'department_id' => $sbism->id,
                'description' => 'Business Administration Program'
            ],
            [
                'name' => 'Bachelor of Science in Information Technology',
                'code' => 'BSIT',
                'department_id' => $sbism->id,
                'description' => 'Information Technology Program'
            ],
            [
                'name' => 'Bachelor of Science in Hospitality Management',
                'code' => 'BSHM',
                'department_id' => $sbism->id,
                'description' => 'Hospitality Management Program'
            ],
            // Nursing Programs
            [
                'name' => 'Bachelor of Science in Nursing',
                'code' => 'BSN',
                'department_id' => $nursing->id,
                'description' => 'Nursing Program'
            ],
            // Allied Health Programs
            [
                'name' => 'Bachelor of Science in Pharmacy',
                'code' => 'BSPhar',
                'department_id' => $alliedHealth->id,
                'description' => 'Pharmacy Program'
            ],
            [
                'name' => 'Bachelor of Science in Medical Laboratory Science',
                'code' => 'BSMLS',
                'department_id' => $alliedHealth->id,
                'description' => 'Medical Laboratory Science Program'
            ],
            // Graduate Studies Programs
            [
                'name' => 'Master of Arts in Nursing',
                'code' => 'MAN',
                'department_id' => $graduateStudies->id,
                'description' => 'Master of Arts in Nursing Program'
            ],
            [
                'name' => 'Master of Arts in Theology',
                'code' => 'MATheo',
                'department_id' => $graduateStudies->id,
                'description' => 'Master of Arts in Theology Program'
            ]
        ];

        foreach ($programs as $program) {
            Program::create($program);
        }
    }
}
