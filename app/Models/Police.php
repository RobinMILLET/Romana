<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Police extends Model
{
    protected $table = "police";
    protected $primaryKey = 'police_id';
    public $timestamps = false;

    public function Conteneur() {
        return $this->hasMany(Conteneur::class, "police_id", "police_id")->get();
    }
}