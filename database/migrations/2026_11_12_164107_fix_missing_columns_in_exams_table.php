
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table) {

            if (!Schema::hasColumn('exams', 'subject_id')) {
                $table->foreignId('subject_id')
                    ->nullable()
                    ->constrained('subjects')
                    ->cascadeOnDelete();
            }

            if (!Schema::hasColumn('exams', 'title')) {
                $table->string('title')->nullable();
            }

            if (!Schema::hasColumn('exams', 'grade')) {
                $table->string('grade')->nullable();
            }

            if (!Schema::hasColumn('exams', 'class_section')) {
                $table->string('class_section', 10)->nullable();
            }

            if (!Schema::hasColumn('exams', 'exam_date')) {
                $table->date('exam_date')->nullable();
            }

            if (!Schema::hasColumn('exams', 'total_marks')) {
                $table->decimal('total_marks', 8, 2)->default(100);
            }
        });

        if (!Schema::hasIndex('exams', ['grade', 'class_section'])) {
            Schema::table('exams', function (Blueprint $table) {
                $table->index(['grade', 'class_section']);
            });
        }

        if (!Schema::hasIndex('exams', ['exam_date'])) {
            Schema::table('exams', function (Blueprint $table) {
                $table->index('exam_date');
            });
        }
    }

    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['subject_id']);
            $table->dropIndex(['grade', 'class_section']);
            $table->dropIndex(['exam_date']);

            $table->dropColumn([
                'subject_id',
                'title',
                'grade',
                'class_section',
                'exam_date',
                'total_marks',
            ]);
        });
    }
};
