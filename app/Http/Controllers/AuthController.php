<?php

namespace App\Http\Controllers;

use App\Models\Constante;
use App\Models\Historique;
use App\Models\Permission;
use App\Models\Personnel;
use App\Models\Typepermission;
use DateTime;
use Exception;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    public static function current() {
        $id = Session::get("auth"); // Obtenir l'id
        if ($id == null) return null; // Si innexistante
        // On récupère le personnel correspondant
        $personnel = Personnel::where('personnel_id', $id)->first();
        // Si l'id ne correspond pas, on réinitialise la valeur 'auth'
        if ($personnel === null) AuthController::logout();
        return $personnel;
    }

    public static function auth(string $login, string $mdp) {
        // Trouver le personnel correspondant (insensible à la casse)
        $personnel = Personnel::where("personnel_nom", "ilike", $login)->first();
        if ($personnel == null) return null; // Si pas trouvé, retour null
        // Vérification du mot de passe (bcrypt) (personnel_mdp est CHAR donc rtrim())
        if (!Hash::check($mdp, rtrim($personnel->personnel_mdp))) return null;
        // On vérifie que l'utilisateur a bien la permission de se connecter
        if (!AuthController::requirePerm("LOGIN", $personnel)) return null;

        // On met son id dans le 'auth' de session
        Session::put("auth", (int)$personnel->personnel_id);
        // Ajouter la connexion dans l'historique
        Historique::create([
            "personnel_id" => (int)$personnel->personnel_id,
            "historique_message" => "Connexion"
        ]);

        if ($personnel->personnel_mdp_change === null) return $personnel;
        // On vérifie que le mot de passe est toujours valide
        // A la fois dans le temps et selon les critères constants
        $last_change = DateTime::createFromFormat("Y-m-d H:i:s", $personnel->personnel_mdp_change);
        $expires_on = date_add($last_change, Constante::interval('mdp_expiration'));
        if ($expires_on < new DateTime() || !CompteController::checkMdp($mdp)) {
            // Sinon, on indique qu'il doit être changé
            $personnel->personnel_mdp_change = null;
            $personnel->save();
            Historique::create([
                "personnel_id" => (int)$personnel->personnel_id,
                "historique_message" => "Mot de passe expiré/invalide"
            ]);
        }
        return $personnel;
    }

    public static function login(Request $request) {
        $request->validate([ // Valider les champs de formulaire
            'login' => ['required', 'string', 'max:64'],
            'mdp' => ['required', 'string', 'max:64']
        ]);
        // Tentative d'authentification
        $personnel = AuthController::auth($request->login, $request->mdp);
        // En cas d'échec, on revoie en arrière avec une erreur
        if ($personnel === null) return redirect()->back()
            ->withErrors(["503" => "Forbidden"]);
        return redirect()->route('admin');
    }

    public static function logout() {
        if ($p = AuthController::current()) {
            // Déconnexion manuelle dans l'historique
            Historique::create([
                "personnel_id" => (int)$p->personnel_id,
                "historique_message" => "Déconnexion"
            ]);
        }
        Session::forget("auth"); // Oublier l'id
    }

    public static function requirePerm(
            string|int|array|null $permission,
            Personnel $personnel = null) {
        if ($permission === null) return true;
        if ($personnel === null) $personnel = AuthController::current();
        if ($personnel === null) return false;

        // Itération à travers les permissions si c'est une liste
        if (is_array($permission)) {
            // Superutilisateur => Tous les droits
            if (AuthController::requirePerm(1, $personnel)) return true;
            foreach ($permission as $perm) {
                if (AuthController::requirePerm($perm, $personnel)) return true;
            }
            return false;
        }

        // Superutilisateur => Tous les droits
        if ($permission !== 1 && AuthController::requirePerm(1, $personnel)) return true;
        
        // Conversion de la permission en son id
        if (is_string($permission)) {
            if (strlen($permission) == 5) { // Par sa clef...
                $permission = strtoupper($permission);
                $column = "typepermission_clef";
            }
            else { // ...ou son libellé
                $permission = ucfirst(strtolower($permission));
                $column = "typepermission_libelle";
            }
            // $perm est maintenant un id de typepermission
            $perm_id = Typepermission::where($column, $permission)->first();
            if ($perm_id === null) throw new Exception("Permission $permission not found");
            $perm_id = $perm_id->typepermission_id;
        }
        else $perm_id = $permission;
        // Trouver l'instance de permission(personnel_id,typepermission_id) correspondante
        return Permission::where("typepermission_id", $perm_id)
            ->where("personnel_id", $personnel->personnel_id)->exists();
    }
}
