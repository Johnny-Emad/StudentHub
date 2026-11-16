<?php

namespace Database\Factories;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => Student::factory(),
            'date' => fake()->dateTimeBetween('2026-09-01', '2027-06-30')->format('Y-m-d'),
            'status' => fake()->randomElement(['present', 'present', 'present', 'present', 'present', 'absent']),
            'marked_by' => User::factory(),
        ];
    }
}
