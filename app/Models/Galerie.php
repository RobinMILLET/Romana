<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galerie extends Model
{
    protected $table = "galerie";
    protected $primaryKey = 'conteneur_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'conteneur_id',
        'photo_id'
    ];

    public function Conteneur() {
        return $this->hasOne(Conteneur::class, "conteneur_id")->get()->first();
    }
    public function Photo(){
        return $this->hasMany(Photo::class, "photo_id")->get()->first();
    }
}