<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'full_name',
        'username',
        'email',
        'password',
        'status',
        'admin_id',
    ];

    protected $hidden = [
        'password',
    ];

}
