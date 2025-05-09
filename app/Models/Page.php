<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $table = "page";
    protected $primaryKey = 'page_id';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'page_id',
        'page_ordre',
        'page_route'
    ];

    public function Traductible() {
        return $this->hasOne(Traductible::class, "traductible_id", "page_id")->get()->first();
    }
    public function Conteneur() {
        return $this->hasMany(Conteneur::class, "page_id", "page_id")->get();
    }

    public function obtenirTraduction(int $langue_id) {
        $traductible = $this->Traductible();
        if (!$traductible) throw new Exception("Page has no Traductible ?");
        $traduction = $traductible->Traduction()->firstWhere('langue_id', $langue_id);
        // Obtenir la page voulue en langue $langue_id
        if ($traduction) return $traduction->traduction_libelle;
        // TODO: Appeller l'API de traduction et créer le texte ici
        // Si elle n'existe pas, fallback en anglais
        if ($langue_id >= 2) return $this->obtenirTraduction(1);
        // Si elle n'existe toujours pas, chercher en français
        if ($langue_id == 1) return $this->obtenirTraduction(0);
        // Si rien n'existe, texte d'erreur par défaut
        if ($langue_id == 0) return "ERREUR : Traduction de page n°$this->page_id";
        else throw new Exception("How did we get here ? \$langue_id is $langue_id..."); // unreachable
    }
}