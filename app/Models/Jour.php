<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jour extends Model
{
    protected $table = "jour";
    protected $primaryKey = 'jour_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'jour_id',
        'horaire_id'
    ];

    public function Horaire(){
        return $this->hasMany(Horaire::class, "horaire_id")->get()->first();
    }
}