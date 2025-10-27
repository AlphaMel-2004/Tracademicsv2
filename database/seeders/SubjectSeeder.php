<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Program;
use App\Models\Subject;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programPrefixes = [
            'BSIT' => 'IT',
            'BSBA' => 'BA',
            'BSPsych' => 'PSY',
            'ABTheo' => 'THEO',
            'BSHM' => 'HM',
            'BEED' => 'ELED',
            'BSED' => 'EDU',
            'BSN' => 'NUR',
            'BSPhar' => 'PHAR',
            'BSMLS' => 'MLS',
            'MAN' => 'MAN',
            'MATheo' => 'MAT',
        ];

        $generalEducationSubjects = [
            ['code' => 'GE101', 'name' => 'Understanding the Self', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE102', 'name' => 'Readings in Philippine History', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE103', 'name' => 'The Contemporary World', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE104', 'name' => 'Mathematics in the Modern World', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE105', 'name' => 'Purposive Communication', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE106', 'name' => 'Art Appreciation', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE107', 'name' => 'Science, Technology, and Society', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE108', 'name' => 'Ethics', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE109', 'name' => 'Life and Works of Rizal', 'units' => 3, 'year_level' => 2],
            ['code' => 'GE110', 'name' => 'Physical Education 1', 'units' => 2, 'year_level' => 1],
            ['code' => 'GE111', 'name' => 'Physical Education 2', 'units' => 2, 'year_level' => 1],
            ['code' => 'GE112', 'name' => 'Physical Education 3', 'units' => 2, 'year_level' => 2],
            ['code' => 'GE113', 'name' => 'Physical Education 4', 'units' => 2, 'year_level' => 2],
            ['code' => 'GE114', 'name' => 'NSTP 1', 'units' => 3, 'year_level' => 1],
            ['code' => 'GE115', 'name' => 'NSTP 2', 'units' => 3, 'year_level' => 1],
        ];

        $programs = Program::all();
        if ($programs->isEmpty()) {
            Log::warning('SubjectSeeder: No programs found. Skipping subject seeding.');
            return;
        }

        DB::transaction(function () use ($programs, $programPrefixes, $generalEducationSubjects) {
            foreach ($programs as $program) {
                $prefix = $programPrefixes[$program->code] ?? $this->generatePrefix($program->code);

                for ($i = 1; $i <= 3; $i++) {
                    $code = sprintf('%s1%02d', $prefix, $i);
                    Subject::updateOrCreate(
                        ['code' => $code],
                        [
                            'name' => sprintf('%s Specialized Subject %d', $program->name, $i),
                            'units' => 3,
                            'program_id' => $program->id,
                            'year_level' => 1,
                        ]
                    );
                }

                foreach ($generalEducationSubjects as $subjectData) {
                    $code = sprintf('%s-%s', $subjectData['code'], $program->code);
                    Subject::updateOrCreate(
                        ['code' => $code],
                        [
                            'name' => $subjectData['name'],
                            'units' => $subjectData['units'],
                            'program_id' => $program->id,
                            'year_level' => $subjectData['year_level'],
                        ]
                    );
                }
            }
        });
    }

    private function generatePrefix(string $programCode): string
    {
        $cleanCode = strtoupper(preg_replace('/[^A-Z]/', '', $programCode));
        if (strlen($cleanCode) >= 3) {
            return substr($cleanCode, -3);
        }

        return str_pad($cleanCode, 3, 'X');
    }
}
