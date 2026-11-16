<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name',
    'description',
])]
class Subject extends Model
{use HasFactory;

    
    public function gradeAssignments(): HasMany
    {
        return $this->hasMany(GradeSubject::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }
}
