<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;

class TestRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => 'MIS',
            'description' => 'Management Information Systems',
            'permissions' => json_encode(['manage_users', 'view_all_reports'])
        ]);
    }
}
