<?php

namespace App\Http\Controllers;

use App\Models\Constante;
use App\Models\Historique;
use DateTime;
use DB;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Log;
use Collator;

class CompteController extends Controller
{
    public static array $ALL_PAGES = [
        'LOGIN' => [ // Connexion
            "Accueil" =>"dashboard",
            "Aide" => "help",
            "Compte" => "account"],
        'SUPER' => [ // Superutilisateur
            "Console" => "console"],
        'ADMIN' => [ // Administrateur
            "Constantes" => "constants",
            "Horaires" => "horaires"],
        'MANAG' => [ // Management
            "Personnel" => "management"],
        'COMMU' => [ // Communication
            "Édition" => "edit"],
        'ACTUS' => [ // Actualités
            "Événements" => "events"],
        'PHOTO' => [ // Illustrateur
            "Galerie" => "gallery"],
        'RESTO' => [ // Restauration
            "Menu" => "menu",
            "Produits" => "products"],
        'ALLER' => [ // Allergies
            "Produits" => "products"],
        'RESER' => [ // Reservation
            "Réservations" => "reservations"],
        'SERVE' => [ // Service
            "Réservations" => "reservations"],
        'HISTO' => [ // Historique
            "Historique" => "logs"],
        'PERSO' => [ // Individuel
            "Compte" => "account"]
    ];

    public static function changeMdp(Request $request) {
        $personnel = AuthController::current();
        // Doit être accessible uniquement si requis,
        // car ce changement de mot de passe ne demande pas l'ancien
        if ($personnel === null || $personnel->personnel_mdp_change !== null)
            return redirect()->route('admin')->withErrors(["503" => "Forbidden"]);

        $request->validate([
            'password1' => ['required', 'string', 'max:64'],
            'password2' => ['required', 'string', 'max:64'],
        ]);
        
        // Vérifier que les deux sont égaux
        if ($request->password1 != $request->password2)
            return view('Private.Pages.mdp', ["error" => "Unequal"]);

        // Vérifier l'intégrité du mot de passe
        $result = CompteController::checkMdp($request->password1, true);
        if (in_array(false, $result, true)) {
            $classes = array_map(fn($x) => $x ? '' : 'red', array_values($result));
            return view('Private.Pages.mdp', ["classes" => $classes]);
        }

        // On ne peut pas utiliser le même mot de passe deux fois d'affilé
        if (Hash::check($request->password1, rtrim($personnel->personnel_mdp)))
            return view('Private.Pages.mdp', ["error" => "Unique"]);

        DB::beginTransaction();
        try {
            $personnel->personnel_mdp = Hash::make($request->password1);
            $personnel->personnel_mdp_change = (new DateTime())->format("Y-m-d H:i:s");
            $personnel->save();
            Historique::create([
                "personnel_id" => (int)$personnel->personnel_id,
                "historique_message" => "Mot de passe changé"
            ]);
        }
        catch (Exception $e) {
            DB::rollBack();
            Log::error("Unnexpected error in CompteController::changeMdp() : ", [$e]);
            view('Private.Pages.mdp', ["error" => "SQL"]);

        }
        DB::commit();
        return redirect()->route('admin');
    }

    public static function checkMdp(string $mdp, bool $return_array = false) {
        // Récupération des critères de mot de passe depuis la constante
        [$nb_chars, $nb_majs, $nb_mins, $nb_nums, $nb_spe] = Constante::key('mdp_critères');
        preg_match_all('/[A-Z]/', $mdp, $majs); // Nombre de majuscules
        preg_match_all('/[a-z]/', $mdp, $mins); // Nombre de minuscules
        preg_match_all('/[0-9]/', $mdp, $nums); // Nombre de chiffres
        preg_match_all('/[^a-zA-Z0-9]/', $mdp, $spe); // Nombre de caractères spéciaux
        $array = [
            "CHA" => (strlen($mdp) >= $nb_chars),
            "MAJ" => (count($majs[0]) >= $nb_majs),
            "MIN" => (count($mins[0]) >= $nb_mins),
            "NUM" => (count($nums[0]) >= $nb_nums),
            "SPE" => (count($spe[0]) >= $nb_spe),
        ];
        // Renvoi la liste ou son produit logique
        return $return_array ? $array : !in_array(false, $array, true);
    }

    public static function pagesAdmin() {
        if (!AuthController::current()) return [];
        $routes = [];
        // On itère à travers les pages
        foreach (CompteController::$ALL_PAGES as $key => $value) {
            // Si l'utilisateur a la permission pour
            if (AuthController::requirePerm($key)) {
                // On ajoutes les routes dans $values [nom => route,...]
                $routes = array_merge($routes, $value);
            }
        }
        // On utilise Collator (de php-intl) car on veut que la comparaison
        // par ordre alphabétique utilise la langue française (pour les accents)
        $collator = new Collator("fr_FR");
        uksort($routes, fn($a, $b) => $collator->compare($a, $b));
        
        foreach ($routes as $key => $value) {
            // Si la route n'existe pas (encore), on la met à false
            if (!Route::has('admin.'.$value)) $routes[$key] = false;
            // Sinon, le string est la route complète (pour lien <a>)
            else $routes[$key] = route('admin.'.$value);
        }
        return $routes;
    }
}
