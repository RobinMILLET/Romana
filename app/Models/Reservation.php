<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $table = "reservation";
    protected $primaryKey = 'reservation_id';
    public $timestamps = false;
    protected $fillable = [
        'statut_id',
        'personnel_id',
        'reservation_num',
        'reservation_nom',
        'reservation_prenom',
        'reservation_telephone',
        'reservation_personnes',
        'reservation_commentaire',
        'reservation_horaire',
        'reservation_anonymiser'
    ];

    public function Personnel() {
        return $this->hasMany(Personnel::class, "personnel_id", "personnel_id")->get()->first();
    }
    public function Statut() {
        return $this->hasOne(Statut::class, "statut_id", "statut_id")->get()->first();
    }
}