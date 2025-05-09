<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mois extends Model
{
    protected $table = "mois";
    protected $primaryKey = 'mois_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'mois_id',
        'horaire_id'
    ];

    public function Horaire(){
        return $this->hasMany(Horaire::class, "horaire_id", "horaire_id")->get()->first();
    }
}