<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horaire extends Model
{
    protected $table = "horaire";
    protected $primaryKey = 'horaire_id';
    public $timestamps = false;
    protected $fillable = [
        'horaire_date_debut',
        'horaire_date_fin',
        'horaire_temps_debut',
        'horaire_temps_fin',
        'horaire_couverts'
    ];

    public function Jour(){
        return $this->hasMany(Jour::class, "horaire_id", "horaire_id")->get();
    }
    public function Mois(){
        return $this->hasMany(Mois::class, "horaire_id", "horaire_id")->get();
    }
}