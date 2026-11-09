<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exams', function (Blueprint $table) {
            $table->id();

            $table->foreignId('subject_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('title');

            $table->string('grade');

            $table->string('class_section', 10);

            $table->date('exam_date');

            $table->decimal('total_marks', 8, 2)->default(100);

            $table->timestamps();

            $table->index(['grade', 'class_section']);
            $table->index('exam_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exams');
    }
};
