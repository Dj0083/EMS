<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    // Specify the exact table name
    protected $table = 'waterrocket_competitors';
    public $timestamps = false;

    // Fields that are mass assignable
    protected $fillable = [
        'Competitor_no',
        'Name',
        'School',
        'Incharge',
        'Attempt_1',
        'Attempt_2',
        'Average',
        'Email',
        'Contact',
        'Year',
        'certificate_status',
    ];
}
