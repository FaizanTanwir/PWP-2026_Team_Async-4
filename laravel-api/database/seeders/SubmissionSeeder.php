<?php

namespace Database\Seeders;

use App\Models\Submission;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubmissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $units = Unit::all();

        if ($users->isEmpty() || $units->isEmpty()) {
            $this->command->warn("Users or Units missing. Skipping SubmissionSeeder.");
            return;
        }

        // Create 5 submissions for each user in random units
        foreach ($users as $user) {
            Submission::factory()->count(5)->create([
                'user_id' => $user->id,
                'unit_id' => $units->random()->id,
            ]);
        }

        $this->command->info("Created submissions for all users.");
    }
}
