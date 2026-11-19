<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name',
        'logo_path',
        'address',
        'phone',
        'email',
        'academic_year',
        'default_language',
    ];
}
