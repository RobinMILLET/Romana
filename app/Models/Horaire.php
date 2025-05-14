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

    public function listeMois(){
        $mois = $this->Mois();
        // Si il n'y a pas de mois affectés, on les marque tous à True
        if ($mois->count() == 0) return array_fill(0, 12, true);
        // Chaque élément du tableau est un booléen qui indique si le mois est affecté
        $array = array_map(fn($x) => boolval($mois->find($x)), range(1,12));
        return $array;
    }
    public function listeJours(){
        $jours = $this->Jour();
        // Si il n'y a pas de jours affectés, on les marque tous à True
        if ($jours->count() == 0) return array_fill(0, 7, true);
        // Chaque élément du tableau est un booléen qui indique si le jour est affecté
        $array = array_map(fn($x) => boolval($jours->find($x)), range(1,7));
        return $array;
    }
}