<?php

namespace App\Http\Controllers;

use App\Models\Fermeture;
use App\Models\Horaire;
use App\Models\Planning;
use App\Models\Reservation;
use DateInterval;
use DateTime;
use Exception;

class PlanningController extends Controller
{
    public static function changePlanning(DateTime $start, DateTime $end, $function = null) {
        // Par défaut, retire le planning entre $start et $end
        if ($function == null) $function = fn($x) => 0;
        $start_str = $start->format("Y-m-d H:i:s") ; $end_str = $end->format("Y-m-d H:i:s");
        // On s'assure qu'il n'y ait pas de milisecondes
        $start = DateTime::createFromFormat("Y-m-d H:i:s", $start_str);
        $end = DateTime::createFromFormat("Y-m-d H:i:s", $end_str);
        // $start doit être plus tôt que $end
        if ($start >= $end) { throw new Exception("Start $start_str must be earlier than end $end_str."); }
        
        // Si le début est avant $end ET la fin est après $start
        // Donc cherche tous les plannings qui sont partiellement dans l'interval,
        // même si ils ne sont pas entièrement contenus dedans
        $plannings = Planning::where("planning_debut", "<", $end_str)
            ->where("planning_fin", ">", $start_str)
            ->orderBy('planning_debut')->get();

        $first = $plannings->first();
        // Si le premier élément est au travers de $start, il faut le séparer en 2
        if ($first && DateTime::createFromFormat("Y-m-d H:i:s", $first->planning_debut) < $start) {
            if ($first->planning_couverts != $function($first->planning_couverts)) {
                // On créer le nouveau planning PUIS maj l'ancien PUIS ajoute le nouveau
                // pour éviter les contraintes de PK sur planning_debut
                $new = new Planning([
                    "planning_debut" => $first->planning_debut,
                    "planning_fin" => $start_str,
                    "planning_couverts" => $first->planning_couverts
                ]);
                // Notez que $new ne sera pas ajouté à $plannings, ce qui est bien
                $first->planning_debut = $start_str;
                $first->save(); $new->save();
            }
        }

        $last = $plannings->last();
        // Si le dernier élément est au travers de $end, il faut le séparer en 2
        if ($last && DateTime::createFromFormat("Y-m-d H:i:s", $last->planning_fin) > $end) {
            if ($last->planning_couverts != $function($last->planning_couverts)) {
                // On créer le nouveau planning PUIS maj l'ancien PUIS ajoute le nouveau
                // pour éviter les contraintes de PK sur planning_debut
                $new = Planning::create([
                    "planning_debut" => $end_str,
                    "planning_fin" => $last->planning_fin,
                    "planning_couverts" => $last->planning_couverts
                ]);
                // Notez que $new ne sera pas ajouté à $plannings, ce qui est bien
                $last->planning_fin = $end_str;
                $last->save(); $new->save();
            }
        }

        // Ici, on cherche à remplir l'espace au début et à la fin si défaut > 0
        $default = $function(0);
        if ($default > 0) {
            if ($plannings->count() == 0) {
                // Si il n'y a pas d'éléments dans $plannings,
                // on créer juste un élément qui va de $start à $end
                Planning::create([
                    "planning_debut" => $start_str,
                    "planning_fin" => $end_str,
                    "planning_couverts" => $default
                ]);
                return;
            }
            // Si le premier élément ne commence pas au début,
            // on ajoute un élément juste avant, qui ne fera pas partie de $planning
            if (DateTime::createFromFormat("Y-m-d H:i:s", $plannings[0]->planning_debut) > $start) {
                Planning::create([
                    "planning_debut" => $start_str,
                    "planning_fin" => $plannings[0]->planning_debut,
                    "planning_couverts" => $default
                ]);
            }
            // Si le dernier élément ne va pas jusqu'à la fin,
            // on ajoute un élément juste après, qui ne fera pas partie de $planning
            $l = $plannings->count()-1;
            if (DateTime::createFromFormat("Y-m-d H:i:s", $plannings[$l]->planning_fin) < $end) {
                Planning::create([
                    "planning_debut" =>  $plannings[$l]->planning_fin,
                    "planning_fin" => $end_str,
                    "planning_couverts" => $default
                ]);
            }
        }

        // Pour chaque planning, on applique la fonction $func pour modifier planning_couverts
        for ($i = 0; $i < $plannings->count(); $i ++) {
            $plannings[$i]->planning_couverts = $function($plannings[$i]->planning_couverts);
            $plannings[$i]->save();
            // Si on n'est pas au dernier index et que défaut > 0,
            // il ne doit pas y avoir de vide entre deux éléments
            if ($i < $plannings->count()-1 && $default > 0 &&
                    $plannings[$i]->planning_fin != $plannings[$i+1]->planning_debut) {
                // Donc si les deux éléments ne sont pas consécutifs,
                // on en créer un 3e pour remplir l'espace
                Planning::create([
                    "planning_debut" => $plannings[$i]->planning_fin,
                    "planning_fin" => $plannings[$i+1]->planning_debut,
                    "planning_couverts" => $default
                ]);
            }
        }
    }

