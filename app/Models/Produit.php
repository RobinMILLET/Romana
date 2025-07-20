<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    protected $table = "produit";
    protected $primaryKey = 'produit_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'produit_id'
    ];

    public function Traductible() {
        return $this->hasOne(Traductible::class, "traductible_id", "produit_id")->get()->first();
    }
    public function Presence(){
        return $this->hasMany(Presence::class, "produit_id", "produit_id")->get();
    }
    public function Categorise(){
        return $this->hasMany(Categorise::class, "produit_id", "produit_id")->get();
    }
}