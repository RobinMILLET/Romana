<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Typepresence extends Model
{
    protected $table = "typepresence";
    protected $primaryKey = 'typepresence_id';
    public $timestamps = false;
    protected $fillable = [
        'typepresence_id',
        'typepresence_hex'
    ];

    public function Traductible() {
        return $this->hasOne(Traductible::class, "traductible_id", "typepresence_id")->get()->first();
    }
    public function Presence(){
        return $this->hasMany(Presence::class, "typepresence_id", "typepresence_id")->get();
    }
}