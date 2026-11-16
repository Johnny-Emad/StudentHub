<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExamGrade>
 */
class ExamGradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $exam = Exam::factory()->create();

        return [
            'exam_id' => $exam->id,
            'student_id' => Student::factory(),
            'marks_obtained' => fake()->randomFloat(2, 0, $exam->total_marks),
        ];
    }
}
