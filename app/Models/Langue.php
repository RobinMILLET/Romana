<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Langue extends Model
{
    protected $table = "langue";
    protected $primaryKey = 'langue_id';
    public $timestamps = false;

    public function Traduction(){
        return $this->hasMany(Traduction::class, "langue_id", "langue_id")->get();
    }
    public function Contenu(){
        return $this->hasMany(Contenu::class, "langue_id", "langue_id")->get();
    }
    public function Texte(){
        return $this->hasMany(Texte::class, "langue_id", "langue_id")->get();
    }
}