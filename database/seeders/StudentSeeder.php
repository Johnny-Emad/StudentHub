<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    private const STUDENT_COUNT = 10000;
    private const CHUNK_SIZE = 1000;

    public function run(): void
    {
        if (Student::exists()) {
            $this->command?->warn(
                'Students table is not empty. StudentSeeder skipped to avoid duplicates.'
            );

            return;
        }

        $remaining = self::STUDENT_COUNT;

        while ($remaining > 0) {
            $count = min(self::CHUNK_SIZE, $remaining);

            Student::factory()
                ->count($count)
                ->create();

            $remaining -= $count;

            $this->command?->info(
                'Seeded ' . (self::STUDENT_COUNT - $remaining) . ' / ' . self::STUDENT_COUNT . ' students.'
            );
        }

        $this->command?->info('Students seeded.');
    }
}
