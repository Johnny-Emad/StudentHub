<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    private const TEACHER_COUNT = 50;

    public function run(): void
    {
        $password = Hash::make(
            env('SEED_TEACHER_PASSWORD', 'password')
        );

        for ($i = 1; $i <= self::TEACHER_COUNT; $i++) {
            $number = str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            $teacher = User::updateOrCreate(
                ['email' => "teacher{$number}@studenthub.test"],
                [
                    'name' => fake()->name(),
                    'password' => $password,
                    'role' => 'teacher',
                    'status' => 'approved',
                ]
            );

            if ($teacher->email_verified_at === null) {
                $teacher->forceFill(['email_verified_at' => now()])->save();
            }
        }

        $this->command?->info('Teacher accounts seeded.');
    }
}
