<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Texte extends Model
{
    protected $table = "texte";
    protected $primaryKey = 'photo_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'langue_id',
        'photo_id',
        'texte_description'
    ];

    public function Photo() {
        return $this->hasOne(Photo::class, "photo_id")->get()->first();
    }
    public function Langue() {
        return $this->hasOne(Langue::class, "langue_id")->get()->first();
    }
}