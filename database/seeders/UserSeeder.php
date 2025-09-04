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
        $asbme = Department::where('code', 'ASBME')->first();
        $nursing = Department::where('code', 'NURSING')->first();
        $alliedHealth = Department::where('code', 'ALLIED_HEALTH')->first();

        // Get active semester
        $activeSemester = Semester::where('is_active', true)->first();

        // Get programs for faculty assignments
        $programs = Program::all();

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
            // Dean for ASBME
            [
                'name' => 'Dr. John Dela Cruz',
                'email' => 'dean.asbme@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $deanRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $asbme->id
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
            ]
        ];

        // Create admin users first
        foreach ($users as $userData) {
            User::create($userData);
        }

        // Create faculty users for each program
        foreach ($programs as $program) {
            $facultyUser = User::create([
                'name' => 'Faculty ' . $program->code,
                'email' => strtolower($program->code) . '.faculty@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $facultyRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $program->department_id
            ]);

            // Also create a program head for each program
            $programHeadUser = User::create([
                'name' => 'Program Head ' . $program->code,
                'email' => strtolower($program->code) . '.head@brokenshire.edu.ph',
                'password' => Hash::make('password'),
                'role_id' => $programHeadRole->id,
                'current_semester_id' => $activeSemester->id,
                'faculty_type' => 'regular',
                'department_id' => $program->department_id
            ]);
        }
    }
}
