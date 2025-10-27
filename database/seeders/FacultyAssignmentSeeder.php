<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Program;
use App\Models\Semester;
use App\Models\FacultyAssignment;

class FacultyAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the active semester
        $activeSemester = Semester::where('is_active', true)->first();
        
        // Get all faculty users
        $facultyRole = Role::where('name', 'Faculty')->first();
        $facultyUsers = User::where('role_id', $facultyRole->id)->get();
        
        // Get all programs
        $programs = Program::all();
        
        // For each faculty user, assign them to their specific program based on their name
        foreach ($facultyUsers as $faculty) {
            // Extract program code from faculty name (e.g., "Faculty BSBA" -> "BSBA")
            $facultyName = $faculty->name;
            $programCode = str_replace('Faculty ', '', $facultyName);
            
            // Find the specific program for this faculty
            $program = $programs->where('code', $programCode)->first();
            
            if ($program && $program->department_id == $faculty->department_id) {
                $programSubjects = $program->subjects()->get();

                foreach ($programSubjects->take(2) as $subject) {
                    FacultyAssignment::firstOrCreate([
                        'user_id' => $faculty->id,
                        'subject_id' => $subject->id,
                        'semester_id' => $activeSemester->id,
                        'program_id' => $program->id,
                    ]);
                }
            }
        }
    }
}
