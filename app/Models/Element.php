<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Element extends Model
{
    protected $table = "element";
    protected $primaryKey = 'element_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'element_id'
    ];

    public function Traductible() {
        return $this->hasOne(Traductible::class, "traductible_id", "element_id")->get()->first();
    }
    public function Presence(){
        return $this->hasMany(Presence::class, "element_id", "element_id")->get();
    }
    public function Categorise(){
        return $this->hasMany(Categorise::class, "element_id", "element_id")->get();
    }
}