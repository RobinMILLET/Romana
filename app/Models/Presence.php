<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    protected $table = "presence";
    protected $primaryKey = 'element_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'allergene_id',
        'element_id',
        'typepresence_id'
    ];

    public function Allergene() {
        return $this->hasOne(Allergene::class, "allergene_id", "allergene_id")->get()->first();
    }
    public function Element() {
        return $this->hasOne(Element::class, "element_id", "element_id")->get()->first();
    }
    public function Typepresence(){
        return $this->hasMany(Typepresence::class, "typepresence_id", "typepresence_id")->get()->first();
    }
}