<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExamSeeder extends Seeder
{
    private const EXAMS_PER_SUBJECT = 29;

    private const EXAM_TYPES = [
        'Unit Test',
        'Quiz',
        'Midterm Examination',
        'Chapter Assessment',
        'Final Examination',
    ];

    private const TOTAL_MARKS = [50, 75, 100];

    public function run(): void
    {
        if (Exam::exists()) {
            $this->command?->warn(
                'Exams table is not empty. ExamSeeder skipped to avoid duplicates.'
            );

            return;
        }

        $subjects = Subject::orderBy('id')->get();

        if ($subjects->isEmpty()) {
            $this->command?->warn('No subjects found. Run SubjectSeeder first.');

            return;
        }

        $combos = Student::query()
            ->select('grade', 'class_section')
            ->distinct()
            ->get();

        if ($combos->isEmpty()) {
            $this->command?->warn('No students found. Run StudentSeeder first.');

            return;
        }

        $academicStart = $this->academicYearStart();
        $academicEnd = $this->academicYearStart()->addMonths(9)->endOfMonth();
        $totalDays = max(1, $academicStart->diffInDays($academicEnd));

        $now = now();
        $rows = [];

        foreach ($subjects as $subject) {
            for ($i = 0; $i < self::EXAMS_PER_SUBJECT; $i++) {
                $combo = $combos[$i % $combos->count()];

                $offset = (int) floor(($i / (self::EXAMS_PER_SUBJECT - 1)) * $totalDays)
                    + random_int(0, 10);

                $examDate = $academicStart->copy()->addDays(min($offset, $totalDays));

                $type = self::EXAM_TYPES[$i % count(self::EXAM_TYPES)];
                $typeNumber = intdiv($i, count(self::EXAM_TYPES)) + 1;

                $rows[] = [
                    'subject_id' => $subject->id,
                    'title' => "{$subject->name} {$type} {$typeNumber}",
                    'grade' => (string) $combo->grade,
                    'class_section' => $combo->class_section,
                    'exam_date' => $examDate->toDateString(),
                    'total_marks' => self::TOTAL_MARKS[$i % count(self::TOTAL_MARKS)],
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::transaction(function () use ($rows) {
            foreach (array_chunk($rows, 100) as $chunk) {
                Exam::insert($chunk);
            }
        });

        $this->command?->info('Exams seeded: ' . count($rows) . ' exams.');
    }

    private function academicYearStart(): Carbon
    {
        $today = Carbon::today();
        $year = $today->month >= 9 ? $today->year : $today->year - 1;

        return Carbon::create($year, 9, 1)->startOfDay();
    }
}
