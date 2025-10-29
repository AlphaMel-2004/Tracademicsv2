<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use App\Models\Program;
use App\Models\Semester;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get roles
        $misRole = Role::where('name', 'MIS')->first();
        $vpaaRole = Role::where('name', 'VPAA')->first();
        $deanRole = Role::where('name', 'Dean')->first();
        $programHeadRole = Role::where('name', 'Program Head')->first();
        $facultyRole = Role::where('name', 'Faculty')->first();

        // Get departments
        $ase = Department::where('code', 'ASE')->first();
        $sbism = Department::where('code', 'SBISM')->first();
        $genEd = Department::where('code', 'GEN_ED')->first();
        $nursing = Department::where('code', 'NURSING')->first();
        $alliedHealth = Department::where('code', 'ALLIED_HEALTH')->first();
        $graduateStudies = Department::where('code', 'GRADUATE_STUDIES')->first();

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();

        // Get programs for faculty assignments
        $programs = Program::all();
    $geProgram = Program::where('code', 'GE')->first();

        $users = [
            // MIS User
            [
                'name' => 'MIS Administrator',
                'email' => 'mis@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $misRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => null,
                'department_id' => null
            ],
            // VPAA User
            [
                'name' => 'Dr. Maria Santos',
                'email' => 'vpaa@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $vpaaRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => null,
                'department_id' => null
            ],
            // Dean for ASE
            [
                'name' => 'Dr. John Dela Cruz',
                'email' => 'dean.ase@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $deanRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $ase->id
            ],
            // Dean for SBISM
            [
                'name' => 'Dr. Michael Tan',
                'email' => 'dean.sbism@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $deanRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $sbism->id
            ],
            // Dean for General Education
            [
                'name' => 'Dr. Grace Mendoza',
                'email' => 'dean.gened@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $deanRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $genEd->id
            ],
            // Dean for Nursing
            [
                'name' => 'Dr. Anna Reyes',
                'email' => 'dean.nursing@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $deanRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $nursing->id
            ],
            // Dean for Allied Health
            [
                'name' => 'Dr. Robert Garcia',
                'email' => 'dean.alliedhealth@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $deanRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $alliedHealth->id
            ],
            // Dean for Graduate Studies
            [
                'name' => 'Dr. Patricia Villanueva',
                'email' => 'dean.graduatestudies@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $deanRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $graduateStudies->id
            ]
        ];

        // Create admin users first (use updateOrCreate to be idempotent)
        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        // Create faculty users for each program (idempotent)
        foreach ($programs as $program) {
            User::updateOrCreate(
                ['email' => strtolower($program->code) . '.faculty@brokenshire.edu.ph'],
                [
                    'name' => 'Faculty ' . $program->code,
                    'email' => strtolower($program->code) . '.faculty@brokenshire.edu.ph',
                    'password' => Hash::make('password'),
                    'role_id' => $facultyRole->id,
                    'current_semester_id' => $activeSemester->id,
                    'faculty_type' => 'regular',
                    'department_id' => $program->department_id
                ]
            );

            // Also create a program head for each program
            User::updateOrCreate(
                ['email' => strtolower($program->code) . '.head@brokenshire.edu.ph'],
                [
                    'name' => 'Program Head ' . $program->code,
                    'email' => strtolower($program->code) . '.head@brokenshire.edu.ph',
                    'password' => Hash::make('password'),
                    'role_id' => $programHeadRole->id,
                    'current_semester_id' => $activeSemester->id,
                    'faculty_type' => 'regular',
                    'department_id' => $program->department_id,
                    'program_id' => $program->id
                ]
            );
        }

        // Create 5 General Education faculty users with no assigned subjects by default
        if ($genEd && $geProgram) {
            for ($i = 1; $i <= 5; $i++) {
                User::updateOrCreate(
                    ['email' => 'ge.faculty' . $i . '@brokenshire.edu.ph'],
                    [
                        'name' => 'GE Faculty ' . $i,
                        'email' => 'ge.faculty' . $i . '@brokenshire.edu.ph',
                        'password' => Hash::make('password'),
                        'role_id' => $facultyRole->id,
                        'current_semester_id' => $activeSemester->id,
                        'faculty_type' => 'regular',
                        'department_id' => $genEd->id,
                        'program_id' => $geProgram->id
                    ]
                );
            }
        } else {
            // If departments/programs aren't present yet, log a warning so deployer knows to reseed in order
            // (No Log import available here; keep consistent with other seeders' behavior)
        }

        // Ensure Program Head for GE exists (create if missing)
        if (isset($geProgram) && $geProgram) {
            $existingGeHead = User::where('program_id', $geProgram->id)->where('role_id', $programHeadRole->id)->first();
            if (! $existingGeHead) {
                User::create([
                    'name' => 'Program Head GE',
                    'email' => 'ge.head@brokenshire.edu.ph',
                    'password' => Hash::make('password'),
                    'role_id' => $programHeadRole->id,
                    'current_semester_id' => $activeSemester->id,
                    'faculty_type' => 'regular',
                    'department_id' => $genEd ? $genEd->id : null,
                    'program_id' => $geProgram->id
                ]);
            }
        }
    }
}
