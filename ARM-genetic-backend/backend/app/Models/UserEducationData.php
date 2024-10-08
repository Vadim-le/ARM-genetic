<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEducationData extends Model
{
    protected $table = 'user_education_data';

    // Define the fillable fields
    protected $fillable = [
        'user_id',
        'educational_institute',
        'educational_level',
        'specialization',
        'qualification',
        'start_year',
        'end_year',
    ];
    use HasFactory;
}
