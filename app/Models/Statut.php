<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Statut extends Model
{
    protected $table = "statut";
    protected $primaryKey = 'statut_id';
    public $timestamps = false;

    public function Reservation() {
        return $this->hasMany(Reservation::class, "statut_id", "statut_id")->get();
    }
}