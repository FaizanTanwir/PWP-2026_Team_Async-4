<?php

namespace Database\Factories;

use App\Models\Attempt;
use App\Models\Sentence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attempt>
 */
class AttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'audio_url' => $this->faker->url() . '/recording.mp3',
            'score' => $this->faker->numberBetween(0, 100),
            'sentence_id' => Sentence::factory(),
            'user_id' => User::factory(),
            'created_at' => now(),
        ];
    }
}
