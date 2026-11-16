<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Exam>
 */
class ExamFactory extends Factory
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
            'title' => fake()->randomElement([
                'Unit Test',
                'Quiz',
                'Midterm Examination',
                'Chapter Assessment',
                'Final Examination',
            ]) . ' ' . fake()->numberBetween(1, 4),
            'grade' => (string) fake()->numberBetween(1, 12),
            'class_section' => fake()->randomElement(['A', 'B']),
            'exam_date' => fake()->dateTimeBetween('2026-09-01', '2027-06-30')->format('Y-m-d'),
            'total_marks' => fake()->randomElement([50, 75, 100]),
        ];
    }
}
