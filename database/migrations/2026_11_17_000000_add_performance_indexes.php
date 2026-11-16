<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->index('grade');
            $table->index('class_section');
            $table->index('status');
            $table->index(['grade', 'class_section']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->index('date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex(['grade']);
            $table->dropIndex(['class_section']);
            $table->dropIndex(['status']);
            $table->dropIndex(['grade', 'class_section']);
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['status']);
        });
    }
};
