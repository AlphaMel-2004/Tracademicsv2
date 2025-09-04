<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Semester;

class SemesterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $semesters = [
            [
                'name' => '1st Semester',
                'start_date' => '2025-08-01',
                'end_date' => '2025-12-15',
                'is_active' => true,
                'academic_year' => '2025-2026'
            ],
            [
                'name' => '2nd Semester',
                'start_date' => '2026-01-15',
                'end_date' => '2026-05-15',
                'is_active' => false,
                'academic_year' => '2025-2026'
            ],
            [
                'name' => 'Summer',
                'start_date' => '2026-06-01',
                'end_date' => '2026-07-31',
                'is_active' => false,
                'academic_year' => '2025-2026'
            ]
        ];

        foreach ($semesters as $semester) {
            Semester::create($semester);
        }
    }
}
