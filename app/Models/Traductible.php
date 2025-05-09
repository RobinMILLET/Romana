<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traductible extends Model
{
    protected $table = "traductible";
    protected $primaryKey = 'traductible_id';
    public $timestamps = false;

    public function Traduction() {
        return $this->hasMany(Traduction::class, "traductible_id", "traductible_id")->get();
    }
    public function Any() {
        return $this->Allergene() ?? $this->Categorie() ?? $this-> Element() ?? $this->Page() ?? $this->Typepresence();
    }
    public function Allergene() {
        return $this->hasOne(Allergene::class, "allergene_id")->get()->first();
    }
    public function Categorie() {
        return $this->hasOne(Categorie::class, "categorie_id")->get()->first();
    }
    public function Element() {
        return $this->hasOne(Element::class, "element_id")->get()->first();
    }
    public function Page() {
        return $this->hasOne(Page::class, "page_id")->get()->first();
    }
    public function Typepresence() {
        return $this->hasOne(Typepresence::class, "typepresence_id")->get()->first();
    }
}