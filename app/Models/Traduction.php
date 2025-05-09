<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traduction extends Model
{
    protected $table = "traduction";
    protected $primaryKey = 'traductible_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'traductible_id',
        'langue_id',
        'traduction_libelle',
        'traduction_description'
    ];

    public function Traductible() {
        return $this->hasOne(Traductible::class, "traductible_id", "traductible_id")->get()->first();
    }
    public function Langue() {
        return $this->hasOne(Langue::class, "langue_id", "langue_id")->get()->first();
    }
}