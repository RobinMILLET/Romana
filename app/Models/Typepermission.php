<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Typepermission extends Model
{
    protected $table = "typepermission";
    protected $primaryKey = 'typepermission_id';
    public $timestamps = false;

    public function Permission() {
        return $this->hasMany(Permission::class, "typepermission_id", "typepermission_id")->get();
    }
}