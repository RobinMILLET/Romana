<?php

namespace App\Models;

use Exception;
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
        return $this->Allergene() ?? $this->Categorie() ?? $this-> Produit() ?? $this->Page() ?? $this->Typepresence();
    }
    public function Allergene() {
        return $this->hasOne(Allergene::class, "allergene_id")->get()->first();
    }
    public function Categorie() {
        return $this->hasOne(Categorie::class, "categorie_id")->get()->first();
    }
    public function Produit() {
        return $this->hasOne(Produit::class, "produit_id")->get()->first();
    }
    public function Page() {
        return $this->hasOne(Page::class, "page_id")->get()->first();
    }
    public function Typepresence() {
        return $this->hasOne(Typepresence::class, "typepresence_id")->get()->first();
    }

    public function obtenirTraduction(int $langue_id) {
        $traduction = $this->Traduction()->firstWhere('langue_id', $langue_id);
        // Obtenir le traductible traduit en langue $langue_id
        if ($traduction) return $traduction;
        // else TODO: Appeller l'API de traduction et créer la traduction ici
        // Si pas allez de crédits, on passe à la suite aka autre langue
        // S'il n'existe pas, fallback en anglais
        if ($langue_id >= 2) return $this->obtenirTraduction(1);
        // S'il n'existe toujours pas, chercher en français
        if ($langue_id == 1) return $this->obtenirTraduction(0);
        // Si rien n'existe, texte d'erreur par défaut
        if ($langue_id == 0) return "ERREUR : Traduction de traductible n°$this->traductible_id";
        else throw new Exception("How did we get here ? \$langue_id is $langue_id..."); // unreachable
    }
}