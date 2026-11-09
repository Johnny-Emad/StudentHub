<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'student_code',
    'first_name',
    'last_name',
    'email',
    'phone',
    'date_of_birth',
    'gender',
    'address',
    'grade',
    'class_section',
    'status',
])]
class Student extends Model
{
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
