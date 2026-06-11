<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Estudiante Demo',
            'email' => 'demo@taskmanager.com',
            'password' => bcrypt('password'),
        ]);

        Task::factory(5)->create([
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        Task::factory(3)->create([
            'user_id' => $user->id,
            'status' => 'in_progress',
        ]);

        Task::factory(2)->create([
            'user_id' => $user->id,
            'status' => 'completed',
        ]);
    }
}
