<?php

namespace App\Http\Controllers;

use App\Models\Conteneur;
use App\Models\Langue;
use App\Models\Page;

class VitrineController extends Controller
{
    public static function index(int $page_id)
    {
        $langue = session('locale', Langue::find(0)); // Langue active sur le site
        // Obtenir les conteneurs relatifs à cette page, un par ligne
        $conteneurs = Conteneur::where('page_id', $page_id)->where('conteneur_visible', true)
        ->distinct('conteneur_ligne')->orderBy('conteneur_ligne')->get();
        // Récupérer pour chaque ligne une collection par colonne
        $lignes = array(); $textes = array();
        foreach ($conteneurs as $ligne) {
            $local = Conteneur::where('page_id', $page_id)->where('conteneur_visible', true)
                ->where('conteneur_ligne', $ligne->conteneur_ligne)
                ->orderBy('conteneur_colonne')->get();
            // Obtenir les traductions depuis Contenu->contenu_texte
            foreach ($local as $conteneur) {
                $textes[strval($conteneur->conteneur_id)] = VitrineController::remplaceFormulaire(
                    $conteneur->obtenirContenuTraduit($langue->langue_id));
                // On place aussi les valeurs de photo et police si présentes
                $conteneur->conteneur_photo_url = $conteneur->Photo() ? $conteneur->Photo()->photo_url : null;
                $conteneur->conteneur_police_texte = $conteneur->Police() ? $conteneur->Police()->police_texte : null;
            }
            array_push($lignes, $local);
        }
        // Insérer les variables dans la vitrine
        return view("Pages.vitrine", [
            'lignes' => $lignes,
            'textes' => $textes,
            'page' => Page::find($page_id)
        ]);
    }

    public static function remplaceFormulaire(string $txt) {
        foreach ([ // Rechercher et remplacer avec [cible => résultat]
            "<form#reservation>" => "<form method='POST' action='".route('api.book').
                "'><input type='hidden' name='_token' value='".csrf_token()."'/>",
            "<button#submit>" => "<button type='submit' disabled ".
            "onmouseenter='submit_enter(this)' onmouseleave='submit_leave()'>",
            
            "<g-recaptcha#reservation.white/>" => "<div class='g-recaptcha' data-sitekey='".
                env('RECAPTCHA_SITE_KEY')."' data-theme='light' ".
                "data-callback='callback' data-expired-callback='expire'></div>",
            "<g-recaptcha#reservation.dark/>" => "<div class='g-recaptcha' data-sitekey='".
                env('RECAPTCHA_SITE_KEY')."' data-theme='dark' ".
                "data-callback='callback' data-expired-callback='expire'></div>",
        ] as $key => $value) { // On itère et on remplace tout
            $txt = str_replace($key, $value, $txt);
        }
        return $txt;
    }
}
