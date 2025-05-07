<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    protected $table = "categorie";
    protected $primaryKey = 'categorie_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'categorie_id',
        'categorie_idparent',
        'categorie_ordre'
    ];

    public function Traductible() {
        return $this->hasOne(Traductible::class, "categorie_id")->get()->first();
    }
    public function Categorise() {
        return $this->hasMany(Categorie::class, "categorie_id", "categorie_id")->get();
    }
}