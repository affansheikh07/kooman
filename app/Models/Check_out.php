<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Check_out extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 
        'program_name', 
        'username', 
        'time_spent', 
        'in_time', 
        'out_time',
        'created_at',
        'updated_at',
    ];
}
