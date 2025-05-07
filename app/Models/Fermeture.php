<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fermeture extends Model
{
    protected $table = "fermeture";
    protected $primaryKey = 'fermeture_id';
    public $timestamps = false;
    protected $fillable = [
        'fermeture_debut',
        'fermeture_fin',
        'fermeture_couverts'
    ];
}