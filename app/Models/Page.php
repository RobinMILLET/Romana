<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = "page";
    protected $primaryKey = 'page_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'page_id',
        'page_ordre'
    ];

    public function Traductible() {
        return $this->hasOne(Traductible::class, "page_id")->get()->first();
    }
    public function Conteneur() {
        return $this->hasMany(Conteneur::class, "page_id", "page_id")->get();
    }
}