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
                $textes[strval($conteneur->conteneur_id)] = VitrineController::replacer(
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

    public static function replacer(string $text) {
        foreach ([ // Rechercher et remplacer avec [cible => résultat]
            "<googlemap/>" => "<iframe class='gmap' src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d112913.".
                "34543813349!2d6.032387482375051!3d45.95580804090011!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.".
                "1!3m3!1m2!1s0x478b8fe110e3bc4b%3A0x4171e0c94e0c9be8!2sLa%20Romana!5e0!3m2!1sfr!2sfr!4v1747588357514!5m2!1sfr!2sfr' ".
                "allowfullscreen='' loading='lazy' referrerpolicy='no-referrer-when-downgrade' width='100%' height='100%'></iframe>"
        ] as $key => $value) { // On itère et on remplace tout
            $text = str_replace($key, $value, $text);
        }
        return $text;
    }
}
