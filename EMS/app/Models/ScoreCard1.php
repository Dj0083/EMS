<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreCard1 extends Model
{
    use HasFactory;
    protected $table = 'waterrocket_competitors';
    public $timestamps = false;
}
