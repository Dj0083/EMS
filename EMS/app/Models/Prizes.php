<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prizes extends Model
{


    protected $table = 'water_rocket_prizes';
    public $timestamps = false;
    // use HasFactory;
    protected $fillable = [
        'Position',
        'Prize_amount',
        'Year',
    ];
}
