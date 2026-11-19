<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtCompetitors extends Model
{
    // Specify the exact table name
    protected $table = 'artcom_competitors';
    public $timestamps = false;

    // Fields that are mass assignable
    protected $fillable = [
        'Competitor_no',
        'Name',
        'School',
        'Incharge',
        'Incharge',
        'Place',
        'Prize',
        'Email',
        'Contact',
        'Year',
        'certificate_status',
    ];
}
