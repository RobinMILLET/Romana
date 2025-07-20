<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;

class Conteneur extends Model
{
    protected $table = "conteneur";
    protected $primaryKey = 'conteneur_id';
    public $timestamps = false;
    protected $fillable = [
        'conteneur_libelle',
        'evenement_id',
        'page_id',
        'photo_id',
        'police_id',
        'conteneur_texte',
        'conteneur_ligne',
        'conteneur_colonne',
        'conteneur_aligne',
        'conteneur_bordure',
        'conteneur_couleur',
        'conteneur_fond',
        'conteneur_largeur',
        'conteneur_marges',
        'conteneur_ombre',
        'conteneur_rayon',
        'conteneur_visible'
    ];

    public function Page() {
        return $this->hasOne(Page::class, "page_id", "page_id")->get()->first();
    }
    public function Evenement() {
        return $this->hasOne(Evenement::class, "evenement_id", "evenement_id")->get()->first();
    }
    public function Police() {
        return $this->hasOne(Police::class, "police_id", "police_id")->get()->first();
    }
    public function Photo() {
        return $this->hasOne(Photo::class, "photo_id", "photo_id")->get()->first();
    }
    public function Contenu() {
        return $this->hasMany(Contenu::class, "conteneur_id", "conteneur_id")->get();
    }

    public function obtenirContenuTraduit(int $langue_id) {
        if (!$this->conteneur_texte) return "";
        // Tester si c'est un formulaire
        $form = $this->formulaire($langue_id);
        if ($form) {
            // On teste si la vue existe
            if (view()->exists($form)) return $form;
            // else TODO: Appeller l'API de traduction et créer le formulaire ici
            // Si pas allez de crédits, on passe à la suite aka autre langue
        }
        else {
            $contenu = $this->Contenu()->firstWhere('langue_id', $langue_id);
            // Obtenir le contenu du texte en langue $langue_id
            if ($contenu) return $contenu->contenu_texte;
            // else TODO: Appeller l'API de traduction et créer le texte ici
            // Si pas allez de crédits, on passe à la suite aka autre langue
        }
        // S'il n'existe pas, fallback en anglais
        if ($langue_id >= 2) return $this->obtenirContenuTraduit(1);
        // S'il n'existe toujours pas, chercher en français
        if ($langue_id == 1) return $this->obtenirContenuTraduit(0);
        // Si rien n'existe, texte d'erreur par défaut
        if ($langue_id == 0) return "ERREUR : Traduction de conteneur n°$this->conteneur_id";
        else throw new Exception("How did we get here ? \$langue_id is $langue_id..."); // unreachable
    }

    public function formulaire($langue_id){
        // Si ne ressemble pas à '<form#___>', on abandonne
        if (!preg_match("/^<form#[a-z0-9]+_?>$/", $this->conteneur_texte)) return null;
        // On extrait le nom de la balise
        $nom = substr($this->conteneur_texte, 6, strlen($this->conteneur_texte)-7);
        // Si le nom ne finit pas par '_', il n'attend pas de code de langue
        if (!str_ends_with($nom, "_")) return "Public.Forms.".$nom;
        $code = Langue::find($langue_id)->langue_code; // Le code de langue
        return "Public.Forms.".$nom.$code; // Et on renvoie le nom (théorique) de la vue
    }
}