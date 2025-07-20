<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorise extends Model
{
    protected $table = "categorise";
    protected $primaryKey = 'categorie_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'categorie_id',
        'produit_id',
        'categorise_prix',
        'categorise_ordre'
    ];

    public function Categorie() {
        return $this->hasMany(Categorie::class, "categorie_id", "categorie_id")->get()->first();
    }
    public function Produit() {
        return $this->hasMany(Produit::class, "produit_id", "produit_id")->get()->first();
    }
}