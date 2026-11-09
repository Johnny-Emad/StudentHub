<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'description',
])]
class Subject extends Model
{
    public function gradeAssignments(): HasMany
    {
        return $this->hasMany(GradeSubject::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
