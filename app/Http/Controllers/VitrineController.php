<?php

namespace App\Http\Controllers;

use App\Models\Categorie;
use App\Models\Categorise;
use App\Models\Conteneur;
use App\Models\Langue;
use App\Models\Page;
use App\Models\Reservation;

class VitrineController extends Controller
{
    public static int $MAX_LEVEL = 3;

    public static function obtenirPagesTraduites() {
        $langue = session('locale', Langue::find(0)); // Langue active sur le site
        $pages = Page::where('page_id', '>', 0)->orderBy('page_ordre')->get(); // Obtenir les pages
        // Obtenir les traductions depuis Traductible->Traduction->traduction_libelle
        foreach ($pages as $page) {
            $page->page_traduction_libelle = $page->Traductible()->obtenirTraduction($langue->langue_id)->traduction_libelle;
        }
        return $pages;
    }

    public static function index(int $page_id = 0, int $langue_id = null,
        Reservation $reservation = null, bool $public = true)
    {
        if ($langue_id === null) $langue_id = session('locale', Langue::find(0))->langue_id;
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
                $textes[strval($conteneur->conteneur_id)] = $conteneur->obtenirContenuTraduit($langue_id);
                // On place aussi les valeurs de photo et police si présentes
                $conteneur->conteneur_photo_url = $conteneur->Photo()?->photo_url;
                $conteneur->conteneur_police_texte = $conteneur->Police()?->police_texte;
            }
            array_push($lignes, $local);
        }
        // Insérer les variables dans la vitrine
        $script = array_search($page_id, [0, 6]) !== false;
        return view("Public.Pages.vitrine", [
            'lignes' => $lignes, 'textes' => $textes, 'script' => $script,
            'reservation' => $reservation, 'public' => $public
        ]);
    }

    public static function menu($id, $level = 1) {
        $category = Categorie::find($id);
        if ($category === null) return;
        if ($level > VitrineController::$MAX_LEVEL) $level = VitrineController::$MAX_LEVEL;

        $langue_id = session('locale', Langue::find(0))->langue_id;
        $traduction = $category->Traductible()->obtenirTraduction($langue_id);
        echo "<p class='menu-category menu-category-$level'>".$traduction->traduction_libelle."</p>";
        if ($traduction->traduction_description) {
            echo "<p class='menu-description menu-description-$level'>".$traduction->traduction_description."</p>";
        }

        $items = Categorise::where('categorie_id', $id)->orderBy('categorise_ordre')->get();
        echo "<table>";
        foreach ($items as $item) {
            $produit = $item->Produit()->Traductible()->obtenirTraduction($langue_id);
            echo "<tr><td class='menu-item'><p class='menu-name'>".$produit->traduction_libelle."</p>";
            if ($produit->traduction_description) {
                echo "<p class='menu-detail'>".$produit->traduction_description."</p>";
            }

            $price = $item->categorise_prix;
            $price = (floor($price) == $price) ? (int)$price : number_format($price, 2, ",");
            echo "</td><td class='menu-price'>$price €</td></tr>";
        }
        echo "</table>";

        $children = Categorie::where('categorie_idparent', $id)->orderBy('categorie_ordre')->get();
        foreach ($children as $child) {
            VitrineController::menu($child->categorie_id, $level + 1);
        }
    }
}
