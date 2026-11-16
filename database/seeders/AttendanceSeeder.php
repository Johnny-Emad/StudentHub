<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    private const CHUNK_SIZE = 2000;
    private const ABSENT_CHANCE = 8;

    public function run(): void
    {
        if (Attendance::exists()) {
            $this->command?->warn(
                'Attendances table is not empty. AttendanceSeeder skipped to avoid duplicates.'
            );

            return;
        }

        $studentIds = Student::query()
            ->where('status', 'active')
            ->pluck('id')
            ->all();

        if (empty($studentIds)) {
            $this->command?->warn('No active students found. Run StudentSeeder first.');

            return;
        }

        $markerIds = User::query()
            ->where('role', 'teacher')
            ->orWhere('role', 'admin')
            ->pluck('id')
            ->all();

        $now = now();
        $totalRecords = 0;
        $days = 0;

        foreach ($this->schoolDays() as $date) {
            $rows = [];

            foreach ($studentIds as $studentId) {
                $rows[] = [
                    'student_id' => $studentId,
                    'date' => $date,
                    'status' => random_int(1, 100) <= self::ABSENT_CHANCE
                        ? 'absent'
                        : 'present',
                    'marked_by' => !empty($markerIds)
                        ? $markerIds[array_rand($markerIds)]
                        : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            foreach (array_chunk($rows, self::CHUNK_SIZE) as $chunk) {
                Attendance::insert($chunk);
            }

            $totalRecords += count($rows);
            $days++;

            if ($days % 20 === 0) {
                $this->command?->info(
                    "Seeded attendance for {$days} school days ({$totalRecords} records)."
                );
            }
        }

        $this->command?->info(
            "Attendance seeded: {$totalRecords} records across {$days} school days."
        );
    }

    /**
     * Weekdays (Mon-Fri) of the current academic year (Sep 1 - Jun 30).
     *
     * @return array<int, string>
     */
    private function schoolDays(): array
    {
        $start = $this->academicYearStart();
        $end = $start->copy()->addMonths(9)->endOfMonth();
        $today = Carbon::today();

        if ($end->gt($today)) {
            $end = $today->copy();
        }

        $days = [];
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            if ($cursor->isWeekday()) {
                $days[] = $cursor->toDateString();
            }

            $cursor->addDay();
        }

        return $days;
    }

    private function academicYearStart(): Carbon
    {
        $today = Carbon::today();
        $year = $today->month >= 9 ? $today->year : $today->year - 1;

        return Carbon::create($year, 9, 1)->startOfDay();
    }
}
