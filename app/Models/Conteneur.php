<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use PhpOption\None;

class Conteneur extends Model
{
    protected $table = "conteneur";
    protected $primaryKey = 'conteneur_id';
    public $timestamps = false;
    protected $fillable = [
        'conteneur_libelle',
        'evenement_id',
        'page_id',
        'police_id',
        'conteneur_texte',
        'conteneur_centre',
        'conteneur_fond',
        'conteneur_hex',
        'conteneur_ligne',
        'conteneur_colonne'
    ];

    public function Page() {
        return $this->hasOne(Page::class, "page_id")->get()->first();
    }
    public function Evenement() {
        return $this->hasOne(Evenement::class, "evenement_id")->get()->first();
    }
    public function Police() {
        return $this->hasOne(Police::class, "police_id")->get()->first();
    }
    public function Galerie() {
        return $this->hasMany(Galerie::class, "conteneur_id", "conteneur_id")->get();
    }
    public function Contenu() {
        return $this->hasMany(Contenu::class, "conteneur_id", "conteneur_id")->get();
    }

    public function obtenirContenuTraduit(int $langue_id) {
        if (!$this->conteneur_texte) return Null;
        $contenu = $this->Contenu()->firstWhere('langue_id', '=', $langue_id);
        // Obtenir le contenu du texte en langue $langue_id
        if ($contenu) return $contenu->contenu_texte;
        // TODO: Appeller l'API de traduction et créer le texte ici
        // S'il n'existe pas, fallback en anglais
        if ($langue_id >= 2) return $this->obtenirContenuTraduit(1);
        // S'il n'existe toujours pas, chercher en français
        if ($langue_id == 1) return $this->obtenirContenuTraduit(0);
        // Si rien n'existe, texte d'erreur par défaut
        if ($langue_id == 0) return "ERREUR : Traduction de conteneur n°$this->conteneur_id";
        else throw new Exception("How did we get here ? \$langue_id is $langue_id..."); // unreachable
    }
}