    public static function nettoiePlanning() {
        // On vide la BdD de plages vides ou négatives
        // Les plages vides sont retirées pour économiser de l'espace au coût de la logique,
        // les négatifs ne sont pas interdits par les CHECK pour autoriser les éléments temporaires
        Planning::where("planning_couverts", "<=", 0)->delete();
        // On veut ensuite fusionner les plannings consécutifs ayant le même nombre de couverts disponibles
        $plannings = Planning::orderBy("planning_debut")->get(); $cursor = 0;
        // Pour cela, on compare n et n+1 sur début/fin et couverts
        while ($cursor < $plannings->count()-1) {
            if ($plannings[$cursor]->planning_couverts == $plannings[$cursor+1]->planning_couverts &&
                    $plannings[$cursor]->planning_fin == $plannings[$cursor+1]->planning_debut) {
                // S'ils sont égaux et consécutifs, on étend le 1er et supprime le 2nd
                $plannings[$cursor]->planning_fin = $plannings[$cursor+1]->planning_fin;
                $plannings[$cursor+1]->delete();
                $plannings[$cursor]->save();
                // Et il faut mettre à jour la liste des plannings
                $plannings = Planning::orderBy("planning_debut")->get();
            }
            else $cursor += 1;
        }
    }

    public static function generePlanning(DateTime $start, DateTime $end) {
        $start_str = $start->format("Y-m-d H:i:s") ; $end_str = $end->format("Y-m-d H:i:s");
        // On s'assure qu'il n'y ait pas de milisecondes
        $start = DateTime::createFromFormat("Y-m-d H:i:s", $start_str);
        $end = DateTime::createFromFormat("Y-m-d H:i:s", $end_str);
        // $start doit être plus tôt que $end
        if ($start >= $end) { throw new Exception("Start $start_str must be earlier than end $end_str."); }

        // Comme $func => 0 par défaut, ceci écrase la plage de planning dans l'interval
        PlanningController::changePlanning($start, $end);
        PlanningController::nettoiePlanning();

        // On obtiens les horaires et les fermetures utiles à notre planning
        $horaires = Horaire::orderBy("horaire_couverts")->get();
        $fermetures = Fermeture::where("fermeture_debut", "<", $end_str)
            ->where("fermeture_fin", ">", $start_str)
            ->orderBy("fermeture_couverts", "desc")->get();

        // On itère dans l'interval de temps, jour par jour
        $date = clone $start;
        while ($date <= $end) {
            $jsem = ((int) $date->format('N'))-1; // Jour de la semaine, de 0 à 6
            $jour = (int) $date->format('j'); // Jour dans le mois, de 1 à 31
            $mois = ((int) $date->format('n'))-1; // Mois dans l'année, de 0 à 11

            foreach ($horaires as $horaire) { // Chaque horaire qui...
                // ...est dans horaire_date (inclusif)
                if ((int) $horaire->horaire_date_debut > $jour) continue;
                if ((int) $horaire->horaire_date_fin < $jour) continue;
                if (!$horaire->listeJours()[$jsem]) continue; // ...affecte ce jour de la semaine
                if (!$horaire->listeMois()[$mois]) continue; // ...affecte ce mois de l'année

                // Il faut faire attention aux milisecondes si elles existent
                // Et on créer la plage horaire qui utilise la $date avec le temps de l'horaire
                $from = clone $date; $to = clone $date;
                [$h, $m, $s] = explode(':', explode('.', $horaire->horaire_temps_debut)[0]);
                $from->setTime($h, $m, $s);
                [$h, $m, $s] = explode(':', explode('.', $horaire->horaire_temps_fin)[0]);
                $to->setTime($h, $m, $s);

                // Vérification de la plage horaire relative au temps, puis on applique
                if ($from < $start || $to > $end) continue;
                PlanningController::changePlanning($from, $to,
                    fn($x) => (int) $horaire->horaire_couverts);
            }
            // Incrémentation de la $date d'1 jour
            $date->modify('+1 day');
        }

        foreach ($fermetures as $fermeture) {
            // Si la fermeture dépasse l'interval autorisé, on modifie les bornes
            $from = max($start, DateTime::createFromFormat("Y-m-d H:i:s", $fermeture->fermeture_debut));
            $to = min($end, DateTime::createFromFormat("Y-m-d H:i:s", $fermeture->fermeture_fin));
            // Les nombres positifs sont un maximum, les négatifs sont soustraits relativement
            if ($fermeture->fermeture_couverts >= 0) {
                $func = fn($x) => min($x, $fermeture->fermeture_couverts); }
            else $func = fn($x) => $x + $fermeture->fermeture_couverts;
            PlanningController::changePlanning($from, $to, $func); // On applique
        }

        PlanningController::nettoiePlanning();
    }

