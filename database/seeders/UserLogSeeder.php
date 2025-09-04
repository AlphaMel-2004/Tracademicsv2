<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\UserLog;
use App\Models\User;

class UserLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        
        if ($users->isEmpty()) {
            return;
        }

        $actions = ['login', 'logout', 'create', 'update', 'delete', 'view'];
        $descriptions = [
            'login' => 'User logged in successfully',
            'logout' => 'User logged out',
            'create' => 'Created new record',
            'update' => 'Updated existing record',
            'delete' => 'Deleted record',
            'view' => 'Viewed page'
        ];

        // Create sample logs for the last 30 days
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $action = $actions[array_rand($actions)];
            
            UserLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $descriptions[$action],
                'ip_address' => fake()->ipv4(),
                'user_agent' => fake()->userAgent(),
                'data' => [
                    'sample_data' => fake()->sentence(),
                    'timestamp' => now()->format('Y-m-d H:i:s')
                ],
                'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
                'updated_at' => now()
            ]);
        }
    }
}
