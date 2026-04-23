<?php

namespace Database\Factories;

use App\Models\Sentence;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Sentence>
 */
class SentenceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'text_target' => $this->faker->sentence(),
            'text_source' => $this->faker->sentence(),
            'unit_id' => Unit::factory(),
        ];
    }
}
