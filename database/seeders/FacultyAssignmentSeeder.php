<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Program;
use App\Models\Subject;
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
        
        // Get all subjects (if they exist)
        $subjects = Subject::all();
        
        // For each faculty user, assign them to their specific program based on their name
        foreach ($facultyUsers as $faculty) {
            // Extract program code from faculty name (e.g., "Faculty BSBA" -> "BSBA")
            $facultyName = $faculty->name;
            $programCode = str_replace('Faculty ', '', $facultyName);
            
            // Find the specific program for this faculty
            $program = $programs->where('code', $programCode)->first();
            
            if ($program && $program->department_id == $faculty->department_id) {
                // If we have subjects, assign faculty to subjects of their specific program
                if ($subjects->isNotEmpty()) {
                    $programSubjects = $subjects->where('program_id', $program->id);
                    
                    if ($programSubjects->isNotEmpty()) {
                        foreach ($programSubjects->take(2) as $subject) { // Assign to max 2 subjects per program
                            FacultyAssignment::create([
                                'user_id' => $faculty->id,
                                'subject_id' => $subject->id,
                                'semester_id' => $activeSemester->id,
                                'program_id' => $program->id,
                            ]);
                        }
                    } else {
                        // Create a subject for this program if none exist
                        $subject = Subject::firstOrCreate([
                            'code' => $program->code . '-101',
                            'name' => 'Basic ' . $program->name,
                            'program_id' => $program->id,
                        ], [
                            'units' => 3, // Default 3 units
                            'year_level' => 1, // Default first year
                        ]);
                        
                        FacultyAssignment::create([
                            'user_id' => $faculty->id,
                            'subject_id' => $subject->id,
                            'semester_id' => $activeSemester->id,
                            'program_id' => $program->id,
                        ]);
                    }
                } else {
                    // If no subjects exist, create a basic subject for this specific program
                    $subject = Subject::firstOrCreate([
                        'code' => $program->code . '-101',
                        'name' => 'Basic ' . $program->name,
                        'program_id' => $program->id,
                    ], [
                        'units' => 3, // Default 3 units
                        'year_level' => 1, // Default first year
                    ]);
                    
                    FacultyAssignment::create([
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
