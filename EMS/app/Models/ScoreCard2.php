<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreCard2 extends Model
{
    use HasFactory;

    protected $table = 'artcom_competitors'; // access the correct table
    public $timestamps = false;
}
