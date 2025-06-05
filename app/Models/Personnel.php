<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Personnel extends Model
{
    protected $table = "personnel";
    protected $primaryKey = 'personnel_id';
    public $timestamps = false;
    protected $fillable = [
        'personnel_nom',
        'personnel_mdp',
        'personnel_mdp_change'
    ];

    public function Reservation() {
        return $this->hasMany(Reservation::class, "personnel_id", "personnel_id")->get();
    }
    public function Permission() {
        return $this->hasMany(Permission::class, "personnel_id", "personnel_id")->get();
    }
    public function Historique() {
        return $this->hasMany(Historique::class, "personnel_id", "personnel_id")->get();
    }
}