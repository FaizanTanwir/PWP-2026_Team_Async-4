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
            'source_language_id' => Language::factory(),
            'target_language_id' => Language::factory(),
            'created_by_id' => function () {
                // Try to find an existing teacher
                $teacher = User::role(UserRole::TEACHER->value)->inRandomOrder()->first();

                // If no teacher exists, create one and assign the role
                if (!$teacher) {
                    $teacher = User::factory()->create();
                    $teacher->assignRole(UserRole::TEACHER->value);
                }

                return $teacher->id;
            },
        ];
    }
}
