<?php

namespace Database\Seeders;

use App\Models\GradeSubject;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    private const SUBJECTS = [
        'Mathematics' => 'Core mathematics covering algebra, geometry, and problem solving.',
        'Science' => 'Integrated science covering physics, chemistry, and biology fundamentals.',
        'English' => 'English language and literature covering reading, writing, and grammar.',
    ];

    public function run(): void
    {
        DB::transaction(function () {
            $subjects = collect(self::SUBJECTS)->map(function ($description, $name) {
                return Subject::firstOrCreate(
                    ['name' => $name],
                    ['description' => $description]
                );
            });

            foreach ($subjects as $subject) {
                foreach (range(1, 12) as $grade) {
                    GradeSubject::firstOrCreate([
                        'subject_id' => $subject->id,
                        'grade' => (string) $grade,
                    ]);
                }
            }
        });

        $this->command?->info('Subjects and grade assignments seeded.');
    }
}