    public static function comptePlacesPrises(DateTime $datetime, DateInterval $duree) {
        // Retourne le nombre de place prises par les réservations à un moment donné,
        // sachant que une réservation bloque pendant $duree
        return Reservation::whereBetween("reservation_horaire", [$datetime->format("Y-m-d H:i:s"),
            date_add(clone $datetime, $duree)->format("Y-m-d H:i:s")])->sum("reservation_personnes");
    }

    public static function tableau(DateTime $start, DateTime $end,
            DateInterval $interval, bool $truncate = false) {
        $start_str = $start->format("Y-m-d H:i:s") ; $end_str = $end->format("Y-m-d H:i:s");
        // $start doit être plus tôt que $end
        if ($start >= $end) { throw new Exception("Start $start_str must be earlier than end $end_str."); }

        $array = []; // Initialiser la liste à créer
        // On itère entre $start et $end, créant une entrée dans la liste tous les $interval
        for ($now = clone $start ; $now <= $end ; $now = date_add($now, $interval)) {
            // Obtenir le planning qui gère $now
            $planning = Planning::where("planning_debut", "<=", $now->format("Y-m-d H:i:s"))
                ->where("planning_fin", ">", $now->format("Y-m-d H:i:s"))->get()->first();
            // S'il est nul (n'existe pas), celà veut dire que le restaurant est fermé (0)
            $max = $planning ? $planning->planning_couverts : 0;
            // Calculer le nombres de places réservées à l'instant $now
            // TODO: Constante éditable pour la durée d'une réservation
            $res = PlanningController::comptePlacesPrises($now, new DateInterval("PT2H"));
            // Enregistrer les données et les ajouter à la liste
            // Notez l'utilisation de 'clone' pour séparer l'objet de son instance
            $array[] = ["dt" => clone $now, "max" => $max, "res" => $res, "free" => $max - $res];
        }

        // Pas besoins de tronquer ; on rend le résultat
        if (!$truncate) return $array;

        // Supprimer les x,0,0,x au début
        while (count($array) && $array[0]['max'] == 0 &&
            $array[0]['res'] == 0) array_shift($array);

        // Supprimer les x,0,0,x à la fin
        while (count($array) && end($array)['max'] == 0 &&
            end($array)['res'] == 0) array_pop($array);

        return $array;
    }
}
