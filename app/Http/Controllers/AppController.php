<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;

class AppController extends Controller
{
    public static function obtenirPagesTraduites() {
        $langue = session('locale', 0); // Langue active sur le site
        $pages = Page::orderBy('page_ordre')->get(); // Obtenir les pages
        // Obtenir les traductions depuis Traductible->Traduction->traduction_libelle
        foreach ($pages as $page) {
            $page->page_libelle = $page->obtenirTraduction($langue);
        }
        return $pages;
    }
}
