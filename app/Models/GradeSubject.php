<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'subject_id',
    'grade',
])]
class GradeSubject extends Model
{
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}
