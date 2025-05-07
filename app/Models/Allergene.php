<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergene extends Model
{
    protected $table = "allergene";
    protected $primaryKey = 'allergene_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'allergene_id'
    ];

    public function Traductible() {
        return $this->hasOne(Traductible::class, "allergene_id")->get()->first();
    }
    public function Presence(){
        return $this->hasMany(Presence::class, "allergene_id", "allergene_id")->get();
    }
}