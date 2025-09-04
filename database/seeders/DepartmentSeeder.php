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
                'name' => 'Arts and Sciences, Business Management, and Education',
                'code' => 'ASBME',
                'description' => 'Department of Arts and Sciences, Business Management, and Education'
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
            ]
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }
    }
}
