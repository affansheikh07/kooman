<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checked_in extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'username',
        'program_name',
        'max_time',
        'time_spent',
        'time_over',
        'status'
    ];
}
