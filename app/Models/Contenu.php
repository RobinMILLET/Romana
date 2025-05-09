<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contenu extends Model
{
    protected $table = "contenu";
    protected $primaryKey = 'conteneur_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'conteneur_id',
        'langue_id',
        'contenu_texte'
    ];

    public function Conteneur() {
        return $this->hasOne(Conteneur::class, "conteneur_id", "conteneur_id")->get()->first();
    }
    public function Langue() {
        return $this->hasOne(Langue::class, "langue_id", "langue_id")->get()->first();
    }
}