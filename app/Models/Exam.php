<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'subject_id',
    'title',
    'grade',
    'class_section',
    'exam_date',
    'total_marks',
])]
class Exam extends Model
{
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
