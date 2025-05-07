<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Historique extends Model
{
    protected $table = "historique";
    protected $primaryKey = 'historique_id';
    public $timestamps = false;
    protected $fillable = [
        'personnel_id',
        'any_id',
        'historique_message'
    ];

    public function Personnel() {
        return $this->hasOne(Personnel::class, "personnel_id")->get()->first();
    }}