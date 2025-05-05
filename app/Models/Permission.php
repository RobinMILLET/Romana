<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = "permission";
    protected $primaryKey = 'personnel_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'permission_id',
        'personnel_id'
    ];

    public function Personnel() {
        return $this->hasOne(Personnel::class, "personnel_id")->get()->first();
    }
    public function Typepermission() {
        return $this->hasOne(Typepermission::class, "permission_id")->get()->first();
    }
}