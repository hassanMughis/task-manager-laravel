<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database. Creates one demo user with a
     * few tasks so you have something to look at after a fresh install.
     */
    public function run(): void
    {
        $user = User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
        ]);

        $user->tasks()->createMany([
            ['title' => 'Set up the project', 'description' => 'Clone the repo and run it locally.', 'status' => 'completed'],
            ['title' => 'Push to GitHub', 'description' => 'Create a repository and push the first commit.', 'status' => 'pending'],
            ['title' => 'Deploy to Render', 'description' => 'Follow the README deployment steps.', 'status' => 'pending', 'due_date' => now()->addWeek()],
        ]);
    }
}
