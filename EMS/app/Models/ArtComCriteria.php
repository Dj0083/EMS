<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtComCriteria extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'artcomcriteria';

    protected $fillable = [
        'Com_Type',
        'Path',
        'Year'
    ];
}
