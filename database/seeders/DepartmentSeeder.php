<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            [
                'name' => 'Arts and Sciences Education',
                'code' => 'ASE',
                'description' => 'Department of Arts and Sciences Education'
            ],
            [
                'name' => 'School of Business Information Science Management',
                'code' => 'SBISM',
                'description' => 'Department of School of Business Information Science Management'
            ],
            [
                'name' => 'General Education',
                'code' => 'GEN_ED',
                'description' => 'Department of General Education'
            ],
            [
                'name' => 'Nursing',
                'code' => 'NURSING',
                'description' => 'Department of Nursing'
            ],
            [
                'name' => 'Allied Health',
                'code' => 'ALLIED_HEALTH',
                'description' => 'Department of Allied Health'
            ],
            [
                'name' => 'Graduate Studies',
                'code' => 'GRADUATE_STUDIES',
                'description' => 'Department of Graduate Studies'
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
