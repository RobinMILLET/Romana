<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    protected $table = "photo";
    protected $primaryKey = 'photo_id';
    public $timestamps = false;
    protected $fillable = [
        'photo_libelle',
        'photo_url'
    ];

    public function Conteneur() {
        return $this->hasMany(Conteneur::class, "photo_id", "photo_id")->get();
    }
}