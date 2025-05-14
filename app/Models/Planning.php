<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Planning extends Model
{
    protected $table = "planning";
    protected $primaryKey = 'planning_debut';
    protected $keyType = 'string';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'planning_debut',
        'planning_fin',
        'planning_couverts'
    ];
}