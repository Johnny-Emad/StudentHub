<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\ExamGrade;
use App\Models\Student;
use Illuminate\Database\Seeder;

class ExamGradeSeeder extends Seeder
{
    private const CHUNK_SIZE = 2000;

    public function run(): void
    {
        if (ExamGrade::exists()) {
            $this->command?->warn(
                'Exam grades table is not empty. ExamGradeSeeder skipped to avoid duplicates.'
            );

            return;
        }

        $exams = Exam::orderBy('id')->get();

        if ($exams->isEmpty()) {
            $this->command?->warn('No exams found. Run ExamSeeder first.');

            return;
        }

        $studentsByClass = Student::query()
            ->where('status', 'active')
            ->get(['id', 'grade', 'class_section'])
            ->groupBy(fn (Student $student) => $student->grade . '|' . $student->class_section);

        $now = now();
        $totalRecords = 0;

        foreach ($exams as $exam) {
            $students = $studentsByClass->get($exam->grade . '|' . $exam->class_section, collect());

            if ($students->isEmpty()) {
                continue;
            }

            $rows = [];

            foreach ($students as $student) {
                $rows[] = [
                    'exam_id' => $exam->id,
                    'student_id' => $student->id,
                    'marks_obtained' => $this->generateMarks($exam->total_marks),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
                ExamGrade::insert($chunk);
            }

            $totalRecords += count($rows);
        }

        $this->command?->info("Exam grades seeded: {$totalRecords} records.");
    }

    private function generateMarks(float $totalMarks): float
    {
        $roll = random_int(1, 100);

        if ($roll <= 15) {
            $percentage = random_int(80, 100);
        } elseif ($roll <= 70) {
            $percentage = random_int(50, 79);
        } else {
            $percentage = random_int(0, 49);
        }

        return round(($totalMarks * $percentage) / 100, 2);
    }
}
