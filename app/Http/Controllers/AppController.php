<?php

namespace App\Http\Controllers;

use App\Models\Langue;
use App\Models\Page;

class AppController extends Controller
{
    public static function obtenirPagesTraduites() {
        $langue = session('locale', Langue::find(0)); // Langue active sur le site
        $pages = Page::where('page_id', '>', 0)->orderBy('page_ordre')->get(); // Obtenir les pages
        // Obtenir les traductions depuis Traductible->Traduction->traduction_libelle
        foreach ($pages as $page) {
            $page->page_traduction_libelle = $page->obtenirTraduction($langue->langue_id);
        }
        return $pages;
    }
}
