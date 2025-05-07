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
        'element_id',
        'categorise_prix'
    ];

    public function Categorie() {
        return $this->hasMany(Categorie::class, "categorie_id")->get()->first();
    }
    public function Element() {
        return $this->hasMany(Element::class, "element_id")->get()->first();
    }
}