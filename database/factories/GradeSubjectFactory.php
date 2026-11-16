<?php

namespace Database\Factories;

use App\Models\GradeSubject;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradeSubject>
 */
class GradeSubjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'subject_id' => Subject::factory(),
            'grade' => (string) fake()->numberBetween(1, 12),
        ];
    }
}
