<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conteneur extends Model
{
    protected $table = "conteneur";
    protected $primaryKey = 'conteneur_id';
    public $timestamps = false;
    protected $fillable = [
        'conteneur_libelle',
        'evenement_id',
        'page_id',
        'police_id',
        'conteneur_texte',
        'conteneur_centre',
        'conteneur_fond',
        'conteneur_hex',
        'conteneur_ligne',
        'conteneur_colonne'
    ];

    public function Page() {
        return $this->hasOne(Page::class, "page_id")->get()->first();
    }
    public function Evenement() {
        return $this->hasOne(Evenement::class, "evenement_id")->get()->first();
    }
    public function Police() {
        return $this->hasOne(Police::class, "police_id")->get()->first();
    }
    public function Galerie() {
        return $this->hasMany(Galerie::class, "conteneur_id", "conteneur_id")->get();
    }
    public function Contenu() {
        return $this->hasMany(Contenu::class, "conteneur_id", "conteneur_id")->get();
    }
}