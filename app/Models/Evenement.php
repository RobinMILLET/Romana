<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evenement extends Model
{
    protected $table = "evenement";
    protected $primaryKey = 'evenement_id';
    public $timestamps = false;
    protected $fillable = [
        'evenement_libelle',
        'evenement_ordre',
        'evenement_suppression',
        'evenement_visible'
    ];

    public function Conteneur(){
        return $this->hasMany(Conteneur::class, "evenement_id", "evenement_id")->get();
    }
}