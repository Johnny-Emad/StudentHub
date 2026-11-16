<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grade = fake()->numberBetween(1, 12);
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'student_code' => 'STU-' . fake()->unique()->numerify('#######'),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => fake()->boolean(70)
                ? fake()->unique()->safeEmail()
                : null,
            'phone' => fake()->boolean(60)
                ? '01' . fake()->numerify('#########')
                : null,
            'date_of_birth' => fake()->dateTimeBetween(
                '-' . ($grade + 7) . ' years',
                '-' . ($grade + 4) . ' years'
            )->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female']),
            'address' => fake()->boolean(50)
                ? fake()->address()
                : null,
            'grade' => $grade,
            'class_section' => fake()->randomElement(['A', 'B']),
            'status' => fake()->randomElement(['active', 'active', 'active', 'active', 'active', 'inactive']),
            'notes' => fake()->boolean(10)
                ? fake()->randomElement([
                    'Transferred from another branch.',
                    'Requires additional academic support.',
                    'Scholarship student.',
                    'Sibling of another enrolled student.',
                    'Recently joined the school.',
                ])
                : null,
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'inactive',
        ]);
    }

    public function inGrade(int $grade): static
    {
        return $this->state(fn (array $attributes) => [
            'grade' => $grade,
        ]);
    }

    public function inSection(string $section): static
    {
        return $this->state(fn (array $attributes) => [
            'class_section' => $section,
        ]);
    }
}
