<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Language;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'source_language_id' => Language::all()->random()->id,
            'target_language_id' => Language::all()->random()->id,
            'created_by_id' => User::role(UserRole::TEACHER->value)->get()->random()->id,
        ];
    }
}
