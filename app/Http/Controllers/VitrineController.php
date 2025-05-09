<?php

namespace App\Http\Controllers;

use App\Models\Conteneur;
use Illuminate\Http\Request;

class VitrineController extends Controller
{
    public static function index(int $page_id)
    {
        $langue = session('locale', 0); // Langue active sur le site
        // Obtenir les conteneurs relatifs à cette page, un par ligne
        $conteneurs = Conteneur::where('page_id', $page_id)->where('conteneur_visible', true)
        ->distinct('conteneur_ligne')->orderBy('conteneur_ligne')->get();
        // Récupérer pour chaque ligne une collection par colonne
        $lignes = array();
        foreach ($conteneurs as $ligne) {
            $local = Conteneur::where('page_id', $page_id)->where('conteneur_visible', true)
                ->where('conteneur_ligne', $ligne->conteneur_ligne)
                ->orderBy('conteneur_colonne')->get();
            // Obtenir les traductions depuis Contenu->contenu_texte
            foreach ($local as $conteneur) {
                $conteneur->conteneur_contenu_texte = $conteneur->obtenirContenuTraduit($langue);
                $conteneur->conteneur_photo_url = $conteneur->Photo() ? $conteneur->Photo()->photo_url : null;
                $conteneur->conteneur_police_texte = $conteneur->Police() ? $conteneur->Police()->police_texte : null;
            }
            array_push($lignes, $local);
        }
        // Insérer les conteneurs dans la vitrine
        return view("Pages.vitrine", [
            'lignes' => $lignes
        ]);
    }
}
