<?php

namespace Database\Factories;

use App\Enums\QuestionType;
use App\Models\Submission;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Submission>
 */
class SubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Creates a new user if not provided
            'unit_id' => Unit::factory(), // Creates a new unit if not provided
            'type' => $this->faker->randomElement(QuestionType::cases()),
            'question_text' => $this->faker->sentence() . "?",
            'provided_answer' => $this->faker->word(),
            'correct_answer' => $this->faker->word(),
            'accuracy' => $this->faker->randomFloat(2, 0, 1), // e.g., 0.75
        ];
    }
}